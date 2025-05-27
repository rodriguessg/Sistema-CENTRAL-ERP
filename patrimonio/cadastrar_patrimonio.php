<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

$host = 'localhost';
$dbname = 'gm_sicbd';
$username_db = 'root';
$password = '';

try {
    // Criação da conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Definir modo de erro para exceção
} catch (PDOException $e) {
    // Em caso de erro na conexão, loga o erro e exibe uma mensagem amigável
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Consulte o administrador.");
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter e validar os dados do formulário
    $nome = $_POST['nome'] ?? null;
    $descricao = $_POST['descricao'] ?? null;
    $valor = $_POST['valor'] ?? null;
    $data_aquisicao = $_POST['data_aquisicao'] ?? null;
    $situacao = $_POST['situacao'] ?? null;
    $localizacao = $_POST['localizacao'] ?? null;
    $categoria = $_POST['categoria'] ?? null;

    if (!$nome || !$descricao || !$valor || !$data_aquisicao || !$situacao || !$localizacao || !$categoria) {
        die("Erro: Todos os campos são obrigatórios!");
    }

    // Mapear código base para cada categoria
    $codigoBase = match ($categoria) {
        'equipamentos_informatica' => 600428000012477,
        'bens_achados' => 705100000000196,
        'moveis_utensilios' => 450518000002335,
        'reserva_bens_moveis' => 460000000000000,
        'bens_com_baixo_valor' => 1,
        default => die("Erro: Categoria inválida!"),
    };

    // Buscar o último código da categoria
    $query_codigo = "SELECT MAX(codigo) AS ultimo_codigo FROM patrimonio WHERE categoria = :categoria";
    $stmt = $pdo->prepare($query_codigo);
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
    $stmt->execute();
    $ultimo_codigo = $stmt->fetch(PDO::FETCH_ASSOC)['ultimo_codigo'] ?? $codigoBase - 1;
    $novo_codigo = $ultimo_codigo + 1;

    // Verificar e processar o upload da foto
    $file = 'default.png';
    if (!empty($_FILES['foto']['name'])) {
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $novo_nome = 'patrimonio-' . uniqid() . '.' . $extensao; // Nome padrão do arquivo
        $diretorio = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

        // Criar o diretório de upload caso não exista
        if (!is_dir($diretorio)) {
            if (!mkdir($diretorio, 0777, true)) {
                die("Erro ao criar o diretório de uploads.");
            }
        }

        // Validar extensão do arquivo
        if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) {
            $caminho_arquivo = $diretorio . $novo_nome; // Caminho completo para salvar o arquivo
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_arquivo)) {
                $file = $novo_nome; // Nome do arquivo salvo no banco
            } else {
                die("Erro ao fazer upload do arquivo.");
            }
        } else {
            die("Tipo de arquivo inválido. Permitido: JPG, JPEG, PNG, GIF.");
        }
    }

    // Inserir o patrimônio no banco de dados
    $query = "INSERT INTO patrimonio (nome, descricao, valor, data_aquisicao, situacao, localizacao, codigo, categoria, cadastrado_por, foto) 
              VALUES (:nome, :descricao, :valor, :data_aquisicao, :situacao, :localizacao, :codigo, :categoria, :cadastrado_por, :foto)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
    $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
    $stmt->bindParam(':data_aquisicao', $data_aquisicao, PDO::PARAM_STR);
    $stmt->bindParam(':situacao', $situacao, PDO::PARAM_STR);
    $stmt->bindParam(':localizacao', $localizacao, PDO::PARAM_STR);
    $stmt->bindParam(':codigo', $novo_codigo, PDO::PARAM_INT);
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
    $stmt->bindParam(':cadastrado_por', $username, PDO::PARAM_STR);
    $stmt->bindParam(':foto', $file, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Registrar no log de eventos
        $tipo_operacao = 'Cadastro de Patrimônio';
        $query_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (:matricula, :tipo_operacao, NOW())";
        $stmt_log = $pdo->prepare($query_log);

        if ($stmt_log) {
            $stmt_log->bindParam(':matricula', $username, PDO::PARAM_STR);
            $stmt_log->bindParam(':tipo_operacao', $tipo_operacao, PDO::PARAM_STR);
            $stmt_log->execute();
            $stmt_log = null;
        }

        // Redirecionar para a página de sucesso
        header('Location: mensagem.php?mensagem=sucesso&pagina=homepatrimonio.php');
        exit();
    } else {
        die("Erro ao cadastrar o patrimônio: " . $stmt->errorInfo()[2]);
    }
}

// Fechar a conexão com o banco de dados
$pdo = null;
?>

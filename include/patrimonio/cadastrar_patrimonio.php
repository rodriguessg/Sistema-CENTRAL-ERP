<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

// Incluir o arquivo de conexão com o banco de dados
include 'banco.php';

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
    $query_codigo = "SELECT MAX(codigo) AS ultimo_codigo FROM patrimonio WHERE categoria = ?";
    $stmt = $con->prepare($query_codigo);
    if (!$stmt) {
        die("Erro ao preparar a consulta de código: " . $con->error);
    }

    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    $result = $stmt->get_result();
    $ultimo_codigo = $result->fetch_assoc()['ultimo_codigo'] ?? $codigoBase - 1;
    $novo_codigo = $ultimo_codigo + 1;

    $stmt->close();

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
        // Ajustar o caminho para salvar no banco
        // $foto_caminho = 'uploads/' . $novo_nome;
    }

    // Inserir o patrimônio no banco de dados
    $query = "INSERT INTO patrimonio (nome, descricao, valor, data_aquisicao, situacao, localizacao, codigo, categoria, cadastrado_por, foto) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        die("Erro ao preparar a consulta de inserção: " . $con->error);
    }

    $stmt->bind_param("ssdsssssss", $nome, $descricao, $valor, $data_aquisicao, $situacao, $localizacao, $novo_codigo, $categoria, $username, $file);
    if ($stmt->execute()) {
        // Registrar no log de eventos
        $tipo_operacao = 'Cadastro de Patrimônio';
        $query_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())";
        $stmt_log = $con->prepare($query_log);

        if ($stmt_log) {
            $stmt_log->bind_param("ss", $username, $tipo_operacao);
            $stmt_log->execute();
            $stmt_log->close();
        }

        // Redirecionar para a página de sucesso
        header('Location: mensagem.php?mensagem=sucesso&pagina=homepatrimonio.php');
        exit();
    } else {
        die("Erro ao cadastrar o patrimônio: " . $stmt->error);
    }

    $stmt->close();
}

// Fechar a conexão com o banco de dados
$con->close();
?>

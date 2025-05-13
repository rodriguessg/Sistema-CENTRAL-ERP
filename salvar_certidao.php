<?php
// Iniciar sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');
ini_set('display_errors', 0);

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados.']);
    exit;
}

// Função para gerar notificação imediata após cadastro
function gerarNotificacaoCadastro($pdo, $username, $setor, $certidao_id, $nome, $data_vencimento) {
    try {
        $vencimento = new DateTime($data_vencimento);
        $mensagem = sprintf(
            'Certidão "%s" SScadastrada com sucesso. Vence em %s. Setor: %s.',
            $nome,
            $certidao_id,
            $vencimento->format('d/m/Y'),
            $setor
        );

        $sql = "INSERT INTO notificacoes (username, setor, mensagem, situacao) 
                VALUES (:username, :setor, :mensagem, 'nao lida')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'setor' => $setor,
            'mensagem' => $mensagem
        ]);
        error_log("Notificação de cadastro criada para certidao_id=$certidao_id: $mensagem");
    } catch (Exception $e) {
        error_log("Erro ao gerar notificação de cadastro: " . $e->getMessage());
    }
}

// Função para gerar notificações de vencimento próximo
function gerarNotificacoes($pdo, $username, $setor) {
    try {
        $hoje = new DateTime();
        $limiteNotificacao = (clone $hoje)->modify('+40 days')->format('Y-m-d');

        $sql = "SELECT id, nome, data_vencimento 
                FROM certidoes 
                WHERE data_vencimento <= :limite 
                AND data_vencimento >= :hoje";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'limite' => $limiteNotificacao,
            'hoje' => $hoje->format('Y-m-d')
        ]);
        $certidoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Certidões para notificação: " . json_encode($certidoes));

        foreach ($certidoes as $certidao) {
            $sqlCheck = "SELECT COUNT(*) 
                         FROM notificacoes 
                         WHERE mensagem LIKE :mensagem 
                         AND DATE(data_criacao) = CURDATE() 
                         AND username = :username";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $mensagemLike = '%A certidão "' . $certidao['nome'] . '" (ID: ' . $certidao['id'] . ')%';
            $stmtCheck->execute([
                'mensagem' => $mensagemLike,
                'username' => $username
            ]);
            $existeNotificacao = $stmtCheck->fetchColumn();

            if ($existeNotificacao == 0) {
                $vencimento = new DateTime($certidao['data_vencimento']);
                $intervalo = $hoje->diff($vencimento);
                $diasRestantes = $intervalo->days;

                $mensagem = sprintf(
                    'A certidão "%s"  está próxima do vencimento (%d dias restantes, vence em %s). ',
                    $certidao['nome'],
                    $certidao['id'],
                    $diasRestantes,
                    $vencimento->format('d/m/Y'),
                    $setor
                );

                $sqlInsert = "INSERT INTO notificacoes (username, setor, mensagem, situacao) 
                              VALUES (:username, :setor, :mensagem, 'nao lida')";
                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->execute([
                    'username' => $username,
                    'setor' => $setor,
                    'mensagem' => $mensagem
                ]);
                error_log("Notificação de vencimento criada para certidao_id={$certidao['id']}: $mensagem");
            }
        }
    } catch (Exception $e) {
        error_log("Erro ao gerar notificações: " . $e->getMessage());
    }
}

// Função para gerar eventos 3 dias antes do vencimento
function gerarEventos($pdo) {
    try {
        $hoje = new DateTime();
        $limiteEvento = (clone $hoje)->modify('+5 days')->format('Y-m-d');

        $sql = "SELECT id, nome, data_vencimento 
                FROM certidoes 
                WHERE data_vencimento <= :limite 
                AND data_vencimento >= :hoje";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'limite' => $limiteEvento,
            'hoje' => $hoje->format('Y-m-d')
        ]);
        $certidoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Certidões para eventos: " . json_encode($certidoes));

        foreach ($certidoes as $certidao) {
            $vencimento = new DateTime($certidao['data_vencimento']);
            $dataEvento = (clone $vencimento)->modify('-3 days');
            $dataEventoStr = $dataEvento->format('Y-m-d');

            if ($dataEvento->format('Y-m-d') === $hoje->format('Y-m-d')) {
                $sqlCheck = "SELECT COUNT(*) 
                             FROM eventos 
                             WHERE descricao LIKE :descricao 
                             AND DATE(data) = :data_evento";
                $stmtCheck = $pdo->prepare($sqlCheck);
                $descricaoLike = '%Certidão ID: ' . $certidao['id'] . '%';
                $stmtCheck->execute([
                    'descricao' => $descricaoLike,
                    'data_evento' => $dataEventoStr
                ]);
                $existeEvento = $stmtCheck->fetchColumn();

                if ($existeEvento == 0) {
                    $titulo = "Renovar Certidão: " . $certidao['nome'];
                    $descricao = sprintf(
                        'Certidão ID: %d, vence em %s.',
                        $certidao['id'],
                        $vencimento->format('d/m/Y')
                    );
                    $sqlInsert = "INSERT INTO eventos (titulo, descricao, data, hora, categoria, cor) 
                                  VALUES (:titulo, :descricao, :data, :hora, :categoria, :cor)";
                    $stmtInsert = $pdo->prepare($sqlInsert);
                    $stmtInsert->execute([
                        'titulo' => $titulo,
                        'descricao' => $descricao,
                        'data' => $dataEventoStr,
                        'hora' => '00:00:00',
                        'categoria' => 'Renovação',
                        'cor' => '#dc3545'
                    ]);
                    error_log("Evento criado para certidao_id={$certidao['id']}: $titulo em $dataEventoStr");
                }
            }
        }
    } catch (Exception $e) {
        error_log("Erro ao gerar eventos: " . $e->getMessage());
    }
}

try {
    header('Content-Type: application/json');

    if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
        echo json_encode(['success' => false, 'message' => 'Usuário ou setor não autenticado.']);
        exit;
    }

    if (!isset($_POST['documento'], $_POST['data_vencimento'], $_POST['nome'], $_POST['fornecedor'], $_POST['responsavel'])) {
        echo json_encode(['success' => false, 'message' => 'Campos obrigatórios faltando.']);
        exit;
    }

    $documento = $_POST['documento'];
    $data_vencimento = $_POST['data_vencimento'];
    $nome = $_POST['nome'];
    $fornecedor = $_POST['fornecedor'];
    $responsavel = $_POST['responsavel'];
    $contrato_id = isset($_POST['contrato_id']) && !empty($_POST['contrato_id']) ? (int)$_POST['contrato_id'] : null;
    $arquivo_path = null;

    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['arquivo'];
        $allowed_types = ['application/pdf'];
        $max_size = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Apenas arquivos PDF são permitidos.']);
            exit;
        }

        if ($file['size'] > $max_size) {
            echo json_encode(['success' => false, 'message' => 'O arquivo excede o tamanho máximo de 5MB.']);
            exit;
        }

        $upload_dir = 'Uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = uniqid() . '_' . basename($file['name']);
        $file_path = $upload_dir . $file_name;

        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar o arquivo.']);
            exit;
        }

        $arquivo_path = $file_name;
    }

    $contrato_titulo = null;
    if ($contrato_id) {
        $sql = "SELECT titulo FROM gestao_contratos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $contrato_id]);
        $contrato = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($contrato) {
            $contrato_titulo = $contrato['titulo'];
        } else {
            $contrato_id = null;
        }
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $sql = "UPDATE certidoes 
                SET documento = :documento, data_vencimento = :data_vencimento, 
                    nome = :nome, fornecedor = :fornecedor, responsavel = :responsavel,
                    arquivo = :arquivo, contrato_id = :contrato_id
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'documento' => $documento,
            'data_vencimento' => $data_vencimento,
            'nome' => $nome,
            'fornecedor' => $fornecedor,
            'responsavel' => $responsavel,
            'arquivo' => $arquivo_path,
            'contrato_id' => $contrato_id,
            'id' => (int)$_POST['id']
        ]);
        $id = (int)$_POST['id'];
        $mensagem = 'Certidão atualizada com sucesso!';
    } else {
        $sql = "INSERT INTO certidoes (documento, data_vencimento, nome, fornecedor, responsavel, arquivo, contrato_id) 
                VALUES (:documento, :data_vencimento, :nome, :fornecedor, :responsavel, :arquivo, :contrato_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'documento' => $documento,
            'data_vencimento' => $data_vencimento,
            'nome' => $nome,
            'fornecedor' => $fornecedor,
            'responsavel' => $responsavel,
            'arquivo' => $arquivo_path,
            'contrato_id' => $contrato_id
        ]);
        $id = $pdo->lastInsertId();
        $mensagem = 'Certidão cadastrada com sucesso!';
    }

    gerarNotificacaoCadastro($pdo, $_SESSION['username'], $_SESSION['setor'], $id, $nome, $data_vencimento);
    gerarNotificacoes($pdo, $_SESSION['username'], $_SESSION['setor']);
    gerarEventos($pdo);

    echo json_encode([
        'success' => true,
        'message' => $mensagem,
        'id' => $id,
        'arquivo' => $arquivo_path,
        'contrato_id' => $contrato_id,
        'contrato_titulo' => $contrato_titulo
    ]);
} catch (Exception $e) {
    error_log("Erro ao salvar certidão: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar certidão: ' . $e->getMessage()]);
}
?>
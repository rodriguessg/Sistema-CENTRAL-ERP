<?php
session_start();
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Configurar sessão
$_SESSION['username'] = 'contratos';
$_SESSION['setor'] = 'contratos';

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    echo "Erro de conexão com o banco de dados: " . $e->getMessage();
    exit;
}

// Função para limpar tabelas (apenas para teste)
function limparTabelas($pdo) {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("TRUNCATE TABLE notificacoes");
        $pdo->exec("TRUNCATE TABLE eventos");
        $pdo->exec("TRUNCATE TABLE certidoes");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "Tabelas limpas com sucesso.\n";
    } catch (Exception $e) {
        error_log("Erro ao limpar tabelas: " . $e->getMessage());
        echo "Erro ao limpar tabelas: " . $e->getMessage() . "\n";
        exit;
    }
}

// Função para simular salvamento da certidão
function salvarCertidaoTeste($pdo, $certidao) {
    try {
        $_POST = $certidao;
        ob_start();
        include 'salvar_certidao.php';
        $output = ob_get_clean();
        preg_match('/\{.*\}/s', $output, $matches);
        if (!isset($matches[0])) {
            throw new Exception("Nenhum JSON encontrado na saída: $output");
        }
        $json_output = $matches[0];
        $result = json_decode($json_output, true);
        if (!$result || !isset($result['success'])) {
            throw new Exception("Resposta JSON inválida: $json_output");
        }
        return $result;
    } catch (Exception $e) {
        error_log("Erro ao executar salvar_certidao.php: " . $e->getMessage());
        throw $e;
    }
}

// Função para testar notificações e eventos
function testarNotificacoesEventos($pdo) {
    try {
        $hoje = new DateTime();
        $data_vencimento = (clone $hoje)->modify('+3 days')->format('Y-m-d');
        $certidao = [
            'documento' => 'CND',
            'data_vencimento' => $data_vencimento,
            'nome' => 'Certidão Negativa Teste',
            'fornecedor' => 'Empresa Teste',
            'responsavel' => 'contratos',
            'arquivo' => null,
            'contrato_id' => null
        ];

        $result = salvarCertidaoTeste($pdo, $certidao);

        if (!$result['success']) {
            echo "Erro ao salvar certidão: " . $result['message'] . "\n";
            return false;
        }

        $certidao_id = $result['id'];
        echo "Certidão cadastrada com ID: $certidao_id\n";

        // Verificar notificação de cadastro
        $sql_notificacao = "SELECT * FROM notificacoes WHERE mensagem LIKE :mensagem";
        $stmt_notificacao = $pdo->prepare($sql_notificacao);
        $mensagem_cadastro = '%Certidão "Certidão Negativa Teste" (ID: ' . $certidao_id . ')%';
        $stmt_notificacao->execute(['mensagem' => $mensagem_cadastro]);
        $notificacao_cadastro = $stmt_notificacao->fetch(PDO::FETCH_ASSOC);

        if ($notificacao_cadastro) {
            $mensagem_esperada = sprintf(
                'Certidão "Certidão Negativa Teste" (ID: %d) cadastrada com sucesso. Vence em %s.',
                $certidao_id,
                (new DateTime($data_vencimento))->format('d/m/Y')
            );
            if ($notificacao_cadastro['mensagem'] === $mensagem_esperada &&
                $notificacao_cadastro['username'] === 'contratos' &&
                $notificacao_cadastro['setor'] === 'contratos' &&
                $notificacao_cadastro['situacao'] === 'nao lida') {
                echo "Notificação de cadastro criada corretamente:\n";
                echo "Mensagem: " . $notificacao_cadastro['mensagem'] . "\n";
                echo "Situação: " . $notificacao_cadastro['situacao'] . "\n";
            } else {
                echo "Erro: Notificação de cadastro com dados incorretos.\n";
                echo "Mensagem recebida: " . $notificacao_cadastro['mensagem'] . "\n";
                echo "Mensagem esperada: $mensagem_esperada\n";
                echo "Situação recebida: " . $notificacao_cadastro['situacao'] . "\n";
                return false;
            }
        } else {
            echo "Erro: Nenhuma notificação de cadastro criada para certidao_id = $certidao_id.\n";
            return false;
        }

        // Verificar notificação de vencimento
        $sql_notificacao_venc = "SELECT * FROM notificacoes WHERE mensagem LIKE :mensagem";
        $stmt_notificacao_venc = $pdo->prepare($sql_notificacao_venc);
        $mensagem_vencimento = '%A certidão "Certidão Negativa Teste" (ID: ' . $certidao_id . ')%';
        $stmt_notificacao_venc->execute(['mensagem' => $mensagem_vencimento]);
        $notificacao_vencimento = $stmt_notificacao_venc->fetch(PDO::FETCH_ASSOC);

        if ($notificacao_vencimento) {
            $mensagem_esperada = sprintf(
                'A certidão "Certidão Negativa Teste" (ID: %d) está próxima do vencimento (3 dias restantes, vence em %s). ',
                $certidao_id,
                (new DateTime($data_vencimento))->format('d/m/Y')
            );
            if ($notificacao_vencimento['mensagem'] === $mensagem_esperada &&
                $notificacao_vencimento['username'] === 'contratos' &&
                $notificacao_vencimento['setor'] === 'contratos' &&
                $notificacao_vencimento['situacao'] === 'nao lida') {
                echo "Notificação de vencimento criada corretamente:\n";
                echo "Mensagem: " . $notificacao_vencimento['mensagem'] . "\n";
                echo "Situação: " . $notificacao_vencimento['situacao'] . "\n";
            } else {
                echo "Erro: Notificação de vencimento com dados incorretos.\n";
                echo "Mensagem recebida: " . $notificacao_vencimento['mensagem'] . "\n";
                echo "Mensagem esperada: $mensagem_esperada\n";
                echo "Situação recebida: " . $notificacao_vencimento['situacao'] . "\n";
                return false;
            }
        } else {
            echo "Erro: Nenhuma notificação de vencimento criada para certidao_id = $certidao_id.\n";
            return false;
        }

        // Verificar evento
        $sql_evento = "SELECT * FROM eventos WHERE descricao LIKE :descricao";
        $stmt_evento = $pdo->prepare($sql_evento);
        $descricao_evento = '%Certidão ID: ' . $certidao_id . '%';
        $stmt_evento->execute(['descricao' => $descricao_evento]);
        $evento = $stmt_evento->fetch(PDO::FETCH_ASSOC);

        if ($evento) {
            $titulo_esperado = "Renovar Certidão: Certidão Negativa Teste";
            $descricao_esperada = sprintf(
                'Certidão ID: %d, vence em %s.',
                $certidao_id,
                (new DateTime($data_vencimento))->format('d/m/Y')
            );
            $cor_esperada = '#dc3545';
            $categoria_esperada = 'Renovação';
            $hora_esperada = '00:00:00';
            $data_evento_esperada = $hoje->format('Y-m-d');

            if ($evento['titulo'] === $titulo_esperado &&
                $evento['descricao'] === $descricao_esperada &&
                $evento['cor'] === $cor_esperada &&
                $evento['categoria'] === $categoria_esperada &&
                $evento['hora'] === $hora_esperada &&
                date('Y-m-d', strtotime($evento['data'])) === $data_evento_esperada) {
                echo "Evento criado corretamente:\n";
                echo "Título: " . $evento['titulo'] . "\n";
                echo "Descrição: " . $evento['descricao'] . "\n";
                echo "Data: " . $evento['data'] . "\n";
                echo "Hora: " . $evento['hora'] . "\n";
                echo "Categoria: " . $evento['categoria'] . "\n";
            } else {
                echo "Erro: Evento criado com dados incorretos.\n";
                echo "Título recebido: " . $evento['titulo'] . "\n";
                echo "Título esperado: $titulo_esperado\n";
                echo "Descrição recebida: " . $evento['descricao'] . "\n";
                echo "Descrição esperada: $descricao_esperada\n";
                echo "Cor recebida: " . $evento['cor'] . "\n";
                echo "Categoria recebida: " . $evento['categoria'] . "\n";
                echo "Hora recebida: " . $evento['hora'] . "\n";
                echo "Data recebida: " . $evento['data'] . "\n";
                echo "Data esperada: $data_evento_esperada\n";
                return false;
            }
        } else {
            echo "Erro: Nenhum evento criado para certidao_id = $certidao_id.\n";
            return false;
        }

        return true;
    } catch (Exception $e) {
        error_log("Erro no teste: " . $e->getMessage());
        echo "Erro no teste: " . $e->getMessage() . "\n";
        return false;
    }
}

// Executar teste
echo "Iniciando teste de notificações e eventos...\n";
limparTabelas($pdo);
$resultado = testarNotificacoesEventos($pdo);
echo "\nResultado do teste: " . ($resultado ? "SUCESSO" : "FALHA") . "\n";
?>
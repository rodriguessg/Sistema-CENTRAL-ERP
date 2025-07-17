<?php
session_start(); // Inicia a sessão
include 'banco.php'; // Inclui o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $setor = strtolower(htmlspecialchars($_POST['setor'])); // Setor fornecido no formulário
    $username = htmlspecialchars($_POST['username']); // Nome de usuário fornecido no formulário
    $senha = htmlspecialchars($_POST['senha']); // Senha fornecida no formulário

    // Consulta para verificar o usuário no banco de dados
    $query = "SELECT * FROM usuario WHERE username = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verifica se o usuário está ativo
        if (strtolower($usuario['situacao']) !== 'ativo') {
            registrarLogEvento($con, $username, 'Login falhou: Usuário inativo');
            header("Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=inativo&pagina=/Sistema-CENTRAL-ERP/index.php");

                   
            exit();
        }

        // Verifica se o setor informado corresponde ao setor do usuário
        if (strtolower($usuario['setor']) !== $setor) {
            registrarLogEvento($con, $username, 'Login falhou: Setor incorreto');
            header("Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=setor_nao_reconhecido&pagina=/Sistema-CENTRAL-ERP/index.php");
            exit();
        }

        // Verifica a senha
        if (password_verify($senha, $usuario['senha'])) {
            // Define variáveis de sessão para manter a autenticação
            $_SESSION['username'] = $username; // Nome de usuário
            $_SESSION['setor'] = $setor; // Setor do usuário

            // Registra o login bem-sucedido no log_eventos
            registrarLogEvento($con, $username, 'Login bem-sucedido');

            // Define o cookie com o nome do setor e redireciona para loading.php
            $cookie_duration = time() + (24 * 3600); // 24 horas
            setcookie($setor, $username, $cookie_duration);

            // Redireciona para a página loading.php passando o destino
            header("Location: loading.php?destino=" . urlencode(getRedirectionPage($setor)));
            exit();
        } else {
            // Senha inválida
            registrarLogEvento($con, $username, 'Login falhou: Senha inválida');
            header("Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=senha_invalida&pagina=/Sistema-CENTRAL-ERP/index.php");
            exit();
        }
    } else {
        // Usuário não encontrado
        registrarLogEvento($con, $username, 'Login falhou: Usuário não encontrado');
        header("Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=usuario_invalido&pagina=/Sistema-CENTRAL-ERP/index.php");
        exit();
    }
}

/**
 * Função para retornar a URL de redirecionamento de acordo com o setor
 *
 * @param string $setor Setor do usuário
 * @return string URL de destino
 */
function getRedirectionPage($setor) {
    switch ($setor) {
        case 'administrador':
            return "painel.php";
        case 'patrimonio':
            return "painelpatrimonio.php";
        case 'financeiro':
            return "homefinanceiro.php";
        case 'estoque':
            return "painelalmoxarifado.php";
        case 'recursos_humanos':
            return "RH.php";
        case 'contratos':
            return "painelcontratos.php";
        case 'ccooperacao':
            return 'operacao.php';
            case'sisbonde':
                return 'painelbonde.php';
                  case'planejamento':
                return 'planejamento.php';
                  case'compras':
                return 'homecompras.php';
                  case'sismed':
                return 'homesismed.php';
        case 'helpdesk':
            return "hometech.php";
        default:
            return "index.php"; // Caso de erro, redireciona para a página inicial
    }
}

/**
 * Função para registrar um evento no log_eventos
 *
 * @param mysqli $con Conexão com o banco de dados
 * @param string $matricula Username do usuário
 * @param string $tipo_operacao Descrição da operação realizada
 * @return void
 */
function registrarLogEvento($con, $matricula, $tipo_operacao) {
    $query_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())";
    $stmt_log = $con->prepare($query_log);

    if (!$stmt_log) {
        die("Erro ao preparar a consulta de log_eventos: " . $con->error);
    }

    $stmt_log->bind_param("ss", $matricula, $tipo_operacao);
    if (!$stmt_log->execute()) {
        die("Erro ao registrar evento no log_eventos: " . $stmt_log->error);
    }

    $stmt_log->close();
}
?>

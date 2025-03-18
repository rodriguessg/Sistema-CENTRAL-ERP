<?php
header('Content-Type: application/json');

// Configurações do banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['reply' => 'Erro ao conectar ao banco de dados: ' . $conn->connect_error]);
    exit;
}

// Função para processar as mensagens do usuário
function processarMensagem($mensagem, $conn) {
    $mensagem = strtolower(trim($mensagem)); // Normaliza a entrada do usuário

    if (empty($mensagem)) {
        return "Por favor, digite algo para que eu possa ajudar.";
    }

    // Verifica a presença de palavras-chave na mensagem para gerar a resposta apropriada
    if (strpos($mensagem, 'o que você pode fazer') !== false) {
        return listarPossibilidades();
    }

    if (strpos($mensagem, 'relatório') !== false) {
        return gerarRelatorio($conn);
    }

    if (strpos($mensagem, 'quantos usuários') !== false || strpos($mensagem, 'usuários ativos') !== false) {
        return "Atualmente temos " . obterContagem($conn, 'usuario') . " usuários cadastrados.";
    }

    if (strpos($mensagem, 'produtos') !== false) {
        return "Há um total de " . obterContagem($conn, 'produtos') . " produtos cadastrados no sistema.";
    }

    if (strpos($mensagem, 'patrimônio') !== false) {
        return "O sistema possui " . obterContagem($conn, 'patrimonio') . " itens de patrimônio registrados.";
    }

    if (strpos($mensagem, 'setores') !== false) {
        return "Atualmente temos " . obterContagem($conn, 'setores') . " setores ativos no sistema.";
    }

    if (strpos($mensagem, 'funcionários') !== false) {
        return "Há " . obterContagem($conn, 'funcionario') . " funcionários cadastrados no sistema.";
    }

    if (strpos($mensagem, 'data de criação') !== false || strpos($mensagem, 'quando começou') !== false) {
        return "O sistema foi criado em " . obterDataCriacao($conn) . ".";
    }

    if (strpos($mensagem, 'estoque') !== false) {
        return gerarResumoEstoque($conn);
    }

    if ($mensagem === 'sim') {
        return gerarRelatorioEstoque($conn);
    }

    if (strpos($mensagem, 'ajuda') !== false || strpos($mensagem, 'como usar') !== false) {
        return "Posso ajudá-lo com informações sobre usuários, produtos, patrimônio, setores ou funcionários. Pergunte algo específico para que eu possa ajudar!";
    }

    return "Eu ainda estou aprendendo. Pode me explicar melhor sua solicitação?";
}

// Função para gerar resumo do estoque
function gerarResumoEstoque($conn) {
    $totalProdutos = obterContagem($conn, 'produtos');
    $valorEstoque = obterValorTotalEstoque($conn);
    $saidasAno = obterSaidasAno($conn);

    return "Atualmente há $totalProdutos produtos no estoque, com um valor total de R$ $valorEstoque. Foram registradas $saidasAno saídas durante o ano. " .
           "Deseja que eu gere um relatório completo? Responda com 'sim' ou 'não'.";
}

// Função para gerar um relatório completo do estoque
function gerarRelatorioEstoque($conn) {
    $sql = "SELECT produto, quantidade, custo, (quantidade * custo) AS valor_total FROM produtos";
    $result = $conn->query($sql);

    if (!$result) {
        return "Erro ao gerar o relatório de estoque: " . $conn->error;
    }

    $relatorio = "<table border='1' style='width:100%; text-align:left;'>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Valor Unitário (R$)</th>
                        <th>Valor Total (R$)</th>
                    </tr>";

    while ($row = $result->fetch_assoc()) {
        $relatorio .= "<tr>
                        <td>{$row['produto']}</td>
                        <td>{$row['quantidade']}</td>
                        <td>" . number_format($row['custo'], 2, ',', '.') . "</td>
                        <td>" . number_format($row['valor_total'], 2, ',', '.') . "</td>
                       </tr>";
    }

    $relatorio .= "</table>";
    $relatorio .= "<br><a href='gerar_pdf.php' target='_blank'>Clique aqui para imprimir o relatório</a>";

    return $relatorio;
}

// Função para listar as possibilidades de comandos
function listarPossibilidades() {
    return "Posso ajudá-lo com as seguintes funcionalidades:\n" .
           "- Consultar usuários cadastrados.\n" .
           "- Gerar relatórios detalhados do sistema.\n" .
           "- Verificar o estoque e o número de saídas.\n" .
           "- Obter informações sobre setores, produtos e funcionários.\n" .
           "Pergunte algo específico para que eu possa ajudar!";
}

// Função para obter o valor total do estoque
function obterValorTotalEstoque($conn) {
    $sql = "SELECT SUM(custo * quantidade) AS total_estoque FROM produtos";
    $result = $conn->query($sql);

    if (!$result) {
        return "Erro ao acessar o estoque: " . $conn->error;
    }

    $row = $result->fetch_assoc();
    return $row ? number_format($row['total_estoque'], 2, ',', '.') : '0,00';
}

// Função para obter o número de saídas no ano
function obterSaidasAno($conn) {
    $anoAtual = date('Y');
    $sql = "SELECT COUNT(*) AS total_saidas FROM saidas WHERE YEAR(data_saida) = $anoAtual";
    $result = $conn->query($sql);

    if (!$result) {
        return "Erro ao acessar as saídas: " . $conn->error;
    }

    $row = $result->fetch_assoc();
    return $row ? $row['total_saidas'] : 0;
}

// Função para obter a contagem de registros em uma tabela
function obterContagem($conn, $tipo) {
    $sql = "SELECT COUNT(*) AS total FROM $tipo";
    $result = $conn->query($sql);

    if (!$result) {
        return "Erro ao acessar a contagem de $tipo: " . $conn->error;
    }

    $row = $result->fetch_assoc();
    return $row ? $row['total'] : 0;
}

// Função para obter a data de criação do sistema
function obterDataCriacao($conn) {
    $sql = "SELECT MIN(data_criacao) AS data_criacao FROM data_criacao";
    $result = $conn->query($sql);

    if (!$result) {
        return "Erro ao acessar a data de criação: " . $conn->error;
    }

    $row = $result->fetch_assoc();
    return $row && $row['data_criacao'] ? $row['data_criacao'] : 'Não disponível';
}

// Função para gerar relatório do sistema
function gerarRelatorio($conn) {
    $abas = ['Central', 'Controle Patrimonial', 'Controle Financeiro', 'Estoque', 'RH'];
    $usuariosAtivos = obterContagem($conn, 'usuario');
    $produtos = obterContagem($conn, 'produtos');
    $patrimonio = obterContagem($conn, 'patrimonio');
    $setores = obterContagem($conn, 'setores');
    $funcionarios = obterContagem($conn, 'funcionario');
    $dataCriacao = obterDataCriacao($conn);

    return "Relatório do Sistema:\n" .
           "- Abas existentes: " . implode(', ', $abas) . ".\n" .
           "- Usuários ativos: {$usuariosAtivos}.\n" .
           "- Produtos cadastrados: {$produtos}.\n" .
           "- Itens de patrimônio: {$patrimonio}.\n" .
           "- Setores cadastrados: {$setores}.\n" .
           "- Funcionários cadastrados: {$funcionarios}.\n" .
           "- Data de criação do sistema: {$dataCriacao}.";
}

// Recebe e processa a mensagem do cliente
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['message']) || trim($input['message']) === '') {
    echo json_encode(['reply' => 'Por favor, envie uma mensagem válida.']);
    $conn->close();
    exit;
}

$mensagemUsuario = $input['message'];
$respostaIA = processarMensagem($mensagemUsuario, $conn);

// Fecha a conexão com o banco de dados
$conn->close();

// Retorna a resposta
echo json_encode(['reply' => $respostaIA]);
?>

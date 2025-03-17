<?php
header('Content-Type: text/html; charset=utf-8');

// Verifica se os parâmetros necessários estão presentes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $periodo = $_POST['periodo'] ?? null;
    $exercicio = $_POST['exercicio'] ?? null;
    $incluirTabela = isset($_POST['imprimir_tabela']) ? true : false;

    // Função para gerar a tabela de produtos, quantidades e cálculos adicionais
    function gerarTabelaProdutos()
    {
        try {
            // Conexão segura com o banco de dados
            $pdo = new PDO('mysql:host=localhost;dbname=supat;charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Query para buscar os dados
            $sql = "SELECT produto, quantidade, custo FROM produtos";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            // Inicializa variáveis para cálculos
            $quantidadeTotal = 0;
            $custoTotal = 0;

            // Monta a tabela com classes para estilização
            $tabela = '<table class="tabela-interativa" border="1" cellspacing="0" cellpadding="10">';
            $tabela .= '<thead><tr><th>Produto</th><th>Quantidade</th><th>Custo Unitário</th></tr></thead>';
            $tabela .= '<tbody>';

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tabela .= '<tr>';
                $tabela .= '<td>' . htmlspecialchars($row['produto']) . '</td>';
                $tabela .= '<td>' . htmlspecialchars($row['quantidade']) . '</td>';
                $tabela .= '<td>' . number_format($row['custo'], 2, ',', '.') . '</td>';
                $tabela .= '</tr>';

                // Atualiza os cálculos
                $quantidadeTotal += $row['quantidade'];
                $custoTotal += $row['quantidade'] * $row['custo'];
            }

            $tabela .= '</tbody>';

            // Adiciona o rodapé da tabela com os totais
            $tabela .= '<tfoot>';
            $tabela .= '<tr>';
            $tabela .= '<th>Total</th>';
            $tabela .= '<th>' . htmlspecialchars($quantidadeTotal) . '</th>';
            $tabela .= '<th>' . number_format($custoTotal, 2, ',', '.') . '</th>';
            $tabela .= '</tr>';
            $tabela .= '</tfoot>';

            $tabela .= '</table>';
            return $tabela;
        } catch (PDOException $e) {
            // Mensagem de erro segura
            return '<p style="color:red;">Erro ao carregar os dados. Tente novamente mais tarde.</p>';
        }
    }

    // Inicializa o conteúdo do relatório
    $relatorioConteudo = '';

    // Gerar o conteúdo com base no período selecionado
    switch ($periodo) {
        case 'semanal':
            $relatorioConteudo .= '<h3>Relatório Semanal</h3><p>Dados referentes ao período semanal.</p>';
            break;

        case 'mensal':
            $relatorioConteudo .= '<h3>Relatório Mensal</h3><p>Dados referentes ao período mensal.</p>';
            break;

        case 'anual':
            if ($exercicio) {
                $relatorioConteudo .= '<h3>Relatório Anual - Exercício ' . htmlspecialchars($exercicio) . '</h3>';
            } else {
                http_response_code(400);
                echo '<p style="color:red;">Exercício (ano) não selecionado.</p>';
                exit;
            }
            break;

        default:
            http_response_code(400);
            echo '<p style="color:red;">Período inválido.</p>';
            exit;
    }

    // Adicionar a tabela de produtos se solicitado
    if ($incluirTabela) {
        $relatorioConteudo .= gerarTabelaProdutos();
    }

    // Retornar o conteúdo final
    echo $relatorioConteudo;

} else {
    // Resposta para métodos não permitidos
    http_response_code(405);
    echo '<p style="color:red;">Método de requisição inválido.</p>';
    exit;
}

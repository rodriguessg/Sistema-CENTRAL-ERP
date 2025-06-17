<?php

session_start();

// Configurações do banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produto = $_POST['produto'];
    $classificacao = $_POST['classificacao'];
    $natureza = $_POST['natureza'];
    $contabil = $_POST['contabil'];
    $descricao = $_POST['descricao'];
    $unidade = $_POST['unidade'];
    $localizacao = $_POST['localizacao'];
    $custo = (float) str_replace([',', '.'], ['.', ''], $_POST['custo']);
    $preco_medio = (float) str_replace([',', '.'], ['.', ''], $_POST['preco_medio']);
    $quantidade = (float) $_POST['quantidade'];
    $nf = $_POST['nf'];

    if (!is_numeric($custo) || !is_numeric($preco_medio) || !is_numeric($quantidade)) {
        echo "Erro: O valor de custo, preço médio ou quantidade não é válido!";
        exit;
    }

    $novo_total_entrada = $quantidade * $preco_medio;

    $sql_verifica = "SELECT id, quantidade, custo, preco_medio FROM produtos WHERE produto = ?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $produto);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        $stmt_verifica->bind_result($id_existente, $quantidade_existente, $custo_existente, $preco_medio_existente);
        $stmt_verifica->fetch();

        $total_valor_existente = $quantidade_existente * $preco_medio_existente;
        $total_valor_novo = $quantidade * $preco_medio;
        $nova_quantidade = $quantidade_existente + $quantidade;
        $novo_preco_medio = ($total_valor_existente + $total_valor_novo) / $nova_quantidade;

        $sql_atualiza = "UPDATE produtos SET quantidade = ?, custo = ?, preco_medio = ?, descricao = ? WHERE id = ?";
        $stmt_atualiza = $conn->prepare($sql_atualiza);
        $stmt_atualiza->bind_param("dddsi", $nova_quantidade, $custo, $novo_preco_medio, $descricao, $id_existente);

        if ($stmt_atualiza->execute()) {
            $query_transacao = "INSERT INTO transicao (material_id, quantidade, data, tipo) VALUES (?, ?, ?, 'entrada')";
            $transacaoStmt = $conn->prepare($query_transacao);
            $transacaoStmt->bind_param('ids', $id_existente, $quantidade, date('Y-m-d H:i:s'));
            $transacaoStmt->execute();
            $transacaoStmt->close();
        }
        $stmt_atualiza->close();
    } else {
        $sql_insere = "INSERT INTO produtos (produto, classificacao, natureza, contabil, descricao, unidade, localizacao, custo, quantidade, preco_medio, nf, tipo_operacao, data_cadastro) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'cadastrado', NOW())";
        $stmt_insere = $conn->prepare($sql_insere);
        $stmt_insere->bind_param(
            "sssssssddds",
            $produto,
            $classificacao,
            $natureza,
            $contabil,
            $descricao,
            $unidade,
            $localizacao,
            $custo,
            $quantidade,
            $preco_medio,
            $nf
        );

        if ($stmt_insere->execute()) {
            $produto_id = $conn->insert_id;
            $query_transacao = "INSERT INTO transicao (material_id, quantidade, data, tipo) VALUES (?, ?, ?, 'entrada')";
            $transacaoStmt = $conn->prepare($query_transacao);
            $transacaoStmt->bind_param('ids', $produto_id, $quantidade, date('Y-m-d H:i:s'));
            $transacaoStmt->execute();
            $transacaoStmt->close();
        }
        $stmt_insere->close();
    }

    $sql_fe = "SELECT saldo_atual, total_entrada FROM fechamento WHERE natureza = ?";
    $stmt_fe = $conn->prepare($sql_fe);
    $stmt_fe->bind_param("s", $natureza);
    $stmt_fe->execute();
    $stmt_fe->store_result();

    if ($stmt_fe->num_rows > 0) {
        $stmt_fe->bind_result($saldo_atual, $total_entrada_existente);
        $stmt_fe->fetch();

        $novo_total_entrada = $total_entrada_existente + $novo_total_entrada;
        $novo_saldo_atual = $saldo_atual + $novo_total_entrada;

        $sql_update_fe = "UPDATE fechamento SET total_entrada = ?, saldo_atual = ? WHERE natureza = ?";
        $stmt_update_fe = $conn->prepare($sql_update_fe);
        $stmt_update_fe->bind_param("dds", $novo_total_entrada, $novo_saldo_atual, $natureza);
        $stmt_update_fe->execute();
        $stmt_update_fe->close();
    } else {
        $sql_insert_fe = "INSERT INTO fechamento (natureza, total_entrada, saldo_atual, data_fechamento) VALUES (?, ?, ?, NOW())";
        $stmt_insert_fe = $conn->prepare($sql_insert_fe);
        $stmt_insert_fe->bind_param("sdd", $natureza, $novo_total_entrada, $novo_total_entrada);
        $stmt_insert_fe->execute();
        $stmt_insert_fe->close();
    }

    $sql_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())";
    $stmt_log = $conn->prepare($sql_log);
    $tipo_operacao = "cadastrou ou atualizou o produto no estoque";
    $stmt_log->bind_param("ss", $username, $tipo_operacao);

    if ($stmt_log->execute()) {
        header('Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=sucesso2&pagina=/Sistema-CENTRAL-ERP/homeestoque.php');
        exit();
    }
    $stmt_log->close();
}

$conn->close();
?>

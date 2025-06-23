<?php

session_start();

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
    $custo = $_POST['custo'];  
    $preco_medio = $_POST['preco_medio'];
    $quantidade = $_POST['quantidade']; 
    $nf = $_POST['nf'];

    $custo = str_replace('.', '', $custo);
    $custo = str_replace(',', '.', $custo);
    $preco_medio = str_replace('.', '', $preco_medio);
    $preco_medio = str_replace(',', '.', $preco_medio);

    $custo = (float)$custo;
    $preco_medio = (float)$preco_medio;
    $quantidade = (float)$quantidade;

    if (!is_numeric($custo) || !is_numeric($preco_medio) || !is_numeric($quantidade)) {
        echo "Erro: O valor de custo, preço médio ou quantidade não é válido!";
        exit;
    }

    $valor_total = $quantidade * $preco_medio;

    $sql_verifica = "SELECT id, quantidade, custo, preco_medio FROM produtos WHERE produto = ?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $produto);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        $stmt_verifica->bind_result($id, $qtd_existente, $custo_existente, $preco_existente);
        $stmt_verifica->fetch();

        $novo_total_valor = ($qtd_existente * $preco_existente) + $valor_total;
        $nova_qtd = $qtd_existente + $quantidade;
        $novo_preco_medio = $novo_total_valor / $nova_qtd;
        $custo_total = $custo_existente + $custo;

        $stmt_atualiza = $conn->prepare("UPDATE produtos SET quantidade = ?, custo = ?, preco_medio = ?, descricao = ? WHERE id = ?");
        $stmt_atualiza->bind_param("dddsi", $nova_qtd, $custo_total, $novo_preco_medio, $descricao, $id);
        $stmt_atualiza->execute();
        $stmt_atualiza->close();

        $id_produto = $id;
    } else {
        $sql_insere = "INSERT INTO produtos (produto, classificacao, natureza, contabil, descricao, unidade, localizacao, custo, quantidade, preco_medio, nf, tipo_operacao, data_cadastro) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'cadastrado', NOW())";
        $stmt_insere = $conn->prepare($sql_insere);
        $stmt_insere->bind_param("sssssssddds", $produto, $classificacao, $natureza, $contabil, $descricao, $unidade, $localizacao, $custo, $quantidade, $preco_medio, $nf);
        $stmt_insere->execute();
        $id_produto = $conn->insert_id;
        $stmt_insere->close();
    }

    $query_transacao = "INSERT INTO transicao (material_id, quantidade, data, tipo, valor_custo_total) VALUES (?, ?, ?, 'entrada', ?)";
    $transacaoStmt = $conn->prepare($query_transacao);
    $transacaoStmt->bind_param('iisd', $id_produto, $quantidade, date('Y-m-d H:i:s'), $preco_medio);
    $transacaoStmt->execute();
    $transacaoStmt->close();

    $sql_fe = "SELECT saldo_atual, total_entrada FROM fechamento WHERE natureza = ?";
    $stmt_fe = $conn->prepare($sql_fe);
    $stmt_fe->bind_param("s", $natureza);
    $stmt_fe->execute();
    $stmt_fe->store_result();

    if ($stmt_fe->num_rows > 0) {
        $stmt_fe->bind_result($saldo_atual, $total_entrada_existente);
        $stmt_fe->fetch();

        $novo_total_entrada = $total_entrada_existente + $valor_total;
        $novo_saldo_atual = $saldo_atual + $valor_total;

        $stmt_update_fe = $conn->prepare("UPDATE fechamento SET total_entrada = ?, saldo_atual = ? WHERE natureza = ?");
        $stmt_update_fe->bind_param("dds", $novo_total_entrada, $novo_saldo_atual, $natureza);
        $stmt_update_fe->execute();
        $stmt_update_fe->close();
    } else {
        $stmt_insert_fe = $conn->prepare("INSERT INTO fechamento (natureza, total_entrada, saldo_atual, data_fechamento) VALUES (?, ?, ?, NOW())");
        $stmt_insert_fe->bind_param("sdd", $natureza, $valor_total, $valor_total);
        $stmt_insert_fe->execute();
        $stmt_insert_fe->close();
    }

    $stmt_log = $conn->prepare("INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())");
    $tipo_operacao = "cadastrou ou atualizou o produto no estoque";
    $stmt_log->bind_param("ss", $username, $tipo_operacao);
    $stmt_log->execute();
    $stmt_log->close();

    header('Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=sucesso2&pagina=/Sistema-CENTRAL-ERP/homeestoque.php');
    exit();
}

$conn->close();
?>
<?php
// Inclui o arquivo de conexão com o banco de dados
include('banco.php');

// Captura os dados enviados pelo formulário
$patrimonio_id = $_POST['patrimonio_id'];
$destino = $_POST['destino'];
$responsavel = $_POST['responsavel'];
$data_transferencia = date('Y-m-d'); // Data atual

// Verifica se todos os campos estão preenchidos
if (empty($patrimonio_id) || empty($destino) || empty($responsavel)) {
    die("Erro: Todos os campos devem ser preenchidos.");
}

// Verifica se o patrimonio_id existe na tabela patrimonio
$query_check = "SELECT id FROM patrimonio WHERE id = ?";
$stmt_check = $con->prepare($query_check);
$stmt_check->bind_param('i', $patrimonio_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows == 0) {
    die("Erro: O patrimônio com ID $patrimonio_id não existe na tabela patrimonio.");
}

$stmt_check->close();

// Insere os dados na tabela transferencias com o valor 'Transferido' para tipo_operacao
$query = "INSERT INTO transferencias (patrimonio_id, destino, responsavel, data_transferencia) VALUES (?,  ?, ?, ?)";
$stmt = $con->prepare($query);
if (!$stmt) {
    die("Erro na preparação da consulta: " . $con->error);
}

// Define o valor fixo para tipo_operacao como "Transferido"
$tipo_operacao = "Transferido";
$stmt->bind_param('issss', $patrimonio_id, $destino, $responsavel, $data_transferencia, $tipo_operacao);

// Executa a consulta
if ($stmt->execute()) {
    echo "Transferência registrada com sucesso na tabela transferencias!";
    
    // Atualiza a coluna tipo_operacao na tabela patrimonio
    $query_update = "UPDATE patrimonio SET situacao = ? WHERE id = ?";
    $stmt_update = $con->prepare($query_update);
    if ($stmt_update) {
        $stmt_update->bind_param('si', $tipo_operacao, $patrimonio_id);
        if ($stmt_update->execute()) {
            echo "Coluna tipo_operacao atualizada com sucesso na tabela patrimonio!";
        } else {
            echo "Erro ao atualizar a coluna tipo_operacao na tabela patrimonio: " . $stmt_update->error;
        }
        $stmt_update->close();
    } else {
        echo "Erro na preparação da consulta de atualização: " . $con->error;
    }
} else {
    echo "Erro ao registrar a transferência: " . $stmt->error;
}

$stmt->close();
$con->close();
?>

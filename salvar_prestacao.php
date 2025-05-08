<?php
// salvar_prestacao.php - Salva os dados da prestação de contas
include 'banco.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contratoId = $_POST['contrato_titulo'];  // Aqui você precisará obter o ID do contrato
    $valorPrestado = $_POST['valor_a_prestar'];
    $dataPagamento = $_POST['data_pagamento'];
    $observacoes = $_POST['observacoes'];

    // Lidar com o upload de arquivos
    $documentos = '';
    if (isset($_FILES['documentos'])) {
        foreach ($_FILES['documentos']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['documentos']['name'][$key];
            $file_tmp = $_FILES['documentos']['tmp_name'][$key];
            move_uploaded_file($file_tmp, "uploads/$file_name");
            $documentos .= $file_name . '; ';
        }
    }

    try {
        // Inserir dados na tabela de prestação de contas
        $sql = "INSERT INTO prestacao_de_contas (contrato_titulo, valor_a_prestar, data_pagamento, documentos, observacoes) 
                VALUES (:contrato_titulo, :valor_a_prestar, :data_pagamento, :documentos, :observacoes)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'contrato_titulo' => $contratoId,
            'valor_a_prestar' => $valorPrestado,
            'data_pagamento' => $dataPagamento,
            'documentos' => $documentos,
            'observacoes' => $observacoes
        ]);

        echo "Prestação de contas salva com sucesso!";
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>

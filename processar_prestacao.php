<?php
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $contrato_id = filter_input(INPUT_POST, 'contrato_id', FILTER_VALIDATE_INT);
        $valor_pago = filter_input(INPUT_POST, 'valor_pago', FILTER_VALIDATE_FLOAT);
        $descricao = filter_input(INPUT_POST, 'descricaoprestacao', FILTER_SANITIZE_STRING);
        $data_pagamento = filter_input(INPUT_POST, 'data_pagamento', FILTER_SANITIZE_STRING);
        $prestacao_status = filter_input(INPUT_POST, 'prestacao_status', FILTER_SANITIZE_STRING);
   

        if ($contrato_id && $valor_pago && $descricao && $data_pagamento && $prestacao_status) {
            // Verificar se já existe registro para o contrato
            $sql_check = "SELECT id FROM prestacao_contas WHERE contrato_id = :contrato_id";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->bindParam(':contrato_id', $contrato_id, PDO::PARAM_INT);
            $stmt_check->execute();
            $existing = $stmt_check->fetch();

            if ($existing) {
                // Atualizar registro existente
                $sql = "UPDATE prestacao_contas 
                        SET valor_pago = :valor_pago, 
                            descricao = :descricao, 
                            data_pagamento = :data_pagamento, 
                            status = :status
                        WHERE contrato_id = :contrato_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':valor_pago' => $valor_pago,
                    ':descricao' => $descricao,
                    ':data_pagamento' => $data_pagamento,
                    ':status' => $prestacao_status,
                    ':contrato_id' => $contrato_id
                ]);
            } else {
                // Inserir novo registro
                $sql = "INSERT INTO prestacao_contas (contrato_id, valor_pago, descricao, data_pagamento, status) 
                        VALUES (:contrato_id, :valor_pago, :descricao, :data_pagamento, :status)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':contrato_id' => $contrato_id,
                    ':valor_pago' => $valor_pago,
                    ':descricao' => $descricao,
                    ':data_pagamento' => $data_pagamento,
                    ':status' => $prestacao_status
                ]);
            }

            header("Location: homecontratos.php?success=1");
            exit;
        } else {
            header("Location: viwes/mensagem.php?error=invalid_data");
            exit;
        }
    }
} catch (PDOException $e) {
    error_log("Erro: " . $e->getMessage());
    header("Location: homecontratos.php?error=db_error");
    exit;
}
?>
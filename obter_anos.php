<?php
$host = 'localhost';  
$dbname = 'gm_sicbd';  
$username = 'root';  
$password = '';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro ao conectar com o banco de dados: ' . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['contrato'])) {
        $contrato = $_POST['contrato'];

        try {
            // Consulta para pegar os anos cadastrados para o contrato
            $sql = "SELECT DISTINCT YEAR(data_cadastro) AS ano
                    FROM gestao_contratos
                    WHERE titulo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$contrato]);

            $anos = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $anos[] = $row['ano'];
            }

            echo json_encode($anos);
        } catch (Exception $e) {
            echo json_encode(['erro' => 'Erro ao obter anos: ' . $e->getMessage()]);
        }
    }
}
?>

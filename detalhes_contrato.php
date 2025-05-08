<?php
// detalhes_contrato.php
include 'db.php'; // Incluir a conexão com o banco de dados

if (isset($_GET['id'])) {
    $contratoId = $_GET['id'];

    try {
        // Preparar a consulta SQL para pegar os dados do contrato
        $sql = "SELECT * FROM gestao_contratos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $contratoId);
        $stmt->execute();

        // Verificar se o contrato foi encontrado
        $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($contrato) {
            // Retornar os dados do contrato como JSON
            echo json_encode($contrato);
        } else {
            echo json_encode(['error' => 'Contrato não encontrado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao buscar dados do contrato: ' . $e->getMessage()]);
    }
}
?>

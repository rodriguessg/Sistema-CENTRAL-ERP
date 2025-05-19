<?php
include 'banco.php'; // Incluir a conexão com o banco de dados

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
            // Retornar os dados do contrato
            echo json_encode($contrato); // Para debug, exibe como JSON
            // Exibir na interface de usuário
            echo "<h1>Detalhes do Contrato: " . $contrato['titulo'] . "</h1>";
            echo "<p><strong>Valor do contrato:</strong> R$ " . number_format($contrato['valor_contrato'], 2, ',', '.') . "</p>";
            echo "<p><strong>Gestor:</strong> " . $contrato['gestor'] . "</p>";
            // Mais detalhes aqui...
        } else {
            echo "<script>alert('Contrato não encontrado');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erro ao buscar dados do contrato: " . $e->getMessage() . "');</script>";
    }
}
?>

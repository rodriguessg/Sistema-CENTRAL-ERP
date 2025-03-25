<?php
// Função para contar notificações não lidas, podendo filtrar por usuário
function getUnreadNotificationsCount($user = null) {
    // Dados do banco de dados
    $host = 'localhost';
    $dbname = 'gm_sicbd';
    $dbUsername = 'root';
    $dbPassword = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbUsername, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($user) {
            // Consulta para contar notificações não lidas de um usuário específico
            $sql = "SELECT COUNT(*) as count FROM notificacoes WHERE situacao = 'não lida' AND username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $user);
        } else {
            // Consulta para contar todas as notificações não lidas
            $sql = "SELECT COUNT(*) as count FROM notificacoes WHERE situacao = 'não lida'";
            $stmt = $pdo->prepare($sql);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    } catch (PDOException $e) {
        error_log("Erro ao contar as notificações não lidas: " . $e->getMessage());
        return 0;
    }
}
?>


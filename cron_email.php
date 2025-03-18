<?php
// Configurações de conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}

include 'enviar_email.php';

$sql = "SELECT * FROM agendamentos WHERE data_g = CURDATE() AND enviado = 0";
$stmt = $pdo->query($sql);

while ($agenda = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (enviarEmail($agenda['email'], 'Lembrete de Agendamento', $agenda['descricao'])) {
        $pdo->prepare("UPDATE agendamentos SET enviado = 1 WHERE id = ?")
            ->execute([$agenda['id']]);
    }
}
?>

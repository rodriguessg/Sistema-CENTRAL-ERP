<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

$host = 'localhost';
$dbname = 'gm_sicbd';
$username_db = 'root';
$password = '';

try {
    // Criação da conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Definir modo de erro para exceção
} catch (PDOException $e) {
    // Em caso de erro na conexão, loga o erro e exibe uma mensagem amigável
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Consulte o administrador.");
}
if (isset($_GET['pesquisa'])) {
    $pesquisa = $_GET['pesquisa'];
    $query = "SELECT * FROM patrimonio WHERE nome LIKE ? OR codigo LIKE ?";
    $stmt = $con->prepare($query);
    $pesquisa = "%$pesquisa%";
    $stmt->bind_param('ss', $pesquisa, $pesquisa);
    $stmt->execute();
    $result = $stmt->get_result();

    $patrimonios = [];
    while ($row = $result->fetch_assoc()) {
        $patrimonios[] = $row;
    }

    echo json_encode($patrimonios);
}
?>

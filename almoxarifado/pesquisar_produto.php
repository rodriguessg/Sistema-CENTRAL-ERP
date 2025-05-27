<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_GET['pesquisa'])) {
    $pesquisa = $_GET['pesquisa'];
    $query = "SELECT * FROM produtos WHERE nome LIKE ? OR codigo LIKE ?";
    $stmt = $con->prepare($query);
    $pesquisa = "%$pesquisa%";
    $stmt->bind_param('ss', $pesquisa, $pesquisa);
    $stmt->execute();
    $result = $stmt->get_result();

    $patrimonios = [];
    while ($row = $result->fetch_assoc()) {
        $patrimonios[] = $row;
    }

    echo json_encode($produtos);
}
?>

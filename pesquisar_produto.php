<?php
include 'banco.php';

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

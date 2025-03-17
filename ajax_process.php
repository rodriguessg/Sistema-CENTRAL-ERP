<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'supat';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro na conexão com o banco de dados.']);
    exit;
}

// Verificar se o ID foi enviado via POST
if (isset($_POST['patrimonio_id'])) {
    $id_selecionado = $_POST['patrimonio_id'];
    $sql = "SELECT codigo, nome, valor, data_registro FROM patrimonio WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_selecionado]);
    $patrimonio = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retornar os dados em JSON
    if ($patrimonio) {
        echo json_encode($patrimonio);
    } else {
        echo json_encode(['error' => 'Patrimônio não encontrado.']);
    }
} else {
    echo json_encode(['error' => 'ID do patrimônio não foi enviado.']);
}
?>

<?php
// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'gm_sicbd');

// Verifica conexão
if ($conn->connect_error) {
    die(json_encode([]));
}

// Busca os dados da tabela funcionario
$sql = "SELECT id, nome, email, cargo, DATE_FORMAT(data_admissao, '%d/%m/%Y') as data_admissao FROM funcionario";
$result = $conn->query($sql);

$funcionarios = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $funcionarios[] = $row;
    }
}

// Retorna os dados como JSON
echo json_encode($funcionarios);

$conn->close();
?>

<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados do formulário
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $cor = $_POST['cor'];
    $convidado = $_POST['convidado'];
    $remetente = $_POST['remetente'];
    $status = $_POST['status'];
    $inicio = $_POST['inicio'];
    $termino = $_POST['termino'];
    $deletar_evento = isset($_POST['deletar_evento']) ? 1 : 0;

    // Prepara e executa a consulta SQL para inserir os dados
    $sql = "INSERT INTO agendamentoos (titulo, descricao, cor, convidado, remetente, status, inicio, termino, deletar_evento) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepara a consulta
    $stmt = $conn->prepare($sql);

    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt === false) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    // Vincula os parâmetros
    $stmt->bind_param("sssssssss", $titulo, $descricao, $cor, $convidado, $remetente, $status, $inicio, $termino, $deletar_evento);

    // Executa a consulta
    if ($stmt->execute()) {
        echo "Evento salvo com sucesso!";
    } else {
        echo "Erro ao salvar o evento: " . $stmt->error;
    }

    // Fecha a declaração e a conexão
    $stmt->close();
    $conn->close();
} else {
    echo "Nenhum dado foi enviado.";
}
?>

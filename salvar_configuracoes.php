<?php
session_start();
include 'banco.php';

// Verificar se os dados foram recebidos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter os dados do formulário
    $nomeSistema = $_POST['nome_sistema'];
    $emailSistema = $_POST['email_sistema'];
    $descricaoSistema = $_POST['descricao_sistema'];

    // Verificar se há um arquivo de logotipo
    if (isset($_FILES['logotipo_sistema']) && $_FILES['logotipo_sistema']['error'] == 0) {
        // Caminho onde o logotipo será salvo
        $logotipoTmp = $_FILES['logotipo_sistema']['tmp_name'];
        $logotipoName = $_FILES['logotipo_sistema']['name'];
        $logotipoPath = 'uploads/' . $logotipoName; // Caminho de upload (criar a pasta 'uploads' no diretório raiz)

        // Mover o arquivo para o diretório de uploads
        if (!move_uploaded_file($logotipoTmp, $logotipoPath)) {
            die("Erro ao fazer upload do logotipo.");
        }
    } else {
        $logotipoPath = ''; // Caso não tenha enviado um logotipo
    }

    // Atualizar as configurações no banco de dados
    $query = "UPDATE configuracoes SET 
                nome_sistema = ?, 
                email_sistema = ?, 
                descricao_sistema = ?, 
                logotipo_sistema = ? 
              WHERE id = 1";

    $stmt = $con->prepare($query);
    $stmt->bind_param('ssss', $nomeSistema, $emailSistema, $descricaoSistema, $logotipoPath);

    if ($stmt->execute()) {
        echo "<script>alert('Configurações salvas com sucesso!'); window.location.href = 'configuracao.php';</script>";
    } else {
        echo "<script>alert('Erro ao salvar as configurações.'); window.location.href = 'configuracao.php';</script>";
    }

    $stmt->close();
    $con->close();
} else {
    echo "<script>alert('Requisição inválida.'); window.location.href = 'configuracao.php';</script>";
}
?>

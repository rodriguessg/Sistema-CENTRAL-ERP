<?php
// Verifica se a variável 'destino' está setada na URL
if (!isset($_GET['destino'])) {
    header("Location: index.php"); // Caso contrário, redireciona para a página inicial ou uma página de erro.
    exit();
}

$destino = $_GET['destino'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Carregamento</title>
    <link rel="stylesheet" type="text/css" href="./src/style/loading.css">
</head>
<body>

    <div class="loading-container">
        <div class="logo-central">
            <img src="./src/img/Logo.png" alt="Logo" class="img-logo">
        </div>
        <div class="loading-bar">
            <div class="progress"></div>
        </div>
        <p class="loading-text">Carregando...</p>
    </div>

    <script>
        // Aguardar 3 segundos antes de redirecionar para a página do setor
        setTimeout(function() {
            // Redireciona para a página do setor após o tempo de carregamento
            window.location.href = "<?php echo $destino; ?>";
        }, 3000); // Tempo de 3 segundos de carregamento
    </script>

    <!-- <script src="./src/js/loading.js"></script> -->
</body>
</html>

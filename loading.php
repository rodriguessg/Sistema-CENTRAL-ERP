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
            <img src="./src/img/colo.png" alt="Logo" class="img-logo">
        </div>

<!-- Estilo de Bolinhas Pulsantes -->
 <div class="loading-balls">
        <div class="ball"></div>
        <div class="ball"></div>
        <div class="ball"></div>
        <div class="ball"></div>
        <div class="ball"></div>
        <div class="ball"></div>
    </div>


<div class="loading-text">
    <span id="dynamicText">Carregando</span>
</div>


    <script>
        // Aguardar 3 segundos antes de redirecionar para a página do setor
        setTimeout(function() {
            // Redireciona para a página do setor após o tempo de carregamento
            window.location.href = "<?php echo $destino; ?>";
        }, 7000); // Tempo de 3 segundos de carregamento
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const dynamicText = document.getElementById("dynamicText");

    const texts = ["Carregando", "Processando", "Aguarde..."]; // Textos a serem alternados
    let index = 0;

    setInterval(function () {
        dynamicText.textContent = texts[index]; // Atualiza o conteúdo do texto
        index = (index + 1) % texts.length; // Alterna entre os textos
    }, 2000); // Troca de texto a cada 3 segundos
});

    </script>

    <!-- <script src="./src/js/loading.js"></script> -->
</body>
</html>


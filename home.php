<?php
// Incluir o arquivo de conexão com o banco de dados
include 'banco.php';


// if(empty($_COOKIE['admin'])){ 
//     header("Location:index.php"); 
// }
// if(isset($_COOKIE['usuario'])){
//     header("Location:homeusuario.php");
// }
// if(isset($_COOKIE['tecnico'])){
//     header("Location:hometech.php");
// }
// Definir o código gerado e a categoria selecionada
$novoCodigo = "";
$categoriaSelecionada = "";

// Código PHP para gerar o código automaticamente
// Se necessário, implemente a lógica de geração de código baseada na categoria selecionada
// Aqui está apenas um exemplo de como gerar um código fictício.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Patrimônio</title>
    <link rel="stylesheet" href="./src/style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include 'header.php'; ?>

<!-- Menu das abas -->
<div class="tabs">
    <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">Cadastrar BP</div>
    <div class="tab" data-tab="retirar" onclick="showTab('retirar')">Movimentação BP</div>
    <div class="tab" data-tab="levantamento" onclick="showTab('levantamento')">Levantamento de Bens</div>
    <div class="tab" data-tab="DPRE" onclick="showTab('DPRE')">DPRE</div>
    <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">Relatorio</div>
</div>

 
</script>

<script src="src/js/script.js"></script>
</body>
</html>

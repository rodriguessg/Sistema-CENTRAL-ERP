<?php
session_start(); // Inicia a sessão
include 'banco.php'; // Inclui a conexão com o banco de dados, caso necessário

// Remove todos os cookies relacionados ao login, ajustando o caminho e o domínio
if (isset($_COOKIE['financeiro'])) {
    setcookie("financeiro", "", time() - 3600, "/"); // Define o cookie para expirar no passado
}

if (isset($_COOKIE['patrimonio'])) {
    setcookie("patrimonio", "", time() - 3600, "/"); // Define o cookie para expirar no passado
}

if (isset($_COOKIE['estoque'])) {
    setcookie("estoque", "", time() - 3600, "/"); // Define o cookie para expirar no passado
}

if (isset($_COOKIE['recursos_humanos'])) {
    setcookie("recursos_humanos", "", time() - 3600, "/"); // Define o cookie para expirar no passado
}


// Destroi a sessão
session_unset(); // Limpa todas as variáveis de sessão
session_destroy(); // Destroi a sessão

// Limpa o cache do navegador, caso necessário (garante que a página não seja carregada do cache)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

  header("Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=deslogado&pagina=/Sistema-CENTRAL-ERP/index.php");

exit; // Encerra o script após o redirecionamento
?>

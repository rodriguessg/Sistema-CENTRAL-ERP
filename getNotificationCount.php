<?php
// Configuração de conexão com o banco de dados
$servername = "localhost";  // Nome do servidor (para XAMPP, use localhost)
$username = "root";         // Usuário do banco de dados (padrão no XAMPP)
$password = "";             // Senha do banco de dados (no XAMPP, geralmente é vazio)
$dbname = "gm_sicbd";          // Nome do banco de dados que você criou

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se houve algum erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error); // Exibe a mensagem de erro se a conexão falhar
}

// Função para obter o número de notificações não lidas para um usuário
function getUnreadNotificationsCount($userId) {
    global $conn;  // Usar a conexão com o banco de dados

    // Verifica se a conexão foi realizada corretamente
    if (!$conn) {
        die("Erro: Não foi possível conectar ao banco de dados.");
    }

    // Preparar a consulta SQL para contar as notificações não lidas
    $query = "SELECT COUNT(*) FROM notificacoes WHERE user_id = ? AND status = 'nao lida'";
    $stmt = $conn->prepare($query);  // Prepara a consulta

    // Verifica se a consulta foi preparada corretamente
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // Associar o parâmetro da consulta
    $stmt->bind_param("i", $userId);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado da consulta
    $stmt->bind_result($count);
    $stmt->fetch();

    return $count;  // Retorna o número de notificações não lidas
}

// Exemplo de uso
$userId = 1;  // ID do usuário (exemplo)
$unreadCount = getUnreadNotificationsCount($userId);
echo "" . $unreadCount;
?>

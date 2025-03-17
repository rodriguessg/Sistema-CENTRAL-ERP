<?php
// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura as informações do formulário
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $usuario = $_POST['usuario'];

    // Função para enviar o reporte para o GLPI via API (Exemplo)
    $url_glpi = 'https://seusistema-glpi/api/ticket';
    $api_key = 'SUA_API_KEY_GLPI'; // Substitua com sua chave de API do GLPI
    $auth_token = 'SEU_TOKEN_AUTENTICACAO'; // Substitua com seu token de autenticação

    // Dados do ticket a ser enviado
    $ticket_data = [
        'input' => [
            'name' => $titulo,
            'content' => $descricao,
            'requester' => $usuario,
            'status' => 1, // Status "Novo" ou "Aberto"
            'priority' => 3, // Prioridade média
        ]
    ];

    // Configuração para enviar o ticket para a API do GLPI
    $ch = curl_init($url_glpi);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ticket_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: UserToken ' . $auth_token,
        'App-Token: ' . $api_key,
        'Content-Type: application/json'
    ]);
    
    // Envia o ticket para o GLPI
    $response = curl_exec($ch);
    curl_close($ch);

    // Verifica a resposta da API
    if ($response) {
        $response_data = json_decode($response, true);
        $ticket_id = $response_data['id'] ?? null;
        echo $ticket_id ? 'Erro reportado com sucesso. ID do ticket: ' . $ticket_id : 'Erro ao reportar o erro.';
    } else {
        echo 'Erro na comunicação com o GLPI.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportar Erro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Reportar Erro</h1>
        <form action="report.php" method="POST">
            <div class="form-group">
                <label for="titulo">Título do Erro:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição do Erro:</label>
                <textarea id="descricao" name="descricao" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="usuario">Seu Nome:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <button type="submit">Enviar Reporte</button>
            </div>
        </form>
    </div>
</body>
</html>

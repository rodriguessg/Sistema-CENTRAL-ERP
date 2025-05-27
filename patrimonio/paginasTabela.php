<?php
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    // Criação da conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Definir modo de erro para exceção
} catch (PDOException $e) {
    // Em caso de erro na conexão, loga o erro e exibe uma mensagem amigável
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Consulte o administrador.");
}

// Definir o número de itens por página
$itens_por_pagina = 5;

// Obter o número da página atual (padrão para 1)
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Calcular o OFFSET para a consulta
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// Consulta SQL com LIMIT e OFFSET
$sql = "SELECT id, nome, descricao, valor, localizacao, situacao, cadastrado_por, categoria, codigo , tipo_operacao
        FROM patrimonio 
        LIMIT :itens_por_pagina OFFSET :offset";

// Preparar a consulta
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':itens_por_pagina', $itens_por_pagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

// Consulta para contar o número total de registros
$sql_total = "SELECT COUNT(*) as total FROM patrimonio";
$total_result = $pdo->query($sql_total);
$total_registros = $total_result->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular o total de páginas
$total_paginas = ceil($total_registros / $itens_por_pagina);

// Dados em JSON para o JavaScript
$dados = [];
if ($stmt && $stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dados[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode([
    'dados' => $dados,
    'total_paginas' => $total_paginas
]);
exit;
?>

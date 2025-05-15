<?php
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $titulo = $data['titulo'] ?? null;
    $num_parcelas = $data['num_parcelas'] ?? null;

    if (!$titulo || !is_numeric($num_parcelas)) {
        throw new Exception('Parâmetros inválidos');
    }

    // Conexão com o banco de dados (substitua pelos seus dados)
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Atualizar num_parcelas
    $stmt = $pdo->prepare('UPDATE contratos SET num_parcelas = :num_parcelas WHERE titulo = :titulo');
    $stmt->execute([
        ':num_parcelas' => $num_parcelas,
        ':titulo' => $titulo
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
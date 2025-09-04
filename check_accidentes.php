<?php
// Ensure no output before this point
ob_start();

// Definir fuso horário de São Paulo (BRT, UTC-3)
date_default_timezone_set('America/Sao_Paulo');

// Database configuration
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar acidentes com status 'em andamento'
    $stmt_check = $pdo->query("SELECT COUNT(*) as total FROM acidentes WHERE status = 'em andamento'");
    $em_andamento = $stmt_check->fetch(PDO::FETCH_ASSOC)['total'];

    // Verificar acidentes com status 'resolvido'
    $stmt_resolved = $pdo->query("SELECT COUNT(*) as total FROM acidentes WHERE status = 'resolvido'");
    $resolvido = $stmt_resolved->fetch(PDO::FETCH_ASSOC)['total'];

    // Clear output buffer and send JSON
    ob_end_clean();
    echo json_encode([
        'em_andamento' => $em_andamento,
        'resolvido' => $resolvido
    ]);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
}
?>
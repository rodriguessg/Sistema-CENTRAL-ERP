<?php
// Conexão com o banco de dados
include 'banco.php'; // Inclua a conexão ao banco

// Consulta para buscar todas as movimentações na tabela log_eventos
$query = "SELECT id, matricula, tipo_operacao, data_operacao FROM log_eventos ORDER BY data_operacao DESC";

try {
    $result = $con->query($query);

    // Verifica se há registros
    if ($result->num_rows > 0) {
        $logEventos = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $logEventos = []; // Nenhum registro encontrado
    }
} catch (Exception $e) {
    echo "Erro ao consultar a tabela log_eventos: " . $e->getMessage();
    exit();
}

// Fecha a conexão
$con->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Movimentações</title>
     <link rel="stylesheet" href="src/style/log.css">
   
</head>
<body>
    <div class="header">Histórico de Movimentações</div>

    <div class="container">
        <?php if (empty($logEventos)): ?>
            <p class="no-data">Nenhuma movimentação registrada no sistema.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Matrícula</th>
                        <th>Tipo de Operação</th>
                        <th>Data da Operação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logEventos as $evento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($evento['id']); ?></td>
                            <td><?php echo htmlspecialchars($evento['matricula']); ?></td>
                            <td><?php echo htmlspecialchars($evento['tipo_operacao']); ?></td>
                            <td><?php echo htmlspecialchars($evento['data_operacao']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>

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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 24px;
            font-weight: bold;
        }

        .container {
            margin: 20px auto;
            width: 80%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        thead th {
            padding: 10px;
            text-align: left;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody td {
            padding: 10px;
            text-align: left;
        }

        tbody tr:hover {
            background-color: #f9f9f9;
        }

        .no-data {
            text-align: center;
            font-size: 18px;
            color: #666;
            margin-top: 20px;
        }
    </style>
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

<?php
session_start();

// Conexão com o banco
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $dbname);
include 'header.php';

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica sessão
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

// Ativa erros PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Consulta acidentes
$sql = "SELECT descricao, localizacao, severidade, categoria, usuario, cor, data_registro FROM acidentes ORDER BY data_registro DESC";
$result = $conn->query($sql);

// Verifica se a consulta foi executada corretamente
if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}

// Verifica se há dados
$temDados = ($result instanceof mysqli_result && $result->num_rows > 0);

// Map colors to CSS classes
$colorClasses = [
    'Verde' => 'severity-green',
    'Amarelo' => 'severity-yellow',
    'Vermelho' => 'severity-red',
    'Amarelo/Vermelho' => 'severity-yellow-red'
];

// Determine the CSS class for each row's severity based on the 'cor' column
function getSeverityClass($cor, $colorClasses) {
    return isset($colorClasses[$cor]) ? $colorClasses[$cor] : '';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Acidentes</title>
    <!-- Aplicando o CSS moderno do anexo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        /* ===== RESET E BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
            font-size: 14px;
        }

        .container {
            padding: 0.75rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ===== HEADER ===== */
        .header-section {
            background: linear-gradient(135deg, #192844 0%, #472774 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .header-section h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        /* ===== SEÇÃO DE FORMULÁRIO ===== */
        .form-section {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            margin: 0.5rem 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .form-section:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .button-container {
            padding: 1.25rem;
            text-align: center;
        }

        .button-container button {
            background: #192844 !important;
            color: #ffffff !important;
            border: none !important;
            padding: 12px 24px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
            font-family: "Inter", sans-serif !important;
            text-decoration: none !important;
            outline: none !important;
            min-height: 44px !important;
        }

        .button-container button:hover {
            background: #472774 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        /* ===== SEÇÕES DE TABELA ===== */
        .accidents-table {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin: 0.75rem 0;
        }

        .accidents-table h2 {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .accidents-table h2::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, #192844 0%, #472774 100%);
        }

        /* ===== TABELAS ===== */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #ffffff;
            font-size: 0.875rem;
        }

        table thead {
            background: linear-gradient(135deg, #192844 0%, #472774 100%);
        }

        table th {
            color: white;
            padding: 0.75rem;
            text-align: center;
            font-weight: 700;
            font-size: 0.875rem;
            border: none;
            white-space: nowrap;
        }

        table th:first-child {
            border-radius: 6px 0 0 0;
        }

        table th:last-child {
            border-radius: 0 6px 0 0;
        }

        table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f3f4f6;
            color: #1f2937;
            vertical-align: middle;
            text-align: left;
            font-weight: 500;
        }

        table tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transition: all 0.15s ease;
        }

        table tr:nth-child(even) {
            background: rgba(248, 250, 252, 0.5);
        }

        table tr:nth-child(even):hover {
            background: rgba(102, 126, 234, 0.05);
        }

        /* ===== SEVERIDADE ===== */
        .severity-green {
            background-color: #10b981;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-block;
            min-width: 80px;
        }

        .severity-yellow {
            background-color: #f59e0b;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-block;
            min-width: 80px;
        }

        .severity-red {
            background-color: #ef4444;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-block;
            min-width: 80px;
        }

        .severity-yellow-red {
            background: linear-gradient(to right, #f59e0b, #ef4444);
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-block;
            min-width: 80px;
        }

        .no-data {
            text-align: center;
            padding: 2rem 1rem;
            color: #9ca3af;
            font-style: italic;
            font-size: 1rem;
        }

        /* ===== ZONAS DE ATENÇÃO ===== */
        .attention-zones {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin: 0.75rem 0;
            padding: 1.25rem;
        }

        .attention-zones h3 {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            margin: -1.25rem -1.25rem 1rem -1.25rem;
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .attention-zones h3::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .attention-zones ul {
            list-style: none;
            padding: 0;
        }

        .attention-zones li {
            padding: 0.5rem 0;
            color: #1f2937;
            font-weight: 500;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .attention-zones li:last-child {
            border-bottom: none;
        }

        .attention-zones li::before {
            content: "⚠️";
            font-size: 1rem;
        }

        /* ===== ÍCONES ===== */
        .icon {
            width: 16px;
            height: 16px;
            stroke-width: 2;
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 768px) {
            .container {
                padding: 0.5rem;
            }

            table {
                font-size: 0.75rem;
            }

            table th,
            table td {
                padding: 0.5rem;
            }

            .header-section h1 {
                font-size: 1.25rem;
            }

            .accidents-table h2 {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0.25rem;
            }

            table th,
            table td {
                padding: 0.25rem;
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="caderno">
        <div class="header-section">
            <h1>
                <i data-lucide="alert-triangle" class="icon"></i>
                Relatório de Acidentes
            </h1>
        </div>

        <div class="form-section">
            <div class="button-container">
                <button onclick="window.location.href='reportacidentes.php'">
                    <i data-lucide="plus" class="icon"></i>
                    Registrar Novo Acidente
                </button>
            </div>
        </div>

        <div class="accidents-table">
            <h2>
                <i data-lucide="file-text" class="icon"></i>
                Histórico de Acidentes
            </h2>

            <?php if ($temDados): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Localização</th>
                            <th>Severidade</th>
                            <th>Categoria</th>
                            <th>Data de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['descricao']) ?></td>
                                <td><?= htmlspecialchars($row['localizacao']) ?></td>
                                <td>
                                    <span class="<?= getSeverityClass($row['cor'], $colorClasses) ?>">
                                        <?= htmlspecialchars($row['severidade']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['categoria']) ?></td>
                                <td><?= htmlspecialchars($row['data_registro']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i data-lucide="info" class="icon"></i>
                    Nenhum acidente registrado.
                </div>
            <?php endif; ?>
        </div>

        <div class="attention-zones">
            <h3>
                <i data-lucide="map-pin" class="icon"></i>
                Zonas de Atenção
            </h3>
            <ul>
                <li>Almoxarifado Central - Alto risco de quedas.</li>
                <li>Setor de Produção - Equipamentos instáveis.</li>
                <li>Área de Estocagem - Acúmulo de materiais inflamáveis.</li>
            </ul>
        </div>
    </div>

    <?php $conn->close(); ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>

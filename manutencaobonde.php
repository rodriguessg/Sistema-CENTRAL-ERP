<?php
session_start();

// Conexão com o banco
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';
$conn = new mysqli($host, $user, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica sessão
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

$erro = '';
$sucesso = '';

// Consulta os bondes disponíveis
$bondes = [];
$sql_bondes = "SELECT id, modelo FROM bondes";
$result_bondes = $conn->query($sql_bondes);
if ($result_bondes) {
    while ($row = $result_bondes->fetch_assoc()) {
        $bondes[$row['id']] = $row['modelo'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data'] ?? date('Y-m-d');
    $tipo = $_POST['tipo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $bonde_afetado_id = $_POST['bonde_afetado'] ?? '';
    $localidade = $_POST['localidade'] ?? '';
    $status = $_POST['status'] ?? 'Pendente';

    // Validação: verifica se o bonde selecionado existe
    if (!array_key_exists($bonde_afetado_id, $bondes)) {
        $erro = "Bonde selecionado inválido!";
    } elseif (empty($data) || empty($tipo) || empty($descricao)) {
        $erro = "Todos os campos obrigatórios devem ser preenchidos!";
    } else {
        $sql = "INSERT INTO manutencoes (data, tipo, descricao, bonde_afetado, localidade, status, usuario, data_registro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $bonde_afetado = $bondes[$bonde_afetado_id] ?? 'Desconhecido'; // Usa o modelo como valor
        $stmt->bind_param("sssssss", $data, $tipo, $descricao, $bonde_afetado, $localidade, $status, $username);

        if ($stmt->execute()) {
            $sucesso = "Manutenção registrada com sucesso!";
        } else {
            $erro = "Erro ao registrar a manutenção: " . $conn->error;
        }
        $stmt->close();
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Manutenção de Bondes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide@latest/dist/umd/lucide.js" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc; /* Changed to clean white background */
            min-height: 100vh;
            padding: 20px;
        }

        .header {
            background: #1e293b; /* Changed to solid dark blue header */
            color: white;
            padding: 24px 30px;
            text-align: center;
            position: relative;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 500;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .form-container {
            padding: 48px; /* Increased padding for larger containers */
        }

        .form-group {
            margin-bottom: 28px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input, textarea, select {
            width: 100%;
            padding: 16px 20px; /* Increased padding for larger input fields */
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            background: white;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        select {
            cursor: pointer;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%236b7280" stroke-width="2"><polyline points="6,9 12,15 18,9"/></svg>');
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            appearance: none;
        }

        .btn {
            background: #1e293b; /* Made button smaller and more standard size */
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: auto; /* Removed full width */
            font-family: 'Inter', sans-serif;
        }

        .btn:hover {
            background: #334155;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 41, 59, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert.error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert.success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .button-container { /* Added button container for proper alignment */
            display: flex;
            justify-content: flex-end;
            margin-top: 32px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 12px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 18px;
            }
            
            .form-container {
                padding: 32px 24px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        .icon {
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    <div class="caderno">
        <div class="header">
            <h1>
                <i data-lucide="plus-circle" class="icon"></i>
                Registro de Manutenção de Bondes
            </h1>
        </div>
        
        <div class="form-container">
            <?php if ($erro): ?>
                <div class="alert error">
                    <i data-lucide="alert-circle" class="icon"></i>
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="alert success">
                    <i data-lucide="check-circle" class="icon"></i>
                    <?php echo $sucesso; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="data">
                        <i data-lucide="calendar" class="icon"></i>
                        Data da Manutenção:
                    </label>
                    <input type="date" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo">
                            <i data-lucide="settings" class="icon"></i>
                            Tipo de Manutenção:
                        </label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Selecione o tipo</option>
                            <option value="Preventiva">Preventiva</option>
                            <option value="Realizada">Realizada</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">
                            <i data-lucide="activity" class="icon"></i>
                            Status:
                        </label>
                        <select id="status" name="status" required>
                            <option value="">Selecione o status</option>
                            <option value="Pendente">Pendente</option>
                            <option value="Em Andamento">Em Andamento</option>
                            <option value="Concluída">Concluída</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="descricao">
                        <i data-lucide="file-text" class="icon"></i>
                        Descrição:
                    </label>
                    <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva os detalhes da manutenção"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="bonde_afetado">
                            <i data-lucide="train" class="icon"></i>
                            Bonde Afetado:
                        </label>
                        <select id="bonde_afetado" name="bonde_afetado" required>
                            <option value="">Selecione o bonde</option>
                            <?php foreach ($bondes as $id => $modelo): ?>
                                <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($modelo, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="localidade">
                            <i data-lucide="map-pin" class="icon"></i>
                            Localidade:
                        </label>
                        <input type="text" id="localidade" name="localidade" placeholder="Ex: Depósito Central">
                    </div>
                </div>
                
                <div class="button-container"> <!-- Added button container and changed button alignment -->
                    <button type="submit" class="btn">
                        <i data-lucide="save" class="icon"></i>
                        Salvar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>

    <?php $conn->close(); ?>
</body>
</html>

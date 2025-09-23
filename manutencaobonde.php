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

// Busca o ID do usuário logado
$user_id = null;
$sql_user = "SELECT id FROM usuario WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);
if ($stmt_user === false) {
    die("Erro na preparação da query de usuário: " . $conn->error);
}
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($row = $result_user->fetch_assoc()) {
    $user_id = $row['id'];
}
$stmt_user->close();
if (!$user_id) {
    die("Erro: Usuário não encontrado no sistema!");
}

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
    $tipo = strtolower($_POST['tipo'] ?? '');
    $descricao = $_POST['descricao'] ?? '';
    $bonde_afetado_id = (int)$_POST['bonde_afetado'] ?? 0;
    $localidade = $_POST['localidade'] ?? '';
    $status = strtolower($_POST['status'] ?? 'pendente');

    // Validação: verifica se o bonde selecionado existe
    if (!array_key_exists($bonde_afetado_id, $bondes)) {
        $erro = "Bonde selecionado inválido!";
    } elseif (empty($data) || empty($tipo) || empty($descricao)) {
        $erro = "Todos os campos obrigatórios devem ser preenchidos!";
    } elseif (!in_array($tipo, ['preventiva', 'corretiva', 'emergencia'])) {
        $erro = "Tipo de manutenção inválido!";
    } elseif (!in_array($status, ['pendente', 'em_andamento', 'concluida', 'cancelada'])) {
        $erro = "Status inválido!";
    } else {
        $sql = "INSERT INTO manutencoes (data, tipo, descricao, bonde_afetado, localidade, status, usuario, data_registro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssi", $data, $tipo, $descricao, $bonde_afetado_id, $localidade, $status, $user_id);

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
        /* ===== VARIÁVEIS CSS ===== */
        :root {
            /* Cores Principais */
            --primary-color: #192844;
            --secondary-color: #472774;
            --accent-color: #667eea;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;

            /* Gradientes */
            --primary-gradient: linear-gradient(135deg, #192844 0%, #472774 100%);
            --secondary-gradient: linear-gradient(135deg, #472774 0%, #6a4c93 100%);
            --accent-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --glass-gradient: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);

            /* Cores de Texto */
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --text-light: #d1d5db;

            /* Cores de Fundo */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --bg-dark: #0f172a;

            /* Bordas e Sombras */
            --border-color: #e5e7eb;
            --border-light: #f3f4f6;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);

            /* Raios de Borda */
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;

            /* Transições */
            --transition-fast: all 0.15s ease;
            --transition-normal: all 0.3s ease;
            --transition-slow: all 0.5s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
        }

        .header {
            background: var(--primary-gradient);
            color: white;
            padding: 20px 20px;
            position: relative;
            border-radius: 20px 20px 0px 0px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 600;
        }

        .header::after {
            content: 'Sistema de controle de manutenção dos bondes de Santa Teresa';
            position: absolute;
            bottom: 5px;
            left: 40px;
            font-size: 14px;
            color: #bfdbfe;
            font-weight: 400;
        }

        .form-container {
            padding: 20px;
            background: white;
            border: 1px solid #e5e7eb;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: #374151;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            background: white;
        }

        input[type="number"], input[type="text"], input[type="date"] {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }

        input[type="number"]:focus, input[type="text"]:focus, input[type="date"]:focus {
            background: white;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        select {
            cursor: pointer;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%236b7280" stroke-width="2"><polyline points="6,9 12,15 18,9"/></svg>');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            appearance: none;
        }

        .btn {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Inter', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .btn:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.25);
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
            border-left: 4px solid;
        }

        .alert.error {
            background: #fef2f2;
            color: #dc2626;
            border-color: #dc2626;
        }

        .alert.success {
            background: #f0fdf4;
            color: #16a34a;
            border-color: #16a34a;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-left: 0px;
        }

        .button-container {
            display: flex;
            margin-top: 20px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            .caderno {
                margin: 10px;
                border-radius: 8px;
            }
            
            .header {
                padding: 16px 20px;
            }
            
            .header h1 {
                font-size: 16px;
                padding-left: 40px;
            }
            
            .header::after {
                left: 60px;
                font-size: 11px;
            }
            
            .form-container {
                padding: 24px 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        .icon {
            width: 16px;
            height: 16px;
            opacity: 0.8;
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
                    <input type="date" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo">
                            <i data-lucide="settings" class="icon"></i>
                            Tipo de Manutenção:
                        </label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Selecione o tipo</option>
                            <option value="preventiva">Preventiva</option>
                            <option value="corretiva">Corretiva</option>
                            <option value="emergencia">Emergência</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">
                            <i data-lucide="activity" class="icon"></i>
                            Status:
                        </label>
                        <select id="status" name="status" required>
                            <option value="">Selecione o status</option>
                            <option value="pendente">Pendente</option>
                            <option value="em_andamento">Em Andamento</option>
                            <option value="concluida">Concluída</option>
                            <option value="cancelada">Cancelada</option>
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
                
                <div class="button-container">
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
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
    <link rel="stylesheet" href="src/estoque/style/estoque2.css">
    <link rel="stylesheet" href="src/estoque/style/linhadotempo.css">
    <link rel="stylesheet" href="src/style/tabs.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error { color: #e74c3c; font-weight: bold; margin-top: 10px; }
        .success { color: #2ecc71; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Registro de Manutenção de Bondes</h1>
    </div>
    <div class="container">
        <?php if ($erro): ?>
            <p class="error"><?php echo $erro; ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <p class="success"><?php echo $sucesso; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="data">Data da Manutenção:</label>
                <input type="date" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo de Manutenção:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Selecione o tipo</option>
                    <option value="Preventiva">Preventiva</option>
                    <option value="Realizada">Realizada</option>
                </select>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva os detalhes da manutenção"></textarea>
            </div>
            <div class="form-group">
                <label for="bonde_afetado">Bonde Afetado:</label>
                <select id="bonde_afetado" name="bonde_afetado" required>
                    <option value="">Selecione o bonde</option>
                    <?php foreach ($bondes as $id => $modelo): ?>
                        <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($modelo, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="localidade">Localidade:</label>
                <input type="text" id="localidade" name="localidade" placeholder="Ex: Depósito Central">
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="">Selecione o status</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Em Andamento">Em Andamento</option>
                    <option value="Concluída">Concluída</option>
                </select>
            </div>
            <button type="submit">Salvar Registro</button>
        </form>
    </div>

    <?php $conn->close(); ?>
</body>
</html>
<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data'] ?? date('Y-m-d');
    $descricao = $_POST['descricao'] ?? '';
    $localizacao = $_POST['localizacao'] ?? '';
    $severidade = $_POST['severidade'] ?? '';
    $categoria = $_POST['categoria'] ?? '';

    if (empty($descricao)  || empty($severidade) || empty($categoria)) {
        $erro = "Todos os campos obrigatórios devem ser preenchidos e a quantidade deve ser maior que zero!";
    } else {
     $sql = "INSERT INTO acidentes (data, descricao, localizacao, usuario, severidade, categoria, data_registro) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação da query: " . $conn->error);
}

$stmt->bind_param("ssssss", $data, $descricao, $localizacao, $username, $severidade, $categoria);

if ($stmt->execute()) {
    $sucesso = "Acidente registrado com sucesso!";
    header("Location: relatorioacidentes.php?success=1");
    exit();
} else {
    $erro = "Erro ao registrar o acidente: " . $stmt->error;
}
$stmt->close();

}}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Acidente</title>
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
        <h1>Registrar Acidente</h1>
    </div>
    <div class="container">
        <?php if ($erro): ?>
            <p class="error"><?php echo $erro; ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <p class="success"><?php echo $sucesso; ?></p>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="localizacao">Localização:</label>
                <input type="text" id="localizacao" name="localizacao" placeholder="Ex: Largo do Curvel, Copacabana, Carioca, próximo ao poste 13 ...">
            </div>
            
            <div class="form-group">
                <label for="data">Data do Acidente:</label>
                <input type="date" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição do Acidente:</label>
                <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva o acidente, danos, envolvidos, e ações tomadas"></textarea>
            </div>
           
            <div class="form-group">
                <label for="severidade">Severidade:</label>
                <select id="severidade" name="severidade" required>
                    <option value="">Selecione a severidade</option>
                    <option value="Leve">Leve</option>
                    <option value="Moderado">Moderado</option>
                    <option value="Grave">Grave</option>
                </select>
            </div>

            <div class="form-group">
                <label for="categoria">Categoria:</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Selecione a categoria</option>
                    <option value="Descarrilamento">Descarrilamento</option>
                    <option value="Colisão">Colisão</option>
                    <option value="Incidente com Passageiro">Incidente com Passageiro</option>
                    <option value="Falha Mecânica">Falha Mecânica</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>

            <div class="form-group">
                <label for="upload">Anexar Imagens (Opcional):</label>
                <input type="file" id="upload" name="upload" accept="image/*" multiple>
            </div>
            
            <button type="submit">Salvar Registro</button>
        </form>
    </div>

    <?php $conn->close(); ?>
</body>
</html>
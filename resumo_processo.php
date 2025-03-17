<?php
// Configuração do banco de dados
$host = 'localhost'; // ou o endereço do seu servidor de banco de dados
$dbname = 'supat'; // Nome do banco de dados
$username = 'root'; // Usuário do banco de dados
$password = ''; // Senha do banco de dados

try {
    // Conexão com o banco de dados usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Definir o modo de erro do PDO para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se ocorrer um erro na conexão, exibe a mensagem
    echo "Erro de conexão: " . $e->getMessage();
    exit;
}

// Suponha que o ID do processo seja passado via GET na URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consultando informações do processo com o ID
    $sql = "SELECT * FROM gestao_contratos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $processo = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Caso o ID não seja passado na URL, redireciona ou exibe um erro
    echo "Processo não encontrado!";
    exit;
}
include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumo do Processo</title>
    <!-- Inclua o CSS necessário (por exemplo, Bootstrap ou o estilo personalizado) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <!-- Cabeçalho da página -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Resumo do Processo</h2>
            <div>
                <a href="relatorio.php?id=<?php echo $processo['id']; ?>" class="btn btn-primary btn-sm">Relatório</a>
                <a href="editar_processo.php?id=<?php echo $processo['id']; ?>" class="btn btn-success btn-sm">Editar Processo</a>
            </div>
        </div>

        <!-- Informações do processo -->
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Processo</th>
                            <td><?php echo $processo['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Natureza da Ação</th>
                            <td><?php echo $processo['natureza_acao']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Cliente (Adverso)</th>
                            <td><?php echo $processo['cliente']; ?></td>
                        </tr>
                        <tr>
                            <th>Advogado</th>
                            <td><?php echo $processo['advogado']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts necessários -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<?php
include 'footer.php'
?>
</body>
</html>

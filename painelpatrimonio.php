<?php
include 'banco.php'; // Inclua a conexão com o banco de dados

// Consultas para contar os diferentes tipos de bens
$totalBensQuery = "SELECT COUNT(*) AS total FROM patrimonio";
$ativosQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'ativo'";
$emBaixaQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'Em Processo de baixa'";
$mortosQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'inativo'";

// Consulta para obter os últimos patrimônios cadastrados
$ultimosPatrimoniosQuery = "SELECT id, codigo, nome, descricao, data_registro, situacao AS status FROM patrimonio ORDER BY data_registro DESC LIMIT 5";

// Consulta para obter os usuários que mais cadastraram patrimônio no mês atual
$query_usuarios_mes = "
    SELECT p.cadastrado_por AS usuario, u.setor, COUNT(*) AS quantidade_cadastros
    FROM patrimonio p
    INNER JOIN usuario u ON p.cadastrado_por = u.username  -- Junção com a tabela usuario
    WHERE MONTH(p.data_registro) = MONTH(CURRENT_DATE()) 
      AND YEAR(p.data_registro) = YEAR(CURRENT_DATE())
      AND p.cadastrado_por IS NOT NULL
    GROUP BY p.cadastrado_por, u.setor  -- Agrupar também pelo setor
    ORDER BY quantidade_cadastros DESC
    LIMIT 5
";

$result_usuarios_mes = $con->query($query_usuarios_mes);

try {
    // Executa as consultas e obtém os resultados
    $totalBens = $con->query($totalBensQuery)->fetch_assoc()['total'] ?? 0;
    $bensAtivos = $con->query($ativosQuery)->fetch_assoc()['total'] ?? 0;
    $bensEmBaixa = $con->query($emBaixaQuery)->fetch_assoc()['total'] ?? 0;
    $bensMortos = $con->query($mortosQuery)->fetch_assoc()['total'] ?? 0;

    // Executa a consulta para os últimos patrimônios
    $result = $con->query($ultimosPatrimoniosQuery);
    $ultimosPatrimonios = [];
    if ($result) {
        $ultimosPatrimonios = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    echo "Erro ao consultar o banco de dados: " . $e->getMessage();
    exit();
}

// Fecha a conexão
include 'header.php';
$con->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Bens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        /* Estilos do cabeçalho */
        .header {
            background-color: #007bff; /* Azul */
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 24px;
            font-weight: bold;
        }

        /* Container principal */
        .dashboard-container {
            display: flex;
            width: 100%;
            height: 100vh;
            gap: 20px;
            padding: 20px;
        }

        /* Lado esquerdo: Cards */
        .cards-container {
            width: 98%;
            margin-top: 49px;
            display: flex;
            gap: 20px;
            flex-basis: fit-content;
        }

        
        .card {
            width: 48%;
            height: 143px;
            background: linear-gradient(135deg, #ffffff, #ff7f50); /* Branco para laranja */
            border: 1px solid #ddd;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card2 {
            width: 48%;
            height: 143px;
            background: linear-gradient(135deg, #ffffff, #28a745); /* Branco para verde */
            border: 1px solid #ddd;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card3 {
            width: 48%;
            height: 143px;
            background: linear-gradient(135deg, #fffacd, #ffdd00); /* Branco suave para amarelo */
            border: 1px solid #ddd;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card4 {
            width: 48%;
            height: 143px;
            background: linear-gradient(135deg, #ff4d4d, #b30000);
            border: 1px solid #ddd;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card h3,
        .card2 h3,
        .card3 h3,
        .card4 h3 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
            color: #333;
        }


        .card p,
         .card2 p, 
         .card3 p,
          .card4 p {
            font-size: 1.5rem;
            color: #333;
        }
        

        /* Lado direito: Tabela */
        .table-container {
            width: 50%;
            max-height: 70%; /* Limita a altura máxima a 20% */
            overflow-y: auto; /* Permite rolagem vertical quando necessário */
            padding: 10px; /* Adiciona espaço interno */
            box-sizing: border-box; /* Inclui o padding dentro da largura/altura */
        }

        .table-container h3 {
            margin-bottom: 10px;
            font-size: 1.5rem;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
        .status-ativo {
    background-color: green;
    color: white;
    padding: 2px 8px;  /* Ajuste o padding para que o fundo seja proporcional ao texto */
    border-radius: 5px;
    text-align: center;
}

.status-inativo {
    background-color: red;
    color: white;
    padding: 2px 8px;  /* Ajuste o padding */
    border-radius: 5px;
    text-align: center;
}

.status-em-baixa {
    background-color: yellow;
    color: black;
    padding: 2px 8px;  /* Ajuste o padding */
    border-radius: 5px;
    text-align: center;
}

    </style>
</head>
<body>

    <!-- Lado esquerdo: Cards -->
<div class="cards-container">
        <div class="card">
            <h3>Total de Bens Cadastrados</h3>
            <p><?php echo $totalBens; ?></p>
        </div>
        <div class="card2">
            <h3>Bens Ativos</h3>
            <p><?php echo $bensAtivos; ?></p>
        </div>
        <div class="card3">
            <h3>Bens em Processo de Baixa</h3>
            <p><?php echo $bensEmBaixa; ?></p>
        </div>
        <div class="card4">
            <h3>Bens Mortos</h3>
            <p><?php echo $bensMortos; ?></p>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Lado direito: Tabela -->
        <div class="table-container">
            <h3>Últimos Patrimônios Cadastrados</h3>
            <table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Data de Cadastro</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ultimosPatrimonios as $patrimonio): ?>
            <tr>
                <td><?php echo htmlspecialchars($patrimonio['codigo']); ?></td>
                <td><?php echo htmlspecialchars($patrimonio['nome']); ?></td>
                <td><?php echo htmlspecialchars($patrimonio['descricao']); ?></td>
                <td><?php echo htmlspecialchars($patrimonio['data_registro']); ?></td>
                <td>
                    <span class="
                        <?php 
                            if ($patrimonio['status'] == 'ativo') {
                                echo 'status-ativo';
                            } elseif ($patrimonio['status'] == 'inativo') {
                                echo 'status-inativo';
                            } elseif ($patrimonio['status'] == 'Em Processo de baixa') {
                                echo 'status-em-baixa';
                            }
                        ?>
                    ">
                        <?php echo htmlspecialchars($patrimonio['status']); ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

        </div>

        <!-- Tabela de usuários que mais cadastraram patrimônio no mês -->
<div class="table-container">
            <h3>Usuários que Mais Cadastraram Bens no mês</h3>
            <table>
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Setor</th>
                        <th>Status</th>
                        <th>Quantidade de Cadastros</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $result_usuarios_mes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['setor']); ?></td>
                            <td>
                    <span class="
                        <?php 
                            if ($patrimonio['status'] == 'ativo') {
                                echo 'status-ativo';
                            } elseif ($patrimonio['status'] == 'inativo') {
                                echo 'status-inativo';
                            } 
                            
                        ?>
                    ">
                        <?php echo htmlspecialchars($patrimonio['status']); ?>
                    </span>
                </td>
                            <td><?php echo htmlspecialchars($usuario['quantidade_cadastros']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
<?php include 'footer.php'; ?>
<?php
// Verifica autenticação
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Cabeçalho HTML
include 'header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Setor Financeiro</title>
    <link rel="stylesheet" href="./src/style/style.css">
    <script>
        // Função para alternar entre abas
        function abrirAba(event, abaId) {
            const abas = document.querySelectorAll('.aba-conteudo');
            abas.forEach(aba => aba.style.display = 'none');
            
            const botoes = document.querySelectorAll('.aba-botao');
            botoes.forEach(botao => botao.classList.remove('active'));
            
            document.getElementById(abaId).style.display = 'block';
            event.currentTarget.classList.add('active');
        }
    </script>
    <style>
        /* Estilos básicos */
        .container {
            width: 90%;
            margin: auto;
        }
        .abas {
            display: flex;
            margin-bottom: 20px;
        }
        .aba-botao {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .aba-botao.active {
            background-color: #007bff;
            color: white;
        }
        .aba-conteudo {
            display: none;
        }
        .aba-conteudo.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo ao Setor Financeiro</h1>

        <!-- Navegação entre abas -->
        <div class="abas">
            <button class="aba-botao active" onclick="abrirAba(event, 'aba-lancamentos')">Lançamentos</button>
            <button class="aba-botao" onclick="abrirAba(event, 'aba-contas')">Contas a Pagar</button>
            <button class="aba-botao" onclick="abrirAba(event, 'aba-receitas')">Receitas</button>
            <button class="aba-botao" onclick="abrirAba(event, 'aba-relatorios')">Relatórios</button>
        </div>

        <!-- Conteúdo das abas -->
        <div id="aba-lancamentos" class="aba-conteudo" style="display: block;">
            <h2>Lançamentos Financeiros</h2>
            <form action="processar_lancamento.php" method="POST">
                <label for="descricao">Descrição:</label>
                <input type="text" name="descricao" id="descricao" required>

                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" required>
                    <option value="receita">Receita</option>
                    <option value="despesa">Despesa</option>
                </select>

                <label for="valor">Valor:</label>
                <input type="number" name="valor" id="valor" step="0.01" required>

                <label for="data">Data:</label>
                <input type="date" name="data" id="data" required>

                <button type="submit">Salvar Lançamento</button>
            </form>
        </div>

        <div id="aba-contas" class="aba-conteudo">
            <h2>Contas a Pagar</h2>
            <form action="processar_contas.php" method="POST">
                <label for="fornecedor">Fornecedor:</label>
                <input type="text" name="fornecedor" id="fornecedor" required>

                <label for="valor-conta">Valor:</label>
                <input type="number" name="valor-conta" id="valor-conta" step="0.01" required>

                <label for="vencimento">Data de Vencimento:</label>
                <input type="date" name="vencimento" id="vencimento" required>

                <button type="submit">Registrar Conta</button>
            </form>
        </div>

        <div id="aba-receitas" class="aba-conteudo">
            <h2>Receitas Recebidas</h2>
            <form action="processar_receitas.php" method="POST">
                <label for="cliente">Cliente:</label>
                <input type="text" name="cliente" id="cliente" required>

                <label for="valor-receita">Valor:</label>
                <input type="number" name="valor-receita" id="valor-receita" step="0.01" required>

                <label for="data-receita">Data do Pagamento:</label>
                <input type="date" name="data-receita" id="data-receita" required>

                <button type="submit">Registrar Receita</button>
            </form>
        </div>

        <div id="aba-relatorios" class="aba-conteudo">
            <h2>Relatórios Financeiros</h2>
            <form action="gerar_relatorio.php" method="GET">
                <label for="relatorio-tipo">Tipo de Relatório:</label>
                <select name="relatorio-tipo" id="relatorio-tipo" required>
                    <option value="mensal">Mensal</option>
                    <option value="anual">Anual</option>
                    <option value="customizado">Customizado</option>
                </select>

                <label for="data-inicio">Data Início:</label>
                <input type="date" name="data-inicio" id="data-inicio" required>

                <label for="data-fim">Data Fim:</label>
                <input type="date" name="data-fim" id="data-fim" required>

                <button type="submit">Gerar Relatório</button>
            </form>
        </div>
    </div>
   


<script src="src/js/script.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
    <!-- <script>
        // Configuração inicial para ativar a primeira aba
        document.querySelector('.aba-botao').click();
    </script>
</body>
</html> -->

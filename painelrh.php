
<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel RH</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
        }

        .card h3 {
            font-size: 24px;
            margin: 10px 0;
        }

        .card p {
            font-size: 18px;
            color: #555;
        }

        .card span {
            font-size: 40px;
            font-weight: bold;
            color: #007BFF;
        }
    </style>
    <script>
        function fetchPainelData() {
            // Faz a requisição ao servidor para obter os números
            fetch('painel_data.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalColaboradores').innerText = data.totalColaboradores;
                    document.getElementById('treinamentosExecutados').innerText = data.treinamentosExecutados;
                    document.getElementById('treinamentosEmAndamento').innerText = data.treinamentosEmAndamento;
                })
                .catch(error => {
                    console.error('Erro ao buscar dados do painel:', error);
                });
        }

        // Chamar a função ao carregar a página
        window.onload = fetchPainelData;
    </script>
</head>
<body>
    <div class="container">
        <div class="card">
            <h3>Total de Colaboradores</h3>
            <span id="totalColaboradores">0</span>
            <!-- <p>Colaboradores registrados</p> -->
        </div>
        <div class="card">
            <h3>Treinamentos Executados</h3>
            <span id="treinamentosExecutados">0</span>
            <!-- <p>Total de treinamentos concluídos</p> -->
        </div>
        <div class="card">
            <h3>Treinamentos em Andamento</h3>
            <span id="treinamentosEmAndamento">0</span>
            <!-- <p>Treinamentos em progresso</p> -->
        </div>
    </div>
</body>
</html>

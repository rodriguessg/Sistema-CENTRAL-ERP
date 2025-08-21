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
            $categoria = $_POST['subcategoria'] ?? '';
            $cor = $_POST['cor'] ?? '';

            if (empty($descricao) || empty($severidade) || empty($categoria) || empty($cor)) {
                $erro = "Todos os campos obrigatórios devem ser preenchidos!";
            } else {
                $sql = "INSERT INTO acidentes (data, descricao, localizacao, usuario, severidade, categoria, cor, data_registro) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);

                if (!$stmt) {
                    die("Erro na preparação da query: " . $conn->error);
                }

                $stmt->bind_param("sssssss", $data, $descricao, $localizacao, $username, $severidade, $categoria, $cor);

                if ($stmt->execute()) {
                    $sucesso = "Acidente registrado com sucesso!";
                    header("Location: relatorioacidentes.php?success=1");
                    exit();
                } else {
                    $erro = "Erro ao registrar o acidente: " . $stmt->error;
                }
                $stmt->close();
            }
        }

        include 'header.php';
        ?>

        <?php if ($erro): ?>
            <p class="error"><?php echo $erro; ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <p class="success"><?php echo $sucesso; ?></p>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
              <div class="form-group">
                <label for="categoria">Categoria:</label>
                <select id="categoria" name="categoria" required onchange="updateSubcategorias()">
                    <option value="">Selecione a categoria</option>
                    <option value="Operacionais">Operacionais</option>
                    <option value="Via permanente / infraestrutura">Via permanente / infraestrutura</option>
                    <option value="Relacionadas a terceiros">Relacionadas a terceiros</option>
                    <option value="Emergências médicas">Emergências médicas</option>
                    <option value="Segurança">Segurança</option>
                    <option value="Eventos externos">Eventos externos</option>
                </select>
            </div>
             <div class="form-group">
                <label for="subcategoria">Subcategoria:</label>
                <select id="subcategoria" name="subcategoria" required onchange="updateSeveridadeECor()">
                    <option value="">Selecione a subcategoria</option>
                </select>
            </div>
             <div class="form-group">
                <label for="severidade">Severidade:</label>
                <select id="severidade" name="severidade"  >
                    <option value="">Selecione a severidade</option>
                    <option value="Leve">Leve</option>
                    <option value="Moderado">Moderado</option>
                    <option value="Grave">Grave</option>
                </select>

                  <input type="hidden" id="cor" name="cor">
            </div>

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
                <label for="upload">Anexar Imagens (Opcional):</label>
                <input type="file" id="upload" name="upload" accept="image/*" multiple>
            </div>
            
            <button type="submit">Salvar Registro</button>
        </form>
    </div>

    <script>
        const subcategorias = {
            "Operacionais": [
                { value: "Pane elétrica", text: "Pane elétrica", severidade: "Moderado", cor: "Amarelo" },
                { value: "Falha mecânica", text: "Falha mecânica (freios, motor de tração)", severidade: "Moderado a Grave", cor: "Amarelo/Vermelho" },
                { value: "Descarrilamento sem vítimas", text: "Descarrilamento sem vítimas", severidade: "Grave", cor: "Vermelho" },
                { value: "Descarrilamento com vítimas", text: "Descarrilamento com vítimas", severidade: "Grave", cor: "Vermelho" },
                { value: "Problema de sinalização", text: "Problema de sinalização", severidade: "Moderado", cor: "Amarelo" },
                { value: "Falha no sistema de bilhetagem", text: "Falha no sistema de bilhetagem", severidade: "Leve", cor: "Verde" }
            ],
            "Via permanente / infraestrutura": [
                { value: "Obstrução na via", text: "Obstrução na via (galho, objeto)", severidade: "Leve", cor: "Verde" },
                { value: "Carro estacionado no trilho", text: "Carro estacionado no trilho", severidade: "Moderado", cor: "Amarelo" },
                { value: "Alagamento de via", text: "Alagamento de via", severidade: "Grave", cor: "Vermelho" },
                { value: "Deslizamento de encosta", text: "Deslizamento de encosta", severidade: "Grave", cor: "Vermelho" },
                { value: "Rompimento de trilho / falha estrutural", text: "Rompimento de trilho / falha estrutural", severidade: "Grave", cor: "Vermelho" }
            ],
            "Relacionadas a terceiros": [
                { value: "Atropelamento de pedestre", text: "Atropelamento de pedestre", severidade: "Grave", cor: "Vermelho" },
                { value: "Colisão com veículo", text: "Colisão com veículo", severidade: "Grave", cor: "Vermelho" },
                { value: "Colisão com motocicleta/bicicleta", text: "Colisão com motocicleta/bicicleta", severidade: "Grave", cor: "Vermelho" },
                { value: "Manifestação/bloqueio proposital na via", text: "Manifestação/bloqueio proposital na via", severidade: "Moderado", cor: "Amarelo" }
            ],
            "Emergências médicas": [
                { value: "Passageiro passando mal (sem gravidade)", text: "Passageiro passando mal (sem gravidade)", severidade: "Moderado", cor: "Amarelo" },
                { value: "Passageiro passando mal (grave)", text: "Passageiro passando mal (grave, ex.: infarto)", severidade: "Grave", cor: "Vermelho" },
                { value: "Acidente interno sem vítima grave", text: "Acidente interno sem vítima grave", severidade: "Moderado", cor: "Amarelo" },
                { value: "Acidente interno com vítima grave", text: "Acidente interno com vítima grave", severidade: "Grave", cor: "Vermelho" }
            ],
            "Segurança": [
                { value: "Ato de vandalismo no bonde", text: "Ato de vandalismo no bonde", severidade: "Moderado", cor: "Amarelo" },
                { value: "Agressão entre passageiros", text: "Agressão entre passageiros", severidade: "Moderado a Grave", cor: "Amarelo/Vermelho" },
                { value: "Roubo ou tentativa de assalto", text: "Roubo ou tentativa de assalto", severidade: "Grave", cor: "Vermelho" },
                { value: "Ameaça de bomba / suspeita de artefato", text: "Ameaça de bomba / suspeita de artefato", severidade: "Grave", cor: "Vermelho" }
            ],
            "Eventos externos": [
                { value: "Incêndio em área próxima à via", text: "Incêndio em área próxima à via", severidade: "Grave", cor: "Vermelho" },
                { value: "Queda de árvore sobre a rede elétrica", text: "Queda de árvore sobre a rede elétrica", severidade: "Grave", cor: "Vermelho" },
                { value: "Falta geral de energia elétrica", text: "Falta geral de energia elétrica (rede pública)", severidade: "Moderado", cor: "Amarelo" }
            ]
        };

        function updateSubcategorias() {
            const categoriaSelect = document.getElementById('categoria');
            const subcategoriaSelect = document.getElementById('subcategoria');
            const selectedCategoria = categoriaSelect.value;

            // Clear previous subcategoria options
            subcategoriaSelect.innerHTML = '<option value="">Selecione a subcategoria</option>';
            // Reset severidade and cor
            document.getElementById('severidade').value = '';
            document.getElementById('cor').value = '';

            // Populate subcategoria options based on selected categoria
            if (selectedCategoria && subcategorias[selectedCategoria]) {
                subcategorias[selectedCategoria].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.value;
                    option.textContent = sub.text;
                    subcategoriaSelect.appendChild(option);
                });
            }
        }

        function updateSeveridadeECor() {
            const categoriaSelect = document.getElementById('categoria');
            const subcategoriaSelect = document.getElementById('subcategoria');
            const severidadeSelect = document.getElementById('severidade');
            const corInput = document.getElementById('cor');
            const selectedCategoria = categoriaSelect.value;
            const selectedSubcategoria = subcategoriaSelect.value;

            // Reset severidade and cor
            severidadeSelect.value = '';
            corInput.value = '';

            // Find the selected subcategoria's severity and color
            if (selectedCategoria && selectedSubcategoria && subcategorias[selectedCategoria]) {
                const subcategoria = subcategorias[selectedCategoria].find(sub => sub.value === selectedSubcategoria);
                if (subcategoria) {
                    severidadeSelect.value = subcategoria.severidade;
                    corInput.value = subcategoria.cor;
                }
            }
        }
    </script>

    <?php $conn->close(); ?>
</body>
</html>
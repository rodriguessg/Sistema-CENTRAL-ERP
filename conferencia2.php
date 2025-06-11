<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linha do Tempo de Conferências</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .timeline {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }
        .timeline::after {
            content: '';
            position: absolute;
            width: 6px;
            background-color: #007bff;
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -3px;
        }
        .timeline-item {
            padding: 10px 40px;
            position: relative;
            background-color: inherit;
            width: 50%;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            width: 25px;
            height: 25px;
            right: -12px;
            background-color: #fff;
            border: 4px solid #007bff;
            top: 15px;
            border-radius: 50%;
            z-index: 1;
        }
        .left {
            left: 0;
        }
        .right {
            left: 50%;
        }
        .right::after {
            left: -12px;
        }
        .content {
            padding: 20px 30px;
            background-color: white;
            position: relative;
            border-radius: 6px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        .content:hover {
            background-color: #e9ecef;
        }
        .checklist {
            display: none;
            margin-top: 20px;
        }
        .checklist.active {
            display: block;
        }
        .checklist-item {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .checklist-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-5">Linha do Tempo de Conferências</h1>
        <div class="timeline">
            <?php
            // Configuração da conexão com o banco de dados
            $dsn = 'mysql:host=localhost;dbname=gm_sicbd;charset=utf8';
            $username = 'root'; // Substitua pelo seu usuário do banco
            $password = ''; // Substitua pela sua senha do banco

            try {
                $pdo = new PDO($dsn, $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Consulta para obter produtos
                $stmt = $pdo->query('SELECT produto, quantidade FROM produtos');
                $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Gerar linha do tempo para os próximos 4 meses
                $currentDate = new DateTime();
                for ($i = 0; $i < 4; $i++) {
                    $month = clone $currentDate;
                    $month->modify("+$i month");
                    $monthName = $month->format('F Y');
                    $side = $i % 2 == 0 ? 'left' : 'right';
                    ?>
                    <div class="timeline-item <?php echo $side; ?>">
                        <div class="content" onclick="toggleChecklist('checklist-<?php echo $i; ?>')">
                            <h3>Conferência - <?php echo $monthName; ?></h3>
                            <p>Clique para ver o checklist de materiais.</p>
                            <div class="checklist" id="checklist-<?php echo $i; ?>">
                                <h4>Checklist de Materiais</h4>
                                <?php foreach ($produtos as $produto) { ?>
                                    <div class="checklist-item">
                                        <input type="checkbox" id="item-<?php echo $i . '-' . htmlspecialchars($produto['produto']); ?>">
                                        <label for="item-<?php echo $i . '-' . htmlspecialchars($produto['produto']); ?>">
                                            <?php echo htmlspecialchars($produto['produto']); ?> (Quantidade: <?php echo $produto['quantidade']; ?>)
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Erro na conexão com o banco: ' . $e->getMessage() . '</div>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleChecklist(id) {
            const checklist = document.getElementById(id);
            const isActive = checklist.classList.contains('active');
            // Fechar todos os checklists
            document.querySelectorAll('.checklist').forEach(cl => cl.classList.remove('active'));
            // Abrir o checklist clicado, se não estava aberto
            if (!isActive) {
                checklist.classList.add('active');
            }
        }
    </script>
</body>
</html>

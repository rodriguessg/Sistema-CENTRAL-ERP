<?php
include 'header.php';
?>
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
        .discrepancy {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-5">Linha do Tempo de Conferências</h1>
        <?php
        // Configuração da conexão com o banco de dados
        $dsn = 'mysql:host=localhost;dbname=gm_sicbd;charset=utf8';
        $username = 'root'; // Substitua pelo seu usuário do banco
        $password = ''; // Substitua pela sua senha do banco

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Processar conferências
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checklist'])) {
                $produto_id = $_POST['produto_id'];
                $mes_conferencia = $_POST['mes_conferencia'];
                $conferido = isset($_POST['conferido']) ? 1 : 0;
                $quantidade_fisica = $_POST['quantidade_fisica'] ?? null;
                $stmt = $pdo->prepare('
                    INSERT INTO conferencias (produto_id, mes_conferencia, conferido, quantidade_fisica, data_conferencia)
                    VALUES (?, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE conferido = ?, quantidade_fisica = ?, data_conferencia = NOW()
                ');
                $stmt->execute([$produto_id, $mes_conferencia, $conferido, $quantidade_fisica, $conferido, $quantidade_fisica]);
            }

            // Filtros
            $descricao = $_GET['descricao'] ?? '';
            $quantidade_min = $_GET['quantidade_min'] ?? 0;
            $localizacao = $_GET['localizacao'] ?? '';
            $query = 'SELECT id, produto, quantidade, descricao, localizacao FROM produtos WHERE 1=1';
            $params = [];
            if ($descricao) {
                $query .= ' AND descricao = ?';
                $params[] = $descricao;
            }
            if ($quantidade_min) {
                $query .= ' AND quantidade >= ?';
                $params[] = $quantidade_min;
            }
            if ($localizacao) {
                $query .= ' AND localizacao = ?';
                $params[] = $localizacao;
            }
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obter descrições e localidades para os filtros
            $stmt = $pdo->query('SELECT DISTINCT descricao FROM produtos');
            $descricoes = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $stmt = $pdo->query('SELECT DISTINCT localizacao FROM produtos');
            $localidades = $stmt->fetchAll(PDO::FETCH_COLUMN);
            // Fallback para garantir que xm1 e xm2 estejam disponíveis
            $localidades = array_unique(array_merge($localidades, ['xm1', 'xm2']));
            ?>
            <div class="mb-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <select class="form-select" id="descricao" name="descricao">
                            <option value="">Todas</option>
                            <?php foreach ($descricoes as $desc) : ?>
                                <option value="<?php echo htmlspecialchars($desc); ?>" <?php echo $desc === $descricao ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($desc); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="quantidade_min" class="form-label">Quantidade Mínima</label>
                        <input type="number" class="form-control" id="quantidade_min" name="quantidade_min" value="<?php echo htmlspecialchars($quantidade_min); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="localizacao" class="form-label">Localização</label>
                        <select class="form-select" id="localizacao" name="localizacao">
                            <option value="">Todas</option>
                            <?php foreach ($localidades as $loc) : ?>
                                <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo $loc === $localizacao ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($loc); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="conferencia.php" class="btn btn-secondary">Limpar</a>
                    </div>
                </form>
                <a href="gerar_relatorio.php" class="btn btn-success mt-3">Gerar Relatório PDF</a>
            </div>

            <?php
            // Determinar quais localidades exibir
            $display_localidades = $localizacao ? [$localizacao] : $localidades;

            // Exibir linha do tempo por localidade
            foreach ($display_localidades as $loc) {
                // Filtrar produtos apenas para a localidade atual
                $produtos_local = array_filter($produtos, fn($p) => $p['localizacao'] === $loc);
                if (empty($produtos_local)) {
                    continue;
                }
                ?>
                <h2 class="mt-5"><?php echo htmlspecialchars($loc); ?></h2>
                <div class="timeline">
                    <?php
                    $currentDate = new DateTime();
                    for ($i = 0; $i < 4; $i++) {
                        $month = clone $currentDate;
                        $month->modify("+$i month");
                        $monthName = $month->format('F Y');
                        $monthKey = $month->format('Y-m-01');
                        $side = $i % 2 == 0 ? 'left' : 'right';
                        ?>
                        <div class="timeline-item <?php echo $side; ?>">
                            <div class="content" onclick="toggleChecklist('checklist-<?php echo htmlspecialchars($loc) . '-' . $i; ?>')">
                                <h3>Conferência - <?php echo $monthName; ?></h3>
                                <p>Clique para ver o checklist de materiais.</p>
                                <div class="checklist" id="checklist-<?php echo htmlspecialchars($loc) . '-' . $i; ?>">
                                    <h4>Checklist de Materiais</h4>
                                    <?php foreach ($produtos_local as $produto) : ?>
                                        <?php
                                        $stmt = $pdo->prepare('SELECT conferido, quantidade_fisica FROM conferencias WHERE produto_id = ? AND mes_conferencia = ?');
                                        $stmt->execute([$produto['id'], $monthKey]);
                                        $conferencia = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $conferido = $conferencia['conferido'] ?? 0;
                                        $quantidade_fisica = $conferencia['quantidade_fisica'] ?? '';
                                        $discrepancia = ($quantidade_fisica !== '' && $quantidade_fisica != $produto['quantidade'])
                                            ? "<span class='discrepancy'>Discrepância: " . abs($produto['quantidade'] - $quantidade_fisica) . "</span>"
                                            : '';
                                        ?>
                                        <form method="POST" class="checklist-item">
                                            <input type="hidden" name="checklist" value="1">
                                            <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                                            <input type="hidden" name="mes_conferencia" value="<?php echo $monthKey; ?>">
                                            <input type="checkbox" name="conferido" id="item-<?php echo htmlspecialchars($loc) . '-' . $i . '-' . $produto['id']; ?>" 
                                                   <?php echo $conferido ? 'checked' : ''; ?> onchange="this.form.submit()">
                                            <label for="item-<?php echo htmlspecialchars($loc) . '-' . $i . '-' . $produto['id']; ?>">
                                                <?php echo htmlspecialchars($produto['produto']); ?> 
                                                (Quantidade Lógica: <?php echo $produto['quantidade']; ?>, Descrição: <?php echo htmlspecialchars($produto['descricao'] ?? 'N/A'); ?>)
                                            </label>
                                            <input type="number" name="quantidade_fisica" placeholder="Quantidade física" 
                                                   value="<?php echo htmlspecialchars($quantidade_fisica); ?>" style="width: 100px;" onchange="this.form.submit()">
                                            <?php echo $discrepancia; ?>
                                        </form>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    } // Fim do loop de meses
                    ?>
                </div>
                <?php
            } // Fim do loop de localidades
            ?>
        </div>
        <?php
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">Erro na conexão com o banco: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleChecklist(id) {
            const checklist = document.getElementById(id);
            const isActive = checklist.classList.contains('active');
            document.querySelectorAll('.checklist').forEach(cl => cl.classList.remove('active'));
            if (!isActive) {
                checklist.classList.add('active');
            }
        }
    </script>

    <?php

    include 'footer.php';
    ?>
</body>
</html>
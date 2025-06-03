<?php
// Configuração da conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    // Conexão com o banco de dados usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Consultando contratos encerrados
    $sql = "SELECT id, titulo, validade, valor_contrato, data_cadastro 
            FROM gestao_contratos 
            WHERE situacao = 'encerrado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $contratos = $stmt->fetchAll();

    // Inicializa variável para evitar undefined variable
    $contrato = null;
    $prestacao = null;

    // Se um contrato for selecionado, buscar os dados do contrato e criar/verificar registro na prestacao_contas
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contrato_id'])) {
        $contrato_id = filter_input(INPUT_POST, 'contrato_id', FILTER_VALIDATE_INT);
        if ($contrato_id !== false) {
            // Buscar dados do contrato
            $sql_contrato = "SELECT id, titulo, valor_contrato, data_cadastro, validade 
                           FROM gestao_contratos 
                           WHERE id = :id AND situacao = 'encerrado'";
            $stmt_contrato = $pdo->prepare($sql_contrato);
            $stmt_contrato->bindParam(':id', $contrato_id, PDO::PARAM_INT);
            $stmt_contrato->execute();
            $contrato = $stmt_contrato->fetch();

            if ($contrato) {
                // Verificar se já existe registro na prestacao_contas
                $sql_prestacao = "SELECT id, status FROM prestacao_contas WHERE contrato_id = :contrato_id";
                $stmt_prestacao = $pdo->prepare($sql_prestacao);
                $stmt_prestacao->bindParam(':contrato_id', $contrato_id, PDO::PARAM_INT);
                $stmt_prestacao->execute();
                $prestacao = $stmt_prestacao->fetch();

                // Se não existe, criar registro com status Pendente
                if (!$prestacao) {
                    $sql_insert = "INSERT INTO prestacao_contas (contrato_id, status) 
                                 VALUES (:contrato_id, 'Pendente')";
                    $stmt_insert = $pdo->prepare($sql_insert);
                    $stmt_insert->bindParam(':contrato_id', $contrato_id, PDO::PARAM_INT);
                    $stmt_insert->execute();
                    $prestacao = ['id' => $pdo->lastInsertId(), 'status' => 'Pendente'];
                }
            }
        }
    }
} catch (PDOException $e) {
    error_log("Erro de conexão: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestação de Contas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; margin-top: 20px; }
        .table-container { margin-bottom: 20px; }
        .form-container { background-color: #f8f9fa; padding: 20px; border-radius: 8px; }
        .error { color: red; font-size: 0.9em; }
        .fade-in { animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        #form-prestacao-container { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Prestação de Contas</h2>

        <!-- Tabela de Contratos -->
        <div class="table-container">
            <h4>Contratos Encerrados</h4>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Validade</th>
                        <th scope="col">Valor (R$)</th>
                        <th scope="col">Status Prestação</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($contratos)): ?>
                        <?php foreach ($contratos as $c): ?>
                            <?php
                            // Buscar status da prestação para cada contrato
                            $sql_status = "SELECT status FROM prestacao_contas WHERE contrato_id = :contrato_id";
                            $stmt_status = $pdo->prepare($sql_status);
                            $stmt_status->bindParam(':contrato_id', $c['id'], PDO::PARAM_INT);
                            $stmt_status->execute();
                            $status_prestacao = $stmt_status->fetchColumn() ?: 'Pendente';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($c['titulo']); ?></td>
                                <td><?= htmlspecialchars($c['validade']); ?></td>
                                <td>R$ <?= number_format($c['valor_contrato'], 2, ',', '.'); ?></td>
                                <td>
                                    <?php
                                    $badge_class = match ($status_prestacao) {
                                        'Pendente' => 'bg-warning',
                                        'Em Andamento' => 'bg-primary',
                                        'Concluída' => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badge_class; ?>"><?= htmlspecialchars($status_prestacao); ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary select-contrato" 
                                            data-id="<?= htmlspecialchars($c['id']); ?>" 
                                            data-titulo="<?= htmlspecialchars($c['titulo']); ?>" 
                                            data-valor="<?= number_format($c['valor_contrato'], 2, '.', ''); ?>" 
                                            data-inicio="<?= htmlspecialchars($c['data_cadastro']); ?>" 
                                            data-validade="<?= htmlspecialchars($c['validade']); ?>"
                                            data-status="<?= htmlspecialchars($status_prestacao); ?>"
                                            aria-label="Selecionar contrato <?= htmlspecialchars($c['titulo']); ?>">
                                        Selecionar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Nenhum contrato encontrado</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Formulário de Prestação de Contas -->
        <div class="form-container fade-in" id="form-prestacao-container">
            <h4>Detalhes do Contrato Selecionado</h4>
            <div id="contrato-detalhes">
                <p><strong>Nome do Contrato:</strong> <span id="detalhe-titulo"></span></p>
                <p><strong>Valor do Contrato:</strong> R$ <span id="detalhe-valor"></span></p>
                <p><strong>Data de Início:</strong> <span id="detalhe-inicio"></span></p>
                <p><strong>Data de Encerramento:</strong> <span id="detalhe-validade"></span></p>
            </div>

            <h4 class="mt-4">Realizar Prestação de Contas</h4>
            <form id="form-prestacao" method="POST" action="processar_prestacao.php">
                <input type="hidden" name="contrato_id" id="contrato_id">

                <div class="mb-3">
                    <label for="valor_pago" class="form-label">Valor Pago:</label>
                    <input type="number" class="form-control" id="valor_pago" name="valor_pago" step="0.01" required 
                           aria-describedby="valor_pago_error">
                    <div class="error" id="valor_pago_error"></div>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição da Prestação de Contas:</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="4" required 
                              aria-describedby="descricao_error"></textarea>
                    <div class="error" id="descricao_error"></div>
                </div>

                <div class="mb-3">
                    <label for="data_pagamento" class="form-label">Data de Pagamento:</label>
                    <input type="date" class="form-control" id="data_pagamento" name="data_pagamento" required 
                           aria-describedby="data_pagamento_error">
                    <div class="error" id="data_pagamento_error"></div>
                </div>

                <div class="mb-3">
                    <label for="prestacao_status" class="form-label">Status da Prestação:</label>
                    <select class="form-control" id="prestacao_status" name="prestacao_status" required>
                        <option value="Pendente">Pendente</option>
                        <option value="Em Andamento">Em Andamento</option>
                        <option value="Concluída">Concluída</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="chamado_glpi" class="form-label">Chamado GLPI (Opcional):</label>
                    <input type="text" class="form-control" id="chamado_glpi" name="chamado_glpi" placeholder="Ex.: 1748">
                </div>

                <button type="submit" class="btn btn-success">Salvar Prestação de Contas</button>
                <button type="button" class="btn btn-secondary ms-2" id="cancelar-form">Cancelar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Mostrar formulário ao selecionar contrato
            $('.select-contrato').click(function () {
                const id = $(this).data('id');
                const titulo = $(this).data('titulo');
                const valor = $(this).data('valor');
                const inicio = $(this).data('inicio');
                const validade = $(this).data('validade');
                const status = $(this).data('status');

                $('#contrato_id').val(id);
                $('#detalhe-titulo').text(titulo);
                $('#detalhe-valor').text(parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
                $('#detalhe-inicio').text(inicio);
                $('#detalhe-validade').text(validade);
                $('#valor_pago').val(valor);
                $('#prestacao_status').val(status);
                $('#form-prestacao-container').fadeIn();
                $('#form-prestacao').trigger('reset'); // Reseta campos não preenchidos
                $('#valor_pago').val(valor); // Restaura valor após reset
                $('#prestacao_status').val(status); // Restaura status após reset
                $('.error').text(''); // Limpa mensagens de erro
            });

            // Esconder formulário ao clicar em Cancelar
            $('#cancelar-form').click(function () {
                $('#form-prestacao-container').fadeOut();
            });

            // Validação do formulário
            $('#form-prestacao').submit(function (e) {
                let valid = true;
                $('.error').text('');

                const valorPago = $('#valor_pago').val();
                if (!valorPago || valorPago <= 0) {
                    $('#valor_pago_error').text('Por favor, insira um valor válido maior que zero.');
                    valid = false;
                }

                const descricao = $('#descricao').val().trim();
                if (!descricao) {
                    $('#descricao_error').text('Por favor, insira uma descrição.');
                    valid = false;
                }

                const dataPagamento = $('#data_pagamento').val();
                if (!dataPagamento) {
                    $('#data_pagamento_error').text('Por favor, selecione uma data.');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
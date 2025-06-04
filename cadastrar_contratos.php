<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
    die("Acesso negado. Faça login primeiro.");
}

$usuario_logado = $_SESSION['username'];
$setor_usuario = $_SESSION['setor'];

// Conexão com o banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cadastrar_contrato"])) {
    try {
        function formatarValor($valor) {
            return floatval(str_replace(['R$', '.', ','], ['', '', '.'], trim($valor)));
        }

        // Formatar os valores dos campos
        $valor_contrato = isset($_POST['valor_contrato']) ? formatarValor($_POST['valor_contrato']) : 0;
        $valor_nf = isset($_POST['valor_nf']) ? formatarValor($_POST['valor_nf']) : 0;
        $n_despesas = isset($_POST['n_despesas']) && !empty($_POST['n_despesas']) ? $_POST['n_despesas'] : 'Sem Despesas';

        // Inserir contrato
        $sql = "INSERT INTO gestao_contratos 
                (titulo, SEI, objeto, gestor, gestorsb, fiscais, validade, contatos, valor_contrato, 
                num_parcelas, descricao, situacao, agencia_bancaria, fonte, publicacao, date_service, 
                n_despesas, valor_nf, parcelamento, outros, servicos, categoria) 
                VALUES 
                (:titulo, :SEI, :objeto, :gestor, :gestorsb, :fiscais, :validade, :contatos, :valor_contrato, 
                 :num_parcelas, :descricao, 'Ativo', :agencia_bancaria, :fonte, :publicacao, 
                 :date_service, :n_despesas, :valor_nf, :parcelamento, :outros, :servicos, :categoria)";

        $stmt = $pdo->prepare($sql);

        // Bind dos dados
        $stmt->bindParam(':titulo', $_POST['titulo']);
        $stmt->bindParam(':SEI', $_POST['SEI']);
        $stmt->bindParam(':objeto', $_POST['objeto']);
        $stmt->bindParam(':gestor', $_POST['gestor']);
        $stmt->bindParam(':gestorsb', $_POST['gestorsb']);
        $stmt->bindParam(':fiscais', $_POST['fiscais']);
        $stmt->bindParam(':validade', $_POST['validade']);
        $stmt->bindParam(':contatos', $_POST['contatos']);
        $stmt->bindParam(':valor_contrato', $valor_contrato);
        $num_parcelas = isset($_POST['parcelamento']) ? $_POST['num_parcelas'] : null;
        $stmt->bindParam(':num_parcelas', $num_parcelas);
        $stmt->bindParam(':descricao', $_POST['descricao']);
        $stmt->bindParam(':agencia_bancaria', $_POST['account-bank']);
        $stmt->bindParam(':fonte', $_POST['fonte']);
        $stmt->bindParam(':publicacao', $_POST['publicacao']);
        $stmt->bindParam(':date_service', $_POST['date_service']);
        $stmt->bindParam(':n_despesas', $n_despesas);
        $stmt->bindParam(':valor_nf', $valor_nf);
        $stmt->bindValue(':parcelamento', isset($_POST['parcelamento']) ? 'Sim' : 'Não');
        $stmt->bindValue(':outros', isset($_POST['outros']) ? 'Sim' : 'Não');
        $stmt->bindValue(':servicos', $_POST['servicos']);
        $stmt->bindValue(':categoria', $_POST['categoria']); // Categoria adicionada
        $stmt->execute();

        $contrato_id = $pdo->lastInsertId();

        // Se parcelado, insere parcelas e eventos
        if ($num_parcelas) {
            $valor_parcela = $valor_contrato / $num_parcelas;
            $validade = new DateTime($_POST['validade']);

            for ($i = 0; $i < $num_parcelas; $i++) {
                $validade->add(new DateInterval('P1M'));
                $mes = $validade->format('m');
                $ano = $validade->format('Y');

                // Inserir parcela
                $sql_parcela = "INSERT INTO contratos_parcelas (contrato_id, mes, ano, valor) 
                                VALUES (:contrato_id, :mes, :ano, :valor)";
                $stmt_parcela = $pdo->prepare($sql_parcela);
                $stmt_parcela->bindParam(':contrato_id', $contrato_id);
                $stmt_parcela->bindParam(':mes', $mes);
                $stmt_parcela->bindParam(':ano', $ano);
                $stmt_parcela->bindParam(':valor', $valor_parcela);
                $stmt_parcela->execute();

                // Evento para 5 dias antes do vencimento
                $data_evento = clone $validade;
                $data_evento->sub(new DateInterval('P5D'));
                $titulo_evento = "Vencimento de Parcela: " . $_POST['titulo'];
                $descricao_evento = "Parcela referente a " . $validade->format('F/Y') . 
                                    ". Valor: R$ " . number_format($valor_parcela, 2, ',', '.');

                // Inserir evento
                $sql_evento = "INSERT INTO eventos (titulo, descricao, data, hora, categoria, cor, criado_em) 
                               VALUES (:titulo, :descricao, :data, :hora, :categoria, :cor, NOW())";
                $stmt_evento = $pdo->prepare($sql_evento);
                $stmt_evento->bindParam(':titulo', $titulo_evento);
                $stmt_evento->bindParam(':descricao', $descricao_evento);
                $stmt_evento->bindParam(':data', $data_evento->format('Y-m-d'));
                $stmt_evento->bindValue(':hora', '09:00');
                $stmt_evento->bindValue(':categoria', 'Pagamento');
                $stmt_evento->bindValue(':cor', '#FF9900');
                $stmt_evento->execute();

                // Inserir notificação
                $sql_notificacao = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                                    VALUES (:username, :setor, :mensagem, 'pendente', NOW())";
                $stmt_notificacao = $pdo->prepare($sql_notificacao);
                $mensagem = "Lembrete: Vencimento da parcela do contrato \"" . $_POST['titulo'] . "\" em " . $validade->format('d/m/Y');
                $stmt_notificacao->bindParam(':username', $usuario_logado);
                $stmt_notificacao->bindParam(':setor', $setor_usuario);
                $stmt_notificacao->bindParam(':mensagem', $mensagem);
                $stmt_notificacao->execute();
            }

        }

        header("Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=Cadastrado_contratos_sucesso&pagina=/Sistema-CENTRAL-ERP/homecontratos.php");
        exit();
         // Registro no log_eventos
    $sql_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())";
    $stmt_log = $conn->prepare($sql_log);

    if ($stmt_log) {
        $tipo_operacao = "cadastrou um contrato na base de dados.";
        $stmt_log->bind_param("ss", $username, $tipo_operacao);

        if ($stmt_log->execute()) {
            // Redireciona para a página de sucesso
           // Redirecionamento para a página 'mensagem.php' em views, com os parâmetros necessários
          header('Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=sucesso2&pagina=/Sistema-CENTRAL-ERP/homeestoque.php');
          exit();

        } else {
            echo "Erro ao registrar ação no log: " . $stmt_log->error;
        }

        $stmt_log->close();
    } else {
        echo "Erro na preparação da consulta do log: " . $conn->error;
    }
    } catch (PDOException $e) {
        echo "Erro ao cadastrar contrato: " . $e->getMessage();
    }
}
?>

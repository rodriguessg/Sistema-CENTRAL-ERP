<?php
    // Conexão com o banco de dados (substitua pelos seus dados)
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cadastrar_contrato"])) {
        try {
            // Converte os valores monetários para formato numérico correto
            function formatarValor($valor) {
                return floatval(str_replace(['R$', '.', ','], ['', '', '.'], trim($valor)));
            }

            $valor_contrato = isset($_POST['valor_contrato']) ? formatarValor($_POST['valor_contrato']) : 0;
            $valor_aditivo = isset($_POST['valor_aditivo']) ? formatarValor($_POST['valor_aditivo']) : null;

            // Prepara a inserção dos dados na tabela gestao_contratos
            $sql = "INSERT INTO gestao_contratos 
                    (titulo, SEI, objeto, gestor, gestorsb, fiscais, validade, contatos, valor_contrato, valor_aditivo, num_parcelas, descricao, situacao) 
                    VALUES 
                    (:titulo, :SEI, :objeto, :gestor, :gestorsb, :fiscais, :validade, :contatos, :valor_contrato, :valor_aditivo, :num_parcelas, :descricao, 'Ativo')";

            $stmt = $pdo->prepare($sql);

            // Bind dos parâmetros
            $stmt->bindParam(':titulo', $_POST['titulo']);
            $stmt->bindParam(':SEI', $_POST['SEI']);
            $stmt->bindParam(':objeto', $_POST['objeto']);
            $stmt->bindParam(':gestor', $_POST['gestor']);
            $stmt->bindParam(':gestorsb', $_POST['gestorsb']);
            $stmt->bindParam(':fiscais', $_POST['fiscais']);
            $stmt->bindParam(':validade', $_POST['validade']);
            $stmt->bindParam(':contatos', $_POST['contatos']);
            $stmt->bindParam(':valor_contrato', $valor_contrato);
            $stmt->bindParam(':valor_aditivo', $valor_aditivo);

            // Se o contrato for parcelado, armazena o número de parcelas, senão define como NULL
            $num_parcelas = isset($_POST['parcelamento']) ? $_POST['num_parcelas'] : null;
            $stmt->bindParam(':num_parcelas', $num_parcelas);

            $stmt->bindParam(':descricao', $_POST['descricao']);

            // Executa a inserção
            $stmt->execute();
            $contrato_id = $pdo->lastInsertId();

            // Adiciona as parcelas no banco
            if ($num_parcelas) {
                $valor_parcela = $valor_contrato / $num_parcelas;
                $validade = new DateTime($_POST['validade']);
                for ($i = 0; $i < $num_parcelas; $i++) {
                    $validade->add(new DateInterval('P1M')); // Adiciona 1 mês a cada parcela
                    $mes = $validade->format('m');
                    $ano = $validade->format('Y');
                    $sql_parcelas = "INSERT INTO contratos_parcelas (contrato_id, mes, ano, valor) VALUES (:contrato_id, :mes, :ano, :valor)";
                    $stmt_parcelas = $pdo->prepare($sql_parcelas);
                    $stmt_parcelas->bindParam(':contrato_id', $contrato_id);
                    $stmt_parcelas->bindParam(':mes', $mes);
                    $stmt_parcelas->bindParam(':ano', $ano);
                    $stmt_parcelas->bindParam(':valor', $valor_parcela);
                    $stmt_parcelas->execute();
                }
            }

            echo "<script>alert('Contrato cadastrado com sucesso!'); window.location.href='homecontratos.php';</script>";
        } catch (PDOException $e) {
            echo "Erro ao cadastrar contrato: " . $e->getMessage();
        }
    }
?>

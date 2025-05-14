<?php
// Conexão com o banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cadastrar_contrato"])) {
    try {
        // Função para formatar valores monetários
        function formatarValor($valor) {
            return floatval(str_replace(['R$', '.', ','], ['', '', '.'], trim($valor)));
        }

        // Formatar os valores de contrato e aditivo
        $valor_contrato = isset($_POST['valor_contrato']) ? formatarValor($_POST['valor_contrato']) : 0;
     
        $valor_nf = isset($_POST['valor_nf']) ? formatarValor($_POST['valor_nf']) : 0;

        // Se o campo n_despesas não foi preenchido, atribui "Sem Despesas"
        $n_despesas = isset($_POST['n_despesas']) && !empty($_POST['n_despesas']) ? $_POST['n_despesas'] : 'Sem Despesas';

        // Prepara a inserção dos dados na tabela gestao_contratos
        $sql = "INSERT INTO gestao_contratos 
                (titulo, SEI, objeto, gestor, gestorsb, fiscais, validade, contatos, valor_contrato, 
                num_parcelas, descricao, situacao, agencia_bancaria, fonte, publicacao, date_service, n_despesas, 
                valor_nf, parcelamento, outros, servicos) 
                VALUES 
                (:titulo, :SEI, :objeto, :gestor, :gestorsb, :fiscais, :validade, :contatos, :valor_contrato, 
                 :num_parcelas, :descricao, 'Ativo', :agencia_bancaria, :fonte, :publicacao, 
                :date_service, :n_despesas, :valor_nf, :parcelamento, :outros, :servicos)";

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
      

        // Se o contrato for parcelado, armazena o número de parcelas, senão define como NULL
        $num_parcelas = isset($_POST['parcelamento']) ? $_POST['num_parcelas'] : null;
        $stmt->bindParam(':num_parcelas', $num_parcelas);

        $stmt->bindParam(':descricao', $_POST['descricao']);

        // Novos campos
        $stmt->bindParam(':agencia_bancaria', $_POST['account-bank']);
        $stmt->bindParam(':fonte', $_POST['fonte']);
        $stmt->bindParam(':publicacao', $_POST['publicacao']);
        $stmt->bindParam(':date_service', $_POST['date_service']);
        $stmt->bindParam(':n_despesas', $n_despesas); // Aqui passamos a variável corrigida
        $stmt->bindParam(':valor_nf', $valor_nf);

        // Usando bindValue() para parâmetros que são valores literais
        $stmt->bindValue(':parcelamento', isset($_POST['parcelamento']) ? 'Sim' : 'Não');
        $stmt->bindValue(':outros', isset($_POST['outros']) ? 'Sim' : 'Não');
        $stmt->bindValue(':servicos', $_POST['servicos']);

        // Executa a inserção
        $stmt->execute();
        $contrato_id = $pdo->lastInsertId();

        // Adiciona as parcelas no banco, se for parcelado
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

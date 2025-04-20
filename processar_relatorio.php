<?php
// Conectar ao banco de dados
$host = 'localhost';  
$dbname = 'gm_sicbd';  
$username = 'root';  
$password = '';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro ao conectar com o banco de dados: ' . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['contrato']) && isset($_POST['relatorio_tipo'])) {
        $titulo_contrato = $_POST['contrato'];
        $relatorio_tipo = $_POST['relatorio_tipo'];

        if (empty($titulo_contrato)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Título do contrato não selecionado.']);
            exit;
        }

        try {
            // Buscar dados do contrato
            $sql = "SELECT titulo, validade, gestor, gestorsb, situacao, num_parcelas, data_cadastro
                    FROM gestao_contratos WHERE titulo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo_contrato]);

            if ($stmt->rowCount() > 0) {
                $dados = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verifica o tipo de relatório e executa a lógica correspondente
                if ($relatorio_tipo === 'completo') {
                    // Relatório completo
                    $dados['relatorio_completo'] = true;
                    echo json_encode(['sucesso' => true, 'dados' => $dados]);
                } elseif ($relatorio_tipo === 'compromissos_futuros') {
                    // Relatório de compromissos futuros
                    $sql_pagamentos = "SELECT data_pagamento FROM pagamentos WHERE contrato_titulo = ? AND data_pagamento > CURDATE()";
                    $stmt_pagamentos = $pdo->prepare($sql_pagamentos);
                    $stmt_pagamentos->execute([$titulo_contrato]);

                    $pagamentos = [];
                    while ($pagamento = $stmt_pagamentos->fetch(PDO::FETCH_ASSOC)) {
                        $pagamentos[] = $pagamento['data_pagamento'];
                    }

                    $dados['proximos_pagamentos'] = implode(", ", $pagamentos);
                    $dados['relatorio_compromissos_futuros'] = true;
                    echo json_encode(['sucesso' => true, 'dados' => $dados]);

                } elseif ($relatorio_tipo === 'pagamentos') {
                    // Relatório de pagamentos
                    $sql_pagamentos = "SELECT data_pagamento, valor FROM pagamentos WHERE contrato_titulo = ? ORDER BY data_pagamento";
                    $stmt_pagamentos = $pdo->prepare($sql_pagamentos);
                    $stmt_pagamentos->execute([$titulo_contrato]);

                    $pagamentos = [];
                    while ($pagamento = $stmt_pagamentos->fetch(PDO::FETCH_ASSOC)) {
                        $pagamentos[] = [
                            'data_pagamento' => $pagamento['data_pagamento'],
                            'valor' => $pagamento['valor']
                        ];
                    }

                    $dados['pagamentos'] = $pagamentos;
                    $dados['relatorio_pagamentos'] = true;
                    echo json_encode(['sucesso' => true, 'dados' => $dados]);

                } else {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Tipo de relatório inválido.']);
                }

            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Contrato não encontrado.']);
            }

        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Dados de contrato ou tipo de relatório não enviados.']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método de requisição inválido.']);
}
?>

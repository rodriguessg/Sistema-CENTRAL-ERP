<?php
header('Content-Type: application/json');

// Conectar ao banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao conectar com o banco de dados: ' . $e->getMessage()]);
    exit;
}

// Função para enviar resposta JSON
function enviarResposta($sucesso, $dados = [], $mensagem = '') {
    echo json_encode([
        'sucesso' => $sucesso,
        'dados' => $dados,
        'mensagem' => $mensagem
    ]);
    exit;
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['contrato']) && isset($_POST['relatorio_tipo'])) {
        $titulo_contrato = $_POST['contrato'];
        $relatorio_tipo = $_POST['relatorio_tipo'];

        if (empty($titulo_contrato)) {
            enviarResposta(false, [], 'Título do contrato não selecionado.');
        }

        try {
            // Relatório Mensal
            if ($relatorio_tipo === 'mensal') {
                // Busca todos os contratos e seus pagamentos
                $sql = "
                    SELECT g.titulo, g.num_parcelas, p.data_pagamento, p.valor_contrato
                    FROM gestao_contratos g
                    LEFT JOIN pagamentos p ON p.contrato_titulo = g.titulo
                    WHERE g.titulo = ?
                    ORDER BY g.titulo, p.data_pagamento
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titulo_contrato]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($result) {
                    // Organiza os dados por contrato
                    $contratos = [];
                    foreach ($result as $row) {
                        $titulo = $row['titulo'];
                        if (!isset($contratos[$titulo])) {
                            $contratos[$titulo] = [
                                'titulo' => $titulo,
                                'num_parcelas' => $row['num_parcelas'],
                                'pagamentos' => []
                            ];
                        }
                        if ($row['data_pagamento']) {
                            $contratos[$titulo]['pagamentos'][] = [
                                'mes' => $row['mes'],
                                'data_pagamento' => $row['data_pagamento'],
                                'valor' => (float)$row['valor']
                            ];
                        }
                    }

                    // Converte para array indexado
                    $dados = array_values($contratos);
                    enviarResposta(true, $dados);
                } else {
                    enviarResposta(false, [], 'Nenhum contrato encontrado.');
                }

            // Relatório Anual
            } elseif ($relatorio_tipo === 'anual') {
                // Busca contratos e agrega pagamentos por ano
                $sql = "
                    SELECT g.titulo, g.num_parcelas, YEAR(p.data_pagamento) AS ano,
                           COUNT(p.id) AS quantidade_pagamentos, SUM( p.valor_contrato) AS total_pago
                    FROM gestao_contratos g
                    LEFT JOIN pagamentos p ON p.contrato_titulo = g.titulo
                    WHERE g.titulo = ?
                    GROUP BY g.titulo, g.num_parcelas, YEAR(p.data_pagamento)
                    ORDER BY g.titulo, ano
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titulo_contrato]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($result) {
                    // Organiza os dados por contrato
                    $contratos = [];
                    foreach ($result as $row) {
                        $titulo = $row['titulo'];
                        if (!isset($contratos[$titulo])) {
                            $contratos[$titulo] = [
                                'titulo' => $titulo,
                                'num_parcelas' => $row['num_parcelas'],
                                'anos' => []
                            ];
                        }
                        if ($row['ano']) {
                            $contratos[$titulo]['anos'][] = [
                                'ano' => $row['ano'],
                                'quantidade_pagamentos' => (int)$row['quantidade_pagamentos'],
                                'total_pago' => (float)$row['total_pago']
                            ];
                        }
                    }

                    // Converte para array indexado
                    $dados = array_values($contratos);
                    enviarResposta(true, $dados);
                } else {
                    enviarResposta(false, [], 'Nenhum contrato encontrado.');
                }

            // Relatório Completo
            } elseif ($relatorio_tipo === 'completo') {
                $sql = "
                    SELECT titulo, validade, gestor, gestorsb, situacao, num_parcelas, data_cadastro
                    FROM gestao_contratos
                    WHERE titulo = ?
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titulo_contrato]);
                $dados = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($dados) {
                    enviarResposta(true, $dados);
                } else {
                    enviarResposta(false, [], 'Contrato não encontrado.');
                }

            // Relatório de Compromissos Futuros
            } elseif ($relatorio_tipo === 'compromissos_futuros') {
                $sql = "
                    SELECT titulo, validade, gestor, gestorsb, situacao, num_parcelas, data_cadastro
                    FROM gestao_contratos
                    WHERE titulo = ?
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titulo_contrato]);
                $dados = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($dados) {
                    $sql_pagamentos = "
                        SELECT data_pagamento
                        FROM pagamentos
                        WHERE contrato_titulo = ? AND data_pagamento > CURDATE()
                        ORDER BY data_pagamento
                    ";
                    $stmt_pagamentos = $pdo->prepare($sql_pagamentos);
                    $stmt_pagamentos->execute([$titulo_contrato]);
                    $pagamentos = $stmt_pagamentos->fetchAll(PDO::FETCH_ASSOC);

                    $proximos_pagamentos = array_column($pagamentos, 'data_pagamento');
                    $dados['proximos_pagamentos'] = implode(", ", $proximos_pagamentos) ?: 'Nenhum pagamento futuro';
                    enviarResposta(true, $dados);
                } else {
                    enviarResposta(false, [], 'Contrato não encontrado.');
                }

            // Relatório de Pagamentos
            } else if ($relatorio_tipo === 'pagamentos') {
                // Busca os pagamentos associados ao contrato
                $sql = "
                    SELECT p.data_pagamento, p.valor_liquidado_ag, p.mes, p.contrato_titulo
                    FROM pagamentos p
                    WHERE p.contrato_titulo = ?
                    ORDER BY p.data_pagamento
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titulo_contrato]);
                $pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Verifica se existem pagamentos
                if ($pagamentos) {
                    // Organizar os pagamentos por contrato
                    $contratos = [];
                    foreach ($pagamentos as $pagamento) {
                        $titulo = $pagamento['contrato_titulo'];

                        // Organiza os dados
                        if (!isset($contratos[$titulo])) {
                            $contratos[$titulo] = [
                                'contrato_titulo' => $titulo,
                                'pagamentos' => []
                            ];
                        }

                        $contratos[$titulo]['pagamentos'][] = [
                            'mes' => $pagamento['mes'],
                            'data_pagamento' => $pagamento['data_pagamento'],
                            'valor_liquidado_ag' => (float)$pagamento['valor_liquidado_ag']
                        ];
                    }

                    // Converte os dados para um array indexado
                    $dados = array_values($contratos);
                    enviarResposta(true, $dados);
                } else {
                    enviarResposta(false, [], 'Nenhum pagamento encontrado.');
                }
            } else {
                enviarResposta(false, [], 'Tipo de relatório inválido.');
            }

        } catch (Exception $e) {
            enviarResposta(false, [], 'Erro no servidor: ' . $e->getMessage());
        }
    } else {
        enviarResposta(false, [], 'Dados de contrato ou tipo de relatório não enviados.');
    }
} else {
    enviarResposta(false, [], 'Método de requisição inválido.');
}
?>
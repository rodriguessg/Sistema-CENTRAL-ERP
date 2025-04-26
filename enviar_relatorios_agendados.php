<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Se estiver usando Composer

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gm_sicbd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar relatórios agendados que devem ser enviados
    $sql = "SELECT * FROM relatorios_agendados WHERE proximo_envio <= NOW()";
    $stmt = $pdo->query($sql);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($agendamentos as $agendamento) {
        $id = $agendamento['id'];
        $tipo_relatorio = $agendamento['tipo_relatorio'];
        $contrato_id = $agendamento['contrato_id'];
        $relatorio_todos = $agendamento['relatorio_todos'];
        $mes = $agendamento['mes'];
        $ano = $agendamento['ano'];
        $email_destinatario = $agendamento['email_destinatario'];
        $periodicidade = $agendamento['periodicidade'];

        // Gerar o relatório
        $dados = [];
        if ($relatorio_todos) {
            $sql_relatorio = "SELECT c.id, c.titulo, c.validade, c.gestor, c.gestorsb, c.situacao, c.num_parcelas, c.data_cadastro
                              FROM contratos c";
            $stmt_relatorio = $pdo->query($sql_relatorio);
            $contratos = $stmt_relatorio->fetchAll(PDO::FETCH_ASSOC);

            foreach ($contratos as $contrato) {
                $contrato_id_rel = $contrato['id'];
                $contrato_data = $contrato;

                if ($relatorio_todos === 'mensal_todos') {
                    $sql_pagamentos = "SELECT data_pagamento, valor
                                       FROM pagamentos
                                       WHERE contrato_id = :id AND MONTH(data_pagamento) = :mes
                                       ORDER BY data_pagamento";
                    $stmt_pagamentos = $pdo->prepare($sql_pagamentos);
                    $stmt_pagamentos->execute(['id' => $contrato_id_rel, 'mes' => $mes]);
                    $contrato_data['pagamentos'] = $stmt_pagamentos->fetchAll(PDO::FETCH_ASSOC);
                } elseif ($relatorio_todos === 'anual_todos') {
                    $sql_pagamentos = "SELECT YEAR(data_pagamento) AS ano, SUM(valor) AS total_pago, COUNT(*) AS quantidade_pagamentos
                                       FROM pagamentos
                                       WHERE contrato_id = :id AND YEAR(data_pagamento) = :ano
                                       GROUP BY YEAR(data_pagamento)";
                    $stmt_pagamentos = $pdo->prepare($sql_pagamentos);
                    $stmt_pagamentos->execute(['id' => $contrato_id_rel, 'ano' => $ano]);
                    $contrato_data['anos'] = $stmt_pagamentos->fetchAll(PDO::FETCH_ASSOC);
                }

                $dados[] = $contrato_data;
            }
        } else {
            $sql_relatorio = "SELECT c.titulo, c.validade, c.gestor, c.gestorsb, c.situacao, c.num_parcelas, c.data_cadastro
                              FROM contratos c
                              WHERE c.id = :id";
            $stmt_relatorio = $pdo->prepare($sql_relatorio);
            $stmt_relatorio->execute(['id' => $contrato_id]);
            $contrato = $stmt_relatorio->fetch(PDO::FETCH_ASSOC);

            if ($contrato) {
                $contrato_data = $contrato;
                if ($tipo_relatorio === 'mensal') {
                    $sql_pagamentos = "SELECT data_pagamento, valor
                                       FROM pagamentos
                                       WHERE contrato_id = :id AND MONTH(data_pagamento) = :mes
                                       ORDER BY data_pagamento";
                    $stmt_pagamentos = $pdo->prepare($sql_pagamentos);
                    $stmt_pagamentos->execute(['id' => $contrato_id, 'mes' => $mes]);
                    $contrato_data['pagamentos'] = $stmt_pagamentos->fetchAll(PDO::FETCH_ASSOC);
                } elseif ($tipo_relatorio === 'anual') {
                    $sql_pagamentos = "SELECT YEAR(data_pagamento) AS ano, SUM(valor) AS total_pago, COUNT(*) AS quantidade_pagamentos
                                       FROM pagamentos
                                       WHERE contrato_id = :id AND YEAR(data_pagamento) = :ano
                                       GROUP BY YEAR(data_pagamento)";
                    $stmt_pagamentos = $pdo->prepare($sql_pagamentos);
                    $stmt_pagamentos->execute(['id' => $contrato_id, 'ano' => $ano]);
                    $contrato_data['anos'] = $stmt_pagamentos->fetchAll(PDO::FETCH_ASSOC);
                }
                $dados[] = $contrato_data;
            }
        }

        // Gerar o conteúdo do relatório em HTML
        $html = '<h1>Relatório ' . htmlspecialchars($relatorio_todos ?: $tipo_relatorio) . '</h1>';
        $html .= '<table border="1">';
        if ($relatorio_todos === 'mensal_todos' || $tipo_relatorio === 'mensal') {
            $html .= '<tr><th>Título do Contrato</th><th>Nº de Parcelas</th><th>Histórico de Pagamentos (Data)</th><th>Histórico de Pagamentos (Valor)</th></tr>';
            foreach ($dados as $contrato) {
                $numPagamentos = isset($contrato['pagamentos']) && is_array($contrato['pagamentos']) ? count($contrato['pagamentos']) : 0;
                $parcelasRestantes = (isset($contrato['num_parcelas']) ? $contrato['num_parcelas'] : 0) - $numPagamentos;

                if (isset($contrato['pagamentos']) && is_array($contrato['pagamentos']) && count($contrato['pagamentos']) > 0) {
                    foreach ($contrato['pagamentos'] as $pagamento) {
                        $html .= '<tr>';
                        $html .= '<td>' . htmlspecialchars($contrato['titulo'] ?: 'N/A') . '</td>';
                        $html .= '<td>' . ($parcelasRestantes >= 0 ? $parcelasRestantes : 'N/A') . '</td>';
                        $html .= '<td>' . (new DateTime($pagamento['data_pagamento']))->format('d/m/Y') . '</td>';
                        $html .= '<td>R$ ' . number_format($pagamento['valor'], 2, ',', '.') . '</td>';
                        $html .= '</tr>';
                    }
                } else {
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($contrato['titulo'] ?: 'N/A') . '</td>';
                    $html .= '<td>' . (isset($contrato['num_parcelas']) ? $contrato['num_parcelas'] : 'N/A') . '</td>';
                    $html .= '<td colspan="2">Nenhum pagamento</td>';
                    $html .= '</tr>';
                }
            }
        } elseif ($relatorio_todos === 'anual_todos' || $tipo_relatorio === 'anual') {
            $html .= '<tr><th>Título do Contrato</th><th>Ano</th><th>Nº de Parcelas</th><th>Total Pago no Ano</th><th>Quantidade de Pagamentos</th></tr>';
            foreach ($dados as $contrato) {
                if (isset($contrato['anos']) && is_array($contrato['anos'])) {
                    foreach ($contrato['anos'] as $ano) {
                        $html .= '<tr>';
                        $html .= '<td>' . htmlspecialchars($contrato['titulo'] ?: 'N/A') . '</td>';
                        $html .= '<td>' . htmlspecialchars($ano['ano'] ?: 'N/A') . '</td>';
                        $html .= '<td>' . (isset($contrato['num_parcelas']) ? $contrato['num_parcelas'] : 'N/A') . '</td>';
                        $html .= '<td>R$ ' . number_format($ano['total_pago'], 2, ',', '.') . '</td>';
                        $html .= '<td>' . (isset($ano['quantidade_pagamentos']) ? $ano['quantidade_pagamentos'] : '0') . '</td>';
                        $html .= '</tr>';
                    }
                } else {
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($contrato['titulo'] ?: 'N/A') . '</td>';
                    $html .= '<td colspan="4">Nenhum dado disponível para este ano.</td>';
                    $html .= '</tr>';
                }
            }
        }
        $html .= '</table>';

        // Enviar e-mail com o relatório
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtps2.webmail.central.rj.gov.br'; // Substitua pelo seu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'impressora@central.rj.gov.br'; // Substitua pelo seu e-mail
        $mail->Password = 'central@123'; // Substitua pela sua senha
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('impressora@central.rj.gov.br', 'Sistema de Relatórios');
        $mail->addAddress($email_destinatario);
        $mail->isHTML(true);
        $mail->Subject = 'Relatório Agendado - ' . ($relatorio_todos ?: $tipo_relatorio);
        $mail->Body = $html;
        $mail->send();

        // Atualizar a data do próximo envio
        $proximo_envio = new DateTime();
        switch ($periodicidade) {
            case 'diario':
                $proximo_envio->modify('+1 day');
                break;
            case 'semanal':
                $proximo_envio->modify('+1 week');
                break;
            case 'mensal':
                $proximo_envio->modify('+1 month');
                break;
        }

        $sql_update = "UPDATE relatorios_agendados SET proximo_envio = :proximo_envio WHERE id = :id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([
            'proximo_envio' => $proximo_envio->format('Y-m-d H:i:s'),
            'id' => $id
        ]);
    }

    echo "Relatórios enviados com sucesso.";
} catch (Exception $e) {
    echo "Erro ao enviar relatórios: " . $e->getMessage();
}
?>
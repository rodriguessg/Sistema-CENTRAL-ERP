<?php
session_start();

// Definir fuso horário de São Paulo (BRT, UTC-3)
date_default_timezone_set('America/Sao_Paulo');

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\Sistema-CENTRAL-ERP\vendor\phpmailer\phpmailer\src/Exception.php';
require 'C:\xampp\htdocs\Sistema-CENTRAL-ERP\vendor\phpmailer\phpmailer\src/PHPMailer.php';
require 'C:\xampp\htdocs\Sistema-CENTRAL-ERP\vendor\phpmailer\phpmailer\src/SMTP.php';

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Adicionar coluna 'vitima' à tabela acidentes, se ainda não existir
    $pdo->exec("ALTER TABLE acidentes ADD COLUMN IF NOT EXISTS vitima VARCHAR(3) DEFAULT 'Não'");
    // Adicionar coluna 'paralizar_sistema' à tabela acidentes, se ainda não existir
    $pdo->exec("ALTER TABLE acidentes ADD COLUMN IF NOT EXISTS paralizar_sistema VARCHAR(3) DEFAULT 'Não'");
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$user = $_SESSION['username'];

// Buscar modelos da tabela bondes
$modelos = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT modelo FROM bondes ORDER BY modelo");
    $modelos = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Erro ao buscar modelos: " . $e->getMessage());
    $modelos = [];
}

// Buscar localizações (saída x retorno) da tabela viagens
$localizacoes = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT saida, retorno FROM viagens ORDER BY saida, retorno");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $value = "{$row['saida']} x {$row['retorno']}";
        $localizacoes[] = ['value' => $value, 'text' => $value];
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar localizações: " . $e->getMessage());
    $localizacoes = [];
}

$erro = '';
$sucesso = '';

// Manipular registro de novo acidente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update_status'])) {
    $descricao = $_POST['descricao'] ?? '';
    $localizacao = $_POST['localizacao'] ?? '';
    $severidade = $_POST['severidade'] ?? '';
    $categoria = $_POST['subcategoria'] ?? '';
    $cor = $_POST['cor'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $maquinistas = $_POST['maquinistas'] ?? '';
    $agentes = $_POST['agentes'] ?? '';
    $data = $_POST['data'] ?? date('Y-m-d H:i:s');
    $vitima = isset($_POST['vitima']) ? 'Sim' : 'Não';
    $paralizar_sistema = isset($_POST['paralizar_sistema']) ? 'Sim' : 'Não';

    if (!empty($data)) {
        $data = str_replace('T', ' ', $data) . ':00';
    }

    $valid_maquinistas = ['Sergio Lima', 'Adriano', 'Helio', 'M. Celestino', 'Leonardo', 'Andre'];
    $valid_agentes = ['Samir', 'Vinicius', 'P. Nascimento', 'Oliveira', 'Carlos'];
    $valid_localizacoes = array_column($localizacoes, 'value');

    if (empty($descricao) || empty($severidade) || empty($categoria) || empty($cor) || empty($modelo) || empty($maquinistas) || empty($agentes) || empty($localizacao) || empty($data)) {
        $erro = "Todos os campos obrigatórios devem ser preenchidos!";
    } elseif (!in_array($severidade, ['Leve', 'Moderado', 'Grave'])) {
        $erro = "Severidade inválida!";
    } elseif (!in_array($modelo, $modelos)) {
        $erro = "Modelo de bonde inválido!";
    } elseif (!in_array($maquinistas, $valid_maquinistas)) {
        $erro = "Maquinista inválido!";
    } elseif (!in_array($agentes, $valid_agentes)) {
        $erro = "Agente inválido!";
    } elseif (!in_array($localizacao, $valid_localizacoes)) {
        $erro = "Localização inválida!";
    } elseif (!DateTime::createFromFormat('Y-m-d H:i:s', $data)) {
        $erro = "Data e hora inválidas!";
    } else {
        try {
            $pdo->beginTransaction();

            // Inserir registro na tabela acidentes
            $sql = "INSERT INTO acidentes (descricao, localizacao, usuario, severidade, categoria, cor, modelo, maquinistas, agentes, data_registro, status, vitima, paralizar_sistema) 
                    VALUES (:descricao, :localizacao, :usuario, :severidade, :categoria, :cor, :modelo, :maquinistas, :agentes, :data_registro, 'em andamento', :vitima, :paralizar_sistema)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'descricao' => $descricao,
                'localizacao' => $localizacao,
                'usuario' => $user,
                'severidade' => $severidade,
                'categoria' => $categoria,
                'cor' => $cor,
                'modelo' => $modelo,
                'maquinistas' => $maquinistas,
                'agentes' => $agentes,
                'data_registro' => $data,
                'vitima' => $vitima,
                'paralizar_sistema' => $paralizar_sistema
            ]);
            $acidente_id = $pdo->lastInsertId();

            // Inserir registro na tabela viagens
            $sql_viagem = "INSERT INTO viagens (bonde, saida, retorno, maquinista, agente, hora, tipo_viagem, data, created_at) 
                           VALUES (:bonde, :saida, :retorno, :maquinista, :agente, :hora, :tipo_viagem, :data, :created_at)";
            $stmt_viagem = $pdo->prepare($sql_viagem);
            $saida_retorno = explode(' x ', $localizacao);
            $saida = $saida_retorno[0] ?? '';
            $retorno = $saida_retorno[1] ?? '';
            $tipo_viagem = $paralizar_sistema === 'Sim' ? 'operação paralizada por motivo de uma nova ocorrência' : 'operação mantida apesar da ocorrência';
            $hora = date('H:i:s', strtotime($data));
            $data_viagem = date('Y-m-d', strtotime($data));
            $created_at = date('Y-m-d H:i:s');

            $stmt_viagem->execute([
                'bonde' => $modelo,
                'saida' => $saida,
                'retorno' => $retorno,
                'maquinista' => $maquinistas,
                'agente' => $agentes,
                'hora' => $hora,
                'tipo_viagem' => $tipo_viagem,
                'data' => $data_viagem,
                'created_at' => $created_at
            ]);

            // Commit da transação
            $pdo->commit();

            $sucesso = "Acidente registrado com sucesso!";

            // Enviar e-mail de notificação
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtps2.ebmail.rj.gov.br';
                $mail->SMTPAuth = true;
                $mail->Username = 'impressora@central.rj.gov.br';
                $mail->Password = 'central@123';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;
                $mail->Timeout = 5;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('impressora@central.rj.gov.br', 'Notificações de Ocorrências');
                $mail->addAddress('grodrigues@central.rj.gov.br');
                $mail->addAddress('alexandrerocha@central.rj.gov.br');

                $mail->isHTML(true);
                $mail->Subject = "Novo Acidente Registrado - ID $acidente_id";
                $mail->Body = "
                    <h2>Novo Acidente Registrado</h2>
                    <p>Um novo acidente foi registrado no sistema. Detalhes abaixo:</p>
                    <table border='1' style='border-collapse: collapse; width: 100%;'>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>ID</th><td style='padding: 8px;'>$acidente_id</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Descrição</th><td style='padding: 8px;'>" . htmlspecialchars($descricao) . "</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Localização</th><td style='padding: 8px;'>" . htmlspecialchars($localizacao) . "</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Usuário</th><td style='padding: 8px;'>" . htmlspecialchars($user) . "</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Severidade</th><td style='padding: 8px;'>" . htmlspecialchars($severidade) . "</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Categoria</th><td style='padding: 8px;'>" . htmlspecialchars($categoria) . "</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Modelo do Bonde</th><td style='padding: 8px;'>" . htmlspecialchars($modelo) . "</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Maquinistas</th><td style='padding: 8px;'>" . htmlspecialchars($maquinistas) . "</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Agentes</th><td style='padding: 8px;'>" . htmlspecialchars($agentes) . "</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Data e Hora de Registro</th><td style='padding: 8px;'>" . date('d/m/Y H:i', strtotime($data)) . "</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Houve Vítima?</th><td style='padding: 8px;'>$vitima</td></tr>
                        <tr><th style='padding: 8px; background-color: #f2f2f2;'>Paralizar Sistema?</th><td style='padding: 8px;'>$paralizar_sistema</td></tr>
                    </table>
                    <p>Por favor, verifique o sistema para mais detalhes ou ações necessárias.</p>
                ";
                $mail->AltBody = "Novo Acidente Registrado - ID: $acidente_id\nDescrição: $descricao\nLocalização: $localizacao\nUsuário: $user\nSeveridade: $severidade\nCategoria: $categoria\nModelo: $modelo\nMaquinistas: $maquinistas\nAgentes: $agentes\nData e Hora: " . date('d/m/Y H:i', strtotime($data)) . "\nHouve Vítima? $vitima\nParalizar Sistema? $paralizar_sistema";

                $start_time = microtime(true);
                try {
                    $mail->send();
                } catch (Exception $e) {
                    $elapsed_time = microtime(true) - $start_time;
                    if ($elapsed_time >= 5) {
                        $erro .= "Erro: O envio do e-mail excedeu o tempo limite de 5 segundos. Detalhes: " . $mail->ErrorInfo;
                    } else {
                        $erro .= "Erro ao enviar e-mail de notificação: " . $mail->ErrorInfo;
                    }
                }
            } catch (Exception $e) {
                $erro .= "Erro na configuração do e-mail: " . $mail->ErrorInfo;
            }

            header('Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=acidente&pagina=/Sistema-CENTRAL-ERP/reportacidentes.php');
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $erro = "Erro ao registrar o acidente: " . $e->getMessage();
        }
    }
}

// Manipular atualização de status e órgãos de emergência
if (isset($_POST['update_status']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $policia = isset($_POST['policia'][$id]) ? 1 : 0;
    $bombeiros = isset($_POST['bombeiros'][$id]) ? 1 : 0;
    $samu = isset($_POST['samu'][$id]) ? 1 : 0;

    try {
        $pdo->beginTransaction();

        // Atualizar o status do acidente
        $sql = "UPDATE acidentes SET status = 'resolvido', policia = :policia, bombeiros = :bombeiros, samu = :samu WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'policia' => $policia,
            'bombeiros' => $bombeiros,
            'samu' => $samu,
            'id' => $id
        ]);

        // Buscar informações do acidente para obter o bonde e localização
        $sql_acidente = "SELECT modelo, localizacao, maquinistas, agentes, paralizar_sistema FROM acidentes WHERE id = :id";
        $stmt_acidente = $pdo->prepare($sql_acidente);
        $stmt_acidente->execute(['id' => $id]);
        $acidente = $stmt_acidente->fetch(PDO::FETCH_ASSOC);

      

        // Commit da transação
        $pdo->commit();
        header("Location: reportacidentes.php?success=2");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $erro = "Erro ao atualizar o status: " . $e->getMessage();
    }
}

// Buscar todos os registros - ORDENAR POR DATA MAIS RECENTE PRIMEIRO
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
try {
    $sql = "SELECT id, descricao, localizacao, usuario, severidade, categoria, cor, modelo, maquinistas, agentes, data_registro, status, policia, bombeiros, samu, vitima, paralizar_sistema 
            FROM acidentes 
            WHERE descricao LIKE :search OR localizacao LIKE :search OR severidade LIKE :search OR categoria LIKE :search 
            ORDER BY data_registro DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => $search]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $fetchSuccess = !empty($result);
} catch (PDOException $e) {
    $erro = "Erro ao obter resultados: " . $e->getMessage();
    $result = [];
    $fetchSuccess = false;
}

if (!$fetchSuccess) {
    $erro = "Nenhum dado foi recuperado. Verifique a query ou os dados na tabela 'acidentes'.";
}

// Converter $result para JSON para uso no JavaScript
$resultJson = json_encode($result, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_TAG);

// Mapear cores para classes CSS
$colorClasses = [
    'Verde' => 'severity-green',
    'Amarelo' => 'severity-yellow',
    'Vermelho' => 'severity-red',
    'Amarelo/Vermelho' => 'severity-yellow-red'
];

// Include header.php only after all header() calls
include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Ocorrências</title>
    <link rel="stylesheet" href="./src/bonde/style/acidente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .data-table td {
            font-weight: bold;
            font-size: 0.9rem;
            text-align: center;
        }
        
        .data-table th {
            font-weight: bold;
            font-size: 0.9rem;
            text-align: center;
        }
        
        .bonde-name {
            color: #2563eb;
            font-weight: bold;
        }

        .localizacao-name {
            color: red;
            font-weight: bold;
        }
        
        .data-table {
            font-size: 0.9rem;
        }

        .severity-green {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            padding: 0.125rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
            border: 1px solid rgba(16, 185, 129, 0.2);
            font-size: 0.75rem;
        }

        .severity-yellow {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            padding: 0.125rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
            border: 1px solid rgba(245, 158, 11, 0.2);
            font-size: 0.75rem;
        }

        .severity-red {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 0.125rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.75rem;
        }

        .severity-yellow-red {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(239, 68, 68, 0.1) 100%);
            color: #ef4444;
            padding: 0.125rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.75rem;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            padding: 20px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .modal-header-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-icon {
            font-size: 1.5rem;
            color: #1e90ff;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .modal-subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #6b7280;
        }

        .modal-close:hover {
            color: #000;
        }

        .modal-body {
            padding: 10px;
        }

        .report-content {
            white-space: pre-wrap;
            margin-bottom: 20px;
        }

        .report-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .meta-item {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .meta-icon {
            font-size: 1.2rem;
            color: #1e90ff;
        }

        .meta-label {
            font-weight: 600;
            color: #374151;
        }

        .meta-value {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="caderno">
        <div class="form-container">
            <div class="section-header">
                <div class="header-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="header-content">
                    <h1>Registrar Ocorrências</h1>
                    <p>Sistema de registro e acompanhamento de acidentes e ocorrências</p>
                </div>
            </div>
        </div>

        <?php if ($erro): ?>
            <div class="message-container error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($sucesso || isset($_GET['success'])): ?>
            <div class="message-container success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($sucesso ?: ($_GET['success'] == 1 ? "OCORRÊNCIAS registrado com sucesso!" : "Status do acidente atualizado para resolvido!")); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <div class="section-header">
                <div class="header-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="header-content">
                    <h2>Novo Registro de Ocorrência</h2>
                    <p>Preencha os dados da ocorrência</p>
                </div>
            </div>
            
            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modelo">
                                <i class="fas fa-train"></i>
                                Modelo do Bonde:
                            </label>
                            <select id="modelo" name="modelo" required>
                                <option value="">Selecione o modelo</option>
                                <?php foreach ($modelos as $modelo): ?>
                                    <option value="<?php echo htmlspecialchars($modelo); ?>"><?php echo htmlspecialchars($modelo); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="categoria">
                                <i class="fas fa-tags"></i>
                                Categoria:
                            </label>
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
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="subcategoria">
                                <i class="fas fa-list"></i>
                                Subcategoria:
                            </label>
                            <select id="subcategoria" name="subcategoria" required onchange="updateSeveridadeECor()">
                                <option value="">Selecione a subcategoria</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="severidade">
                                <i class="fas fa-thermometer-half"></i>
                                Severidade:
                            </label>
                            <select id="severidade" name="severidade">
                                <option value="">Selecione a severidade</option>
                                <option value="Leve">Leve</option>
                                <option value="Moderado">Moderado</option>
                                <option value="Grave">Grave</option>
                            </select>
                            <input type="hidden" id="cor" name="cor">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="localizacao">
                                <i class="fas fa-map-marker-alt"></i>
                                Localização:
                            </label>
                            <select id="localizacao" name="localizacao" required>
                                <option value="">Selecione</option>
                                <?php foreach ($localizacoes as $loc): ?>
                                    <option value="<?php echo htmlspecialchars($loc['value']); ?>"><?php echo htmlspecialchars($loc['text']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="data">
                                <i class="fas fa-calendar-alt"></i>
                                Data e Hora do Acidente:
                            </label>
                            <input type="datetime-local" id="data" name="data" value="<?php echo date('Y-m-d\TH:i', strtotime('now')); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="maquinistas">
                                <i class="fas fa-user-tie"></i>
                                Maquinistas:
                            </label>
                            <select id="maquinistas" name="maquinistas" required>
                                <option value="">Selecione</option>
                                <option value="Sergio Lima">Sergio Lima</option>
                                <option value="Adriano">Adriano</option>
                                <option value="Helio">Helio</option>
                                <option value="M. Celestino">M. Celestino</option>
                                <option value="Leonardo">Leonardo</option>
                                <option value="Andre">Andre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="agentes">
                                <i class="fas fa-user-shield"></i>
                                Agentes:
                            </label>
                            <select id="agentes" name="agentes" required>
                                <option value="">Selecione</option>
                                <option value="Samir">Samir</option>
                                <option value="Vinicius">Vinicius</option>
                                <option value="P. Nascimento">P. Nascimento</option>
                                <option value="Oliveira">Oliveira</option>
                                <option value="Carlos">Carlos</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="vitima">
                                <i class="fas fa-user-injured"></i>
                                Houve Vítima?
                            </label>
                            <input type="checkbox" id="vitima" name="vitima">
                        </div>
                        <div class="form-group">
                            <label for="paralizar_sistema">
                                <i class="fas fa-pause-circle"></i>
                                Precisa paralizar o sistema?
                            </label>
                            <input type="checkbox" id="paralizar_sistema" name="paralizar_sistema">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descricao">
                            <i class="fas fa-file-alt"></i>
                            Descrição do Acidente:
                        </label>
                        <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva o acidente, danos, envolvidos, e ações tomadas"></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit">
                        <i class="fas fa-save"></i>
                        Salvar Registro
                    </button>
                </div>
            </form>
        </div>

        <div class="search-section">
            <div class="section-header">
                <div class="header-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="header-content">
                    <h3>Filtros de Pesquisa</h3>
                    <p>Utilize os filtros para encontrar ocorrências específicas</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <div class="form-group">
                        <label for="search">
                            <i class="fas fa-search"></i>
                            Pesquisar:
                        </label>
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Pesquisar por descrição, localização, severidade ou categoria">
                    </div>

                    <div class="form-group">
                        <label for="severityFilter">
                            <i class="fas fa-filter"></i>
                            Filtrar por Severidade:
                        </label>
                        <select id="severityFilter" name="severityFilter">
                            <option value="">Todas</option>
                            <option value="Leve">Leve</option>
                            <option value="Moderado">Moderado</option>
                            <option value="Grave">Grave</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="dateStart">
                            <i class="fas fa-calendar"></i>
                            Data e Hora de Registro (Início):
                        </label>
                        <input type="datetime-local" id="dateStart" name="dateStart">
                    </div>

                    <div class="form-group">
                        <label for="dateEnd">
                            <i class="fas fa-calendar"></i>
                            Data e Hora de Registro (Fim):
                        </label>
                        <input type="datetime-local" id="dateEnd" name="dateEnd">
                    </div>
                </div>
            </div>
        </div>

        <div class="table-section">
            <div class="table-header">
                <h3>
                    <i class="fas fa-history"></i>
                    Histórico de Ocorrências
                </h3>
                <div class="table-info">
                    <div class="record-count" id="totalAccidents">
                        <i class="fas fa-list-ol"></i>
                        Total de Ocorrências: <?php echo count($result); ?>
                    </div>
                </div>
            </div>

            <?php if (!is_array($result)): ?>
                <div class="message-container error">
                    <i class="fas fa-exclamation-triangle"></i>
                    Erro: Dados inválidos. Valor: <?php echo htmlspecialchars(var_export($result, true)); ?>
                </div>
                <?php $result = []; ?>
            <?php endif; ?>

            <?php if (empty($result)): ?>
                <div class="no-data">
                    <i class="fas fa-inbox"></i>
                    <span>Nenhum acidente registrado.</span>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="data-table" id="accidentsTable">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i>ID</th>
                                <th><i class="fas fa-train"></i>Bonde</th>
                                <th><i class="fas fa-map-marker-alt"></i>Localização</th>
                                <th><i class="fas fa-user"></i>Usuário</th>
                                <th><i class="fas fa-user-tie"></i>Maquinista</th>
                                <th><i class="fas fa-user-shield"></i>Agente</th>
                                <th><i class="fas fa-thermometer-half"></i>Severidade</th>
                                <th><i class="fas fa-tags"></i>Categoria</th>
                                <th><i class="fas fa-file-alt"></i>Descrição</th>
                                <th><i class="fas fa-calendar"></i>Data e Hora de Registro</th>
                                <th><i class="fas fa-user-injured"></i>Vítima</th>
                                <th><i class="fas fa-pause-circle"></i>Paralizar Sistema</th>
                                <th><i class="fas fa-shield-alt"></i>Polícia</th>
                                <th><i class="fas fa-fire-extinguisher"></i>Bombeiros</th>
                                <th><i class="fas fa-ambulance"></i>SAMU</th>
                                <th><i class="fas fa-info-circle"></i>Status</th>
                                <th><i class="fas fa-cogs"></i>Ação</th>
                            </tr>
                        </thead>
                        <tbody id="accidentsTableBody">
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination" id="pagination">
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal-overlay" id="descriptionModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h3 class="modal-title">Descrição da Ocorrência</h3>
                        <p class="modal-subtitle" id="modalSubtitle">Detalhes completos do incidente</p>
                    </div>
                </div>
                <button class="modal-close" onclick="closeDescriptionModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="description-content" id="modalDescription">
                    Conteúdo da descrição será inserido aqui 
                </div>
                <div class="description-meta" id="modalMeta">
                    Metadados serão inseridos aqui 
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="reportModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h3 class="modal-title">Relatório da Ocorrência</h3>
                        <p class="modal-subtitle" id="reportModalSubtitle">Detalhes completos do incidente</p>
                    </div>
                </div>
                <button class="modal-close" onclick="closeReportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="report-content" id="reportModalContent">
                    Conteúdo do relatório será inserido aqui
                </div>
                <div class="report-meta" id="reportModalMeta">
                    Metadados do relatório serão inseridos aqui
                </div>
                <button class="create-form-btn" onclick="createForm()">
                    <i class="fas fa-file-export"></i>
                    Criar Formulário
                </button>
            </div>
        </div>
    </div>

    <script>
        const acidentes = <?php echo $resultJson; ?>;
        const colorClasses = <?php echo json_encode($colorClasses); ?>;
        const perPage = 5;
        let currentPage = 1;
        let filteredData = acidentes;

        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return 'N/A';
            const date = new Date(dateTimeStr);
            return date.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
        }

        function createDescriptionCell(description, rowIndex) {
            const maxLength = 50;
            const truncated = description.length > maxLength ? 
                description.substring(0, maxLength) + '...' : description;
            
            return `
                <div class="description-cell" data-row-index="${rowIndex}">
                    <div class="description-preview">
                        <i class="fas fa-eye"></i>
                        <span>${escapeHtml(truncated)}</span>
                    </div>
                </div>
            `;
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function openDescriptionModal(description, rowData) {
            try {
                const modal = document.getElementById('descriptionModal');
                const modalDescription = document.getElementById('modalDescription');
                const modalSubtitle = document.getElementById('modalSubtitle');
                const modalMeta = document.getElementById('modalMeta');

                if (!description || !rowData) {
                    console.error('Dados inválidos para o modal:', { description, rowData });
                    return;
                }

                modalDescription.textContent = description;
                modalSubtitle.textContent = `Ocorrência #${rowData.id} - ${rowData.categoria || 'Categoria não informada'}`;

                modalMeta.innerHTML = `
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">ID da Ocorrência</div>
                            <div class="meta-value">#${rowData.id || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Localização</div>
                            <div class="meta-value">${rowData.localizacao || 'Não informado'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Usuário</div>
                            <div class="meta-value">${rowData.usuario || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Maquinista</div>
                            <div class="meta-value">${rowData.maquinistas || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Agente</div>
                            <div class="meta-value">${rowData.agentes || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Severidade</div>
                            <div class="meta-value">${rowData.severidade || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-train"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Modelo do Bonde</div>
                            <div class="meta-value">${rowData.modelo || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Data e Hora de Registro</div>
                            <div class="meta-value">${formatDateTime(rowData.data_registro)}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Houve Vítima?</div>
                            <div class="meta-value">${rowData.vitima || 'Não'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-pause-circle"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Paralizar Sistema?</div>
                            <div class="meta-value">${rowData.paralizar_sistema || 'Não'}</div>
                        </div>
                    </div>
                `;

                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Erro ao abrir modal:', error);
                alert('Erro ao exibir a descrição. Tente novamente.');
            }
        }

        function closeDescriptionModal() {
            const modal = document.getElementById('descriptionModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function openReportModal(rowData) {
            try {
                const modal = document.getElementById('reportModal');
                const modalContent = document.getElementById('reportModalContent');
                const modalSubtitle = document.getElementById('reportModalSubtitle');
                const modalMeta = document.getElementById('reportModalMeta');

                if (!rowData) {
                    console.error('Dados inválidos para o modal de relatório:', rowData);
                    return;
                }

                modalContent.innerHTML = `
                    <h4>Relatório da Ocorrência #${rowData.id}</h4>
                    <p><strong>Descrição:</strong> ${escapeHtml(rowData.descricao || 'Não informado')}</p>
                    <p><strong>Resumo:</strong> Ocorrência registrada com severidade ${rowData.severidade || 'N/A'} na localização ${rowData.localizacao || 'Não informado'}.</p>
                    <p><strong>Órgãos de Emergência:</strong></p>
                    <ul>
                        <li>Polícia: ${rowData.policia == 1 ? 'Acionada' : 'Não acionada'}</li>
                        <li>Bombeiros: ${rowData.bombeiros == 1 ? 'Acionados' : 'Não acionados'}</li>
                        <li>SAMU: ${rowData.samu == 1 ? 'Acionado' : 'Não acionado'}</li>
                    </ul>
                    <p><strong>Houve Vítima?:</strong> ${rowData.vitima || 'Não'}</p>
                    <p><strong>Paralizar Sistema?:</strong> ${rowData.paralizar_sistema || 'Não'}</p>
                    <p><strong>Status Atual:</strong> ${rowData.status || 'N/A'}</p>
                `;
                modalSubtitle.textContent = `Ocorrência #${rowData.id} - ${rowData.categoria || 'Categoria não informada'}`;

                modalMeta.innerHTML = `
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">ID da Ocorrência</div>
                            <div class="meta-value">#${rowData.id || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Localização</div>
                            <div class="meta-value">${rowData.localizacao || 'Não informado'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Usuário</div>
                            <div class="meta-value">${rowData.usuario || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Maquinista</div>
                            <div class="meta-value">${rowData.maquinistas || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Agente</div>
                            <div class="meta-value">${rowData.agentes || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Severidade</div>
                            <div class="meta-value">${rowData.severidade || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-train"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Modelo do Bonde</div>
                            <div class="meta-value">${rowData.modelo || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Data e Hora de Registro</div>
                            <div class="meta-value">${formatDateTime(rowData.data_registro)}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Houve Vítima?</div>
                            <div class="meta-value">${rowData.vitima || 'Não'}</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-pause-circle"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Paralizar Sistema?</div>
                            <div class="meta-value">${rowData.paralizar_sistema || 'Não'}</div>
                        </div>
                    </div>
                `;

                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Erro ao abrir modal de relatório:', error);
                alert('Erro ao exibir o relatório. Tente novamente.');
            }
        }

        function closeReportModal() {
            const modal = document.getElementById('reportModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function createForm() {
            alert('Funcionalidade de criação de formulário será implementada aqui.');
        }

        document.addEventListener('click', function(e) {
            const descriptionCell = e.target.closest('.description-cell');
            const reportBtn = e.target.closest('.report-btn');
            if (descriptionCell) {
                const rowIndex = parseInt(descriptionCell.dataset.rowIndex);
                if (!isNaN(rowIndex) && filteredData[rowIndex]) {
                    const rowData = filteredData[rowIndex];
                    const description = rowData.descricao || '';
                    openDescriptionModal(description, rowData);
                } else {
                    console.error('Dados da linha não encontrados:', rowIndex);
                }
            } else if (reportBtn) {
                const rowIndex = parseInt(reportBtn.dataset.rowIndex);
                if (!isNaN(rowIndex) && filteredData[rowIndex]) {
                    const rowData = filteredData[rowIndex];
                    openReportModal(rowData);
                } else {
                    console.error('Dados da linha não encontrados:', rowIndex);
                }
            }
        });

        document.getElementById('descriptionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDescriptionModal();
            }
        });

        document.getElementById('reportModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReportModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDescriptionModal();
                closeReportModal();
            }
        });

        function updateTotalAccidents(data) {
            const totalAccidents = document.getElementById('totalAccidents');
            totalAccidents.innerHTML = `<i class="fas fa-list-ol"></i> Ocorrências Registradas: ${data.length}`;
        }

        function getEmergencyServices(categoria) {
            const emergencyServices = {
                "Atropelamento de pedestre": { policia: true, bombeiros: false, samu: true },
                "Colisão com veículo": { policia: true, bombeiros: false, samu: true },
                "Colisão com motocicleta/bicicleta": { policia: true, bombeiros: false, samu: true },
                "Manifestação/bloqueio proposital na via": { policia: true, bombeiros: false, samu: false },
                "Ato de vandalismo no bonde": { policia: true, bombeiros: false, samu: false },
                "Agressão entre passageiros": { policia: true, bombeiros: false, samu: false },
                "Roubo ou tentativa de assalto": { policia: true, bombeiros: false, samu: false },
                "Ameaça de bomba / suspeita de artefato": { policia: true, bombeiros: false, samu: false },
                "Descarrilamento com vítimas": { policia: false, bombeiros: true, samu: true },
                "Alagamento de via": { policia: false, bombeiros: true, samu: false },
                "Deslizamento de encosta": { policia: false, bombeiros: true, samu: false },
                "Rompimento de trilho / falha estrutural": { policia: false, bombeiros: true, samu: false },
                "Incêndio em área próxima à via": { policia: false, bombeiros: true, samu: false },
                "Queda de árvore sobre a rede elétrica": { policia: false, bombeiros: true, samu: false },
                "Passageiro passando mal (grave)": { policia: false, bombeiros: false, samu: true },
                "Acidente interno com vítima grave": { policia: false, bombeiros: false, samu: true }
            };
            return emergencyServices[categoria] || { policia: false, bombeiros: false, samu: false };
        }

        function renderTable(page, data) {
            const start = (page - 1) * perPage;
            const end = start + perPage;
            const pageData = data.slice(start, end);
            const tbody = document.getElementById('accidentsTableBody');
            tbody.innerHTML = '';

            if (pageData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="17" class="no-data">Nenhum acidente encontrado.</td></tr>';
                return;
            }

            pageData.forEach((row, index) => {
                if (typeof row !== 'object' || row === null) {
                    console.error('Dados inválidos:', row);
                    return;
                }
                
                const globalIndex = start + index;
                const emergencyServices = getEmergencyServices(row.categoria);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><span class="id-badge">${row.id || ''}</span></td>
                    <td><span class="bonde-name">${row.modelo || ''}</span></td>
                    <td><span class="localizacao-name">${row.localizacao || ''}</td>
                    <td>${row.usuario || ''}</td>
                    <td>${row.maquinistas || ''}</td>
                    <td>${row.agentes || ''}</td>
                    <td><span class="${colorClasses[row.cor] || ''}">${row.severidade || ''}</span></td>
                    <td>${row.categoria || ''}</td>
                    <td>${createDescriptionCell(row.descricao || '', globalIndex)}</td>
                    <td class="date">${formatDateTime(row.data_registro)}</td>
                    <td>${row.vitima || 'Não'}</td>
                    <td>${row.paralizar_sistema || 'Não'}</td>
                    <td>
                        ${row.status === 'em andamento' ? 
                            `<form method="POST" action="" id="form-${row.id}">
                                <input type="checkbox" name="policia[${row.id}]" ${emergencyServices.policia ? 'checked' : ''}>
                                <input type="hidden" name="id" value="${row.id}">
                                <input type="hidden" name="update_status" value="1">
                            </form>` : 
                            (row.policia == 1 ? '<i class="fas fa-check" style="color: #10b981;"></i>' : '')}
                    </td>
                    <td>
                        ${row.status === 'em andamento' ? 
                            `<input type="checkbox" name="bombeiros[${row.id}]" form="form-${row.id}" ${emergencyServices.bombeiros ? 'checked' : ''}>` : 
                            (row.bombeiros == 1 ? '<i class="fas fa-check" style="color: #10b981;"></i>' : '')}
                    </td>
                    <td>
                        ${row.status === 'em andamento' ? 
                            `<input type="checkbox" name="samu[${row.id}]" form="form-${row.id}" ${emergencyServices.samu ? 'checked' : ''}>` : 
                            (row.samu == 1 ? '<i class="fas fa-check" style="color: #10b981;"></i>' : '')}
                    </td>
                    <td>${row.status || ''}</td>
                    <td>
                        ${row.status === 'em andamento' ? 
                            `<button type="submit" form="form-${row.id}" class="status-btn">
                                <i class="fas fa-check"></i>
                                Resolver
                            </button>` : 
                            `<button class="status-btn resolved" disabled>
                                <i class="fas fa-check-circle"></i>
                                Resolvido
                            </button>
                            <button class="report-btn" data-row-index="${globalIndex}">
                                <i class="fas fa-file-alt"></i>
                                Gerar Relatório
                            </button>`}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function renderPagination(data) {
            const totalPages = Math.ceil(data.length / perPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            updateTotalAccidents(data);

            const prevButton = document.createElement('button');
            prevButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevButton.disabled = currentPage === 1;
            prevButton.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable(currentPage, filteredData);
                    renderPagination(filteredData);
                }
            };
            pagination.appendChild(prevButton);

            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.textContent = i;
                pageButton.className = i === currentPage ? 'active' : '';
                pageButton.onclick = () => {
                    currentPage = i;
                    renderTable(currentPage, filteredData);
                    renderPagination(filteredData);
                };
                pagination.appendChild(pageButton);
            }

            const nextButton = document.createElement('button');
            nextButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextButton.disabled = currentPage === totalPages || totalPages === 0;
            nextButton.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable(currentPage, filteredData);
                    renderPagination(filteredData);
                }
            };
            pagination.appendChild(nextButton);
        }

        function filterData() {
            const searchTerm = document.getElementById('search').value.toLowerCase().trim();
            const severityFilter = document.getElementById('severityFilter').value;
            const dateStart = document.getElementById('dateStart').value;
            const dateEnd = document.getElementById('dateEnd').value;

            return acidentes.filter(row => {
                const matchesSearch = !searchTerm || 
                    (row.descricao?.toLowerCase().includes(searchTerm) || false) ||
                    (row.localizacao?.toLowerCase().includes(searchTerm) || false) ||
                    (row.severidade?.toLowerCase().includes(searchTerm) || false) ||
                    (row.categoria?.toLowerCase().includes(searchTerm) || false) ||
                    (row.maquinistas?.toLowerCase().includes(searchTerm) || false) ||
                    (row.agentes?.toLowerCase().includes(searchTerm) || false) ||
                    (row.vitima?.toLowerCase().includes(searchTerm) || false) ||
                    (row.paralizar_sistema?.toLowerCase().includes(searchTerm) || false);

                const matchesSeverity = !severityFilter || row.severidade === severityFilter;

                let matchesDate = true;
                if (dateStart) {
                    const rowDate = new Date(row.data_registro);
                    const startDate = new Date(dateStart);
                    matchesDate = rowDate >= startDate;
                    if (dateEnd) {
                        const endDate = new Date(dateEnd);
                        matchesDate = matchesDate && rowDate <= endDate;
                    }
                }

                return matchesSearch && matchesSeverity && matchesDate;
            });
        }

        function applyFilters() {
            currentPage = 1;
            filteredData = filterData();
            renderTable(currentPage, filteredData);
            renderPagination(filteredData);
        }

        document.getElementById('search').addEventListener('input', applyFilters);
        document.getElementById('severityFilter').addEventListener('change', applyFilters);
        document.getElementById('dateStart').addEventListener('change', applyFilters);
        document.getElementById('dateEnd').addEventListener('change', applyFilters);

        renderTable(currentPage, filteredData);
        renderPagination(filteredData);

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

            subcategoriaSelect.innerHTML = '<option value="">Selecione a subcategoria</option>';
            document.getElementById('severidade').value = '';
            document.getElementById('cor').value = '';

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

            severidadeSelect.value = '';
            corInput.value = '';

            if (selectedCategoria && selectedSubcategoria && subcategorias[selectedCategoria]) {
                const subcategoria = subcategorias[selectedCategoria].find(sub => sub.value === selectedSubcategoria);
                if (subcategoria) {
                    severidadeSelect.value = subcategoria.severidade;
                    corInput.value = subcategoria.cor;
                }
            }
        }
    </script>
</body>
</html>
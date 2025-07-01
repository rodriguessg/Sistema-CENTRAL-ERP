<?php
// api.php – Bonde de Santa Teresa API
// Lida com operações CRUD e busca de dados via AJAX

header('Content-Type: application/json');

// ======================= CONEXÃO BD ================================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "gm_sicbd"; // Certifique-se de que este é o nome correto do seu banco de dados

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => "Erro na conexão: " . $e->getMessage()]);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'get_initial_data':
        getInitialData($conn);
        break;
    case 'add_viagem':
        addViagem($conn);
        break;
    case 'update_viagem':
        updateViagem($conn);
        break;
    case 'delete_viagem':
        deleteViagem($conn);
        break;
    case 'clear_transactions':
        clearTransactions($conn);
        break;
    case 'get_viagens':
        getViagens($conn);
        break;
    case 'get_totals':
        getTotals($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
        break;
}

// Função para buscar dados iniciais para os selects
function getInitialData($conn) {
    try {
        $bondes = $conn->query("SELECT id, modelo FROM bondes ORDER BY modelo")->fetchAll(PDO::FETCH_ASSOC);
        $destinos = $conn->query("SELECT id, nome FROM destinos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
        $maquinistas = $conn->query("SELECT id, nome FROM maquinistas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
        $agentes = $conn->query("SELECT id, nome FROM agentes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'bondes' => $bondes,
            'destinos' => $destinos,
            'maquinistas' => $maquinistas,
            'agentes' => $agentes
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar dados iniciais: ' . $e->getMessage()]);
    }
}

// Função para adicionar uma nova viagem
function addViagem($conn) {
    $bonde_id      = filter_input(INPUT_POST, 'bonde_id', FILTER_VALIDATE_INT);
    $origem_id     = filter_input(INPUT_POST, 'origem_id', FILTER_VALIDATE_INT);
    $destino_id    = filter_input(INPUT_POST, 'destino_id', FILTER_VALIDATE_INT); // Pode ser NULL para subida pendente
    $maquinista_id = filter_input(INPUT_POST, 'maquinista_id', FILTER_VALIDATE_INT);
    $agente_id     = filter_input(INPUT_POST, 'agente_id', FILTER_VALIDATE_INT);
    $hora          = filter_input(INPUT_POST, 'hora', FILTER_SANITIZE_STRING);
    $pagantes      = filter_input(INPUT_POST, 'pagantes', FILTER_VALIDATE_INT);
    $moradores     = filter_input(INPUT_POST, 'moradores', FILTER_VALIDATE_INT);
    $gratuidade    = filter_input(INPUT_POST, 'gratuidade', FILTER_VALIDATE_INT); // Corresponde a grat_pcd_idoso
    $tipo_viagem   = filter_input(INPUT_POST, 'tipo_viagem', FILTER_SANITIZE_STRING);
    $data_viagem   = filter_input(INPUT_POST, 'data_viagem', FILTER_SANITIZE_STRING);

    // Validação básica
    if (!$bonde_id || !$origem_id || !$maquinista_id || !$agente_id || !$hora || $pagantes === null || $moradores === null || $gratuidade === null || !$tipo_viagem || !$data_viagem) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos para adicionar a viagem.']);
        return;
    }

    $total_passageiros = $pagantes + $moradores + $gratuidade;
    if ($total_passageiros > 32) {
        echo json_encode(['success' => false, 'message' => 'Capacidade máxima de 32 passageiros excedida.']);
        return;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO viagens (bonde_id, origem_id, destino_id, maquinista_id, agente_id, hora, pagantes, moradores, gratuidade, tipo_viagem, data_viagem) VALUES (:bonde_id, :origem_id, :destino_id, :maquinista_id, :agente_id, :hora, :pagantes, :moradores, :gratuidade, :tipo_viagem, :data_viagem)");
        $stmt->execute([
            ':bonde_id'      => $bonde_id,
            ':origem_id'     => $origem_id,
            ':destino_id'    => $destino_id ?: null, // Pode ser null para subida pendente
            ':maquinista_id' => $maquinista_id,
            ':agente_id'     => $agente_id,
            ':hora'          => $hora,
            ':pagantes'      => $pagantes,
            ':moradores'     => $moradores,
            ':gratuidade'    => $gratuidade,
            ':tipo_viagem'   => $tipo_viagem,
            ':data_viagem'   => $data_viagem
        ]);
        echo json_encode(['success' => true, 'message' => 'Viagem adicionada com sucesso!', 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao adicionar viagem: ' . $e->getMessage()]);
    }
}

// Função para atualizar uma viagem existente
function updateViagem($conn) {
    $id            = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $bonde_id      = filter_input(INPUT_POST, 'bonde_id', FILTER_VALIDATE_INT);
    $origem_id     = filter_input(INPUT_POST, 'origem_id', FILTER_VALIDATE_INT);
    $destino_id    = filter_input(INPUT_POST, 'destino_id', FILTER_VALIDATE_INT);
    $maquinista_id = filter_input(INPUT_POST, 'maquinista_id', FILTER_VALIDATE_INT);
    $agente_id     = filter_input(INPUT_POST, 'agente_id', FILTER_VALIDATE_INT);
    $hora          = filter_input(INPUT_POST, 'hora', FILTER_SANITIZE_STRING);
    $pagantes      = filter_input(INPUT_POST, 'pagantes', FILTER_VALIDATE_INT);
    $moradores     = filter_input(INPUT_POST, 'moradores', FILTER_VALIDATE_INT);
    $gratuidade    = filter_input(INPUT_POST, 'gratuidade', FILTER_VALIDATE_INT);
    $tipo_viagem   = filter_input(INPUT_POST, 'tipo_viagem', FILTER_SANITIZE_STRING);
    $data_viagem   = filter_input(INPUT_POST, 'data_viagem', FILTER_SANITIZE_STRING);

    if (!$id || !$bonde_id || !$origem_id || !$maquinista_id || !$agente_id || !$hora || $pagantes === null || $moradores === null || $gratuidade === null || !$tipo_viagem || !$data_viagem) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos para atualizar a viagem.']);
        return;
    }

    $total_passageiros = $pagantes + $moradores + $gratuidade;
    if ($total_passageiros > 32) {
        echo json_encode(['success' => false, 'message' => 'Capacidade máxima de 32 passageiros excedida.']);
        return;
    }

    try {
        $stmt = $conn->prepare("UPDATE viagens SET bonde_id = :bonde_id, origem_id = :origem_id, destino_id = :destino_id, maquinista_id = :maquinista_id, agente_id = :agente_id, hora = :hora, pagantes = :pagantes, moradores = :moradores, gratuidade = :gratuidade, tipo_viagem = :tipo_viagem, data_viagem = :data_viagem WHERE id = :id");
        $stmt->execute([
            ':bonde_id'      => $bonde_id,
            ':origem_id'     => $origem_id,
            ':destino_id'    => $destino_id ?: null,
            ':maquinista_id' => $maquinista_id,
            ':agente_id'     => $agente_id,
            ':hora'          => $hora,
            ':pagantes'      => $pagantes,
            ':moradores'     => $moradores,
            ':gratuidade'    => $gratuidade,
            ':tipo_viagem'   => $tipo_viagem,
            ':data_viagem'   => $data_viagem,
            ':id'            => $id
        ]);
        echo json_encode(['success' => true, 'message' => 'Viagem atualizada com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar viagem: ' . $e->getMessage()]);
    }
}

// Função para excluir uma viagem
function deleteViagem($conn) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID da viagem não fornecido.']);
        return;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM viagens WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Viagem excluída com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir viagem: ' . $e->getMessage()]);
    }
}

// Função para limpar todas as transações
function clearTransactions($conn) {
    try {
        $conn->exec("TRUNCATE TABLE viagens");
        echo json_encode(['success' => true, 'message' => 'Todas as transações foram limpas.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao limpar transações: ' . $e->getMessage()]);
    }
}

// Função para buscar viagens paginadas e filtradas
function getViagens($conn) {
    $page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
    $perPage = 7;
    $offset = ($page - 1) * $perPage;
    $searchId = filter_input(INPUT_POST, 'search_id', FILTER_VALIDATE_INT);

    $sql = "
        SELECT
            v.id,
            b.modelo AS bonde,
            d1.nome AS saida,
            d2.nome AS retorno,
            m.nome AS maquinista,
            a.nome AS agente,
            DATE_FORMAT(v.hora, '%H:%i:%s') AS hora,
            v.pagantes,
            v.moradores,
            v.gratuidade AS gratuidadeCalculated,
            (v.pagantes + v.moradores + v.gratuidade) AS passageirosCalculated,
            v.tipo_viagem AS tipoViagem,
            v.data_viagem AS data
        FROM viagens v
        JOIN bondes b ON v.bonde_id = b.id
        JOIN destinos d1 ON v.origem_id = d1.id
        LEFT JOIN destinos d2 ON v.destino_id = d2.id
        JOIN maquinistas m ON v.maquinista_id = m.id
        JOIN agentes a ON v.agente_id = a.id
    ";

    $where = [];
    $params = [];

    if ($searchId) {
        $where[] = "v.id = :search_id";
        $params[':search_id'] = $searchId;
    }

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY v.id DESC LIMIT :offset, :perPage";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $viagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalSql = "SELECT COUNT(*) FROM viagens";
        if (!empty($where)) {
            $totalSql .= " WHERE " . implode(" AND ", $where);
        }
        $totalStmt = $conn->prepare($totalSql);
        foreach ($params as $key => &$val) {
            $totalStmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $totalStmt->execute();
        $totalViagens = $totalStmt->fetchColumn();
        $totalPages = ceil($totalViagens / $perPage);

        echo json_encode(['success' => true, 'viagens' => $viagens, 'totalPages' => $totalPages, 'currentPage' => $page]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar viagens: ' . $e->getMessage()]);
    }
}

// Função para buscar totais consolidados
function getTotals($conn) {
    $totais = [
        'subida' => ['pagantes' => 0, 'gratuitos' => 0, 'moradores' => 0, 'passageiros' => 0, 'bondes' => 0],
        'retorno' => ['pagantes' => 0, 'gratuitos' => 0, 'moradores' => 0, 'passageiros' => 0, 'bondes' => 0]
    ];

    try {
        $stmt = $conn->query("SELECT pagantes, moradores, gratuidade, tipo_viagem FROM viagens");
        while ($v = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $key = $v['tipo_viagem'] === 'subida' ? 'subida' : 'retorno';
            $totais[$key]['pagantes'] += $v['pagantes'];
            $totais[$key]['gratuitos'] += $v['gratuidade'];
            $totais[$key]['moradores'] += $v['moradores'];
            $totais[$key]['passageiros'] += $v['pagantes'] + $v['moradores'] + $v['gratuidade'];
            $totais[$key]['bondes']++;
        }
        echo json_encode(['success' => true, 'totals' => $totais]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar totais: ' . $e->getMessage()]);
    }
}
?>

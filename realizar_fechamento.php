<?php
// // Inicia a sessão
// session_start();

// // Verifica se o usuário está logado
// if (!isset($_SESSION['username'])) {
//     echo json_encode(['success' => false, 'message' => 'Usuário não está logado.']);
//     exit;
// }

// // Obtém o username da sessão
// $username = $_SESSION['username'];

// // Configuração do banco de dados
// $servername = "localhost";
// $user = "root";
// $password = "";
// $dbname = "gm_sicbd";

// // Criando a conexão com o banco de dados
// $conn = new mysqli($servername, $user, $password, $dbname);

// // Verificando se a conexão foi bem-sucedida
// if ($conn->connect_error) {
//     echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . $conn->connect_error]);
//     exit;
// }

// // Verificando se já houve fechamento neste mês
// $currentMonth = date('Y-m'); // Obtém o ano e o mês atual no formato YYYY-MM
// $sqlCheck = "SELECT * FROM fechamentos WHERE DATE_FORMAT(data_fechamento, '%Y-%m') = ?";
// $stmt = $conn->prepare($sqlCheck);
// $stmt->bind_param("s", $currentMonth);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows > 0) {
//     // Se já houver um fechamento para este mês, exibe a mensagem
//     echo json_encode(['success' => false, 'message' => 'O fechamento do mês ' . date('F Y') . ' já foi realizado. Contate o administrador.']);
//     exit;
// }

// // 1. Transferir dados da tabela fechamento para fechamentos
// $sql = "SELECT natureza, saldo_anterior, total_entrada, total_saida, saldo_atual FROM fechamento";
// $result = $conn->query($sql);

// if ($result->num_rows > 0) {
//     $fechamentoRealizado = false;
//     $naturezaComDivergencia = "";

//     while ($row = $result->fetch_assoc()) {
//         // Verifica se algum saldo é negativo
//         if ($row['saldo_atual'] < 0) {
//             $naturezaComDivergencia = $row['natureza'];
//             break; // Sai do loop assim que encontrar uma natureza com saldo negativo
//         }

//         // Inserir os dados na tabela fechamentos
//         $natureza = $row['natureza'];
//         $saldoAnterior = $row['saldo_anterior'];
//         $totalEntrada = $row['total_entrada'];
//         $totalSaida = $row['total_saida'];
//         $saldoAtual = $row['saldo_atual'];

//         // Preparando a consulta para inserir os dados na tabela fechamentos
//         $insertSql = "INSERT INTO fechamentos (username, natureza, saldo_anterior, total_entrada, total_saida, saldo_atual, data_fechamento) 
//                       VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
//         // Verificando se a consulta foi preparada corretamente
//         if ($stmt = $conn->prepare($insertSql)) {
//             // Ligando os parâmetros à consulta preparada
//             $stmt->bind_param("sssddd", $username, $natureza, $saldoAnterior, $totalEntrada, $totalSaida, $saldoAtual);

//             // Executando a consulta
//             if ($stmt->execute()) {
//                 // Sucesso na inserção
//                 $fechamentoRealizado = true;
//             } else {
//                 echo json_encode(['success' => false, 'message' => 'Erro ao inserir dados na tabela fechamentos: ' . $stmt->error]);
//                 exit;
//             }
//         } else {
//             echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta: ' . $conn->error]);
//             exit;
//         }
//     }

//     if ($fechamentoRealizado) {
//         // 2. Atualizar a tabela fechamento, transferindo saldo_atual para saldo_anterior e apagando os outros campos
//         $updateSql = "UPDATE fechamento SET saldo_anterior = saldo_atual, total_entrada = 0, total_saida = 0, saldo_atual = 0";
//         if ($conn->query($updateSql) === TRUE) {
            
//             // 3. Limpar a tabela transicao
//             $clearTransicaoSql = "DELETE FROM transicao";
//             if ($conn->query($clearTransicaoSql) === TRUE) {
//                 echo json_encode(['success' => true, 'message' => 'Fechamento realizado com sucesso e tabela transicao limpa!']);
//             } else {
//                 echo json_encode(['success' => false, 'message' => 'Erro ao limpar a tabela transicao: ' . $conn->error]);
//             }

//         } else {
//             echo json_encode(['success' => false, 'message' => 'Erro ao atualizar tabela fechamento: ' . $conn->error]);
//         }
//     } else {
//         // Caso haja saldo negativo
//         echo json_encode(['success' => false, 'message' => 'Não foi possível realizar o fechamento. A natureza "' . $naturezaComDivergencia . '" encontra-se com divergência devido a saldo negativo.']);
//     }

// } else {
//     echo json_encode(['success' => false, 'message' => 'Nenhum dado encontrado para o fechamento.']);
// }

// // Fechando a conexão com o banco de dados
// $conn->close();
?>
<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado.']);
    exit;
}

// Obtém o username da sessão
$username = $_SESSION['username'];

// Configuração do banco de dados
$servername = "localhost";
$user = "root";
$password = "";
$dbname = "gm_sicbd";

// Criando a conexão com o banco de dados
$conn = new mysqli($servername, $user, $password, $dbname);

// Verificando se a conexão foi bem-sucedida
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . $conn->connect_error]);
    exit;
}

// Configura o fuso horário para São Paulo
date_default_timezone_set('America/Sao_Paulo');

// 1. Transferir dados da tabela fechamento para fechamentos
$sql = "SELECT natureza, saldo_anterior, total_entrada, total_saida, saldo_atual, MONTH(data_fechamento) AS mes, YEAR(data_fechamento) AS ano 
        FROM fechamento 
        GROUP BY ano, mes"; // Agrupar por ano e mês
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $fechamentoRealizado = false;
    $naturezaComDivergencia = "";

    while ($row = $result->fetch_assoc()) {
        // Verifica se algum saldo é negativo
        if ($row['saldo_atual'] < 0) {
            $naturezaComDivergencia = $row['natureza'];
            break; // Sai do loop assim que encontrar uma natureza com saldo negativo
        }

        // Verifica se já existe um fechamento para esse mês
        $mes = $row['mes'];
        $ano = $row['ano'];
        $mesAno = $ano . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT); // Formato YYYY-MM

        $checkFechamentoSql = "SELECT 1 FROM fechamentos WHERE MONTH(data_fechamento) = ? AND YEAR(data_fechamento) = ? LIMIT 1";
        $stmtCheck = $conn->prepare($checkFechamentoSql);
        $stmtCheck->bind_param("ii", $mes, $ano);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            // Se já houver um fechamento para esse mês, não insere novamente
            continue;
        }

        // Somando o saldo_atual para o mês
        $sumSaldoSql = "SELECT SUM(saldo_atual) AS total_saldo FROM fechamento WHERE MONTH(data_fechamento) = ? AND YEAR(data_fechamento) = ?";
        $stmtSumSaldo = $conn->prepare($sumSaldoSql);
        $stmtSumSaldo->bind_param("ii", $mes, $ano);
        $stmtSumSaldo->execute();
        $resultSumSaldo = $stmtSumSaldo->get_result();
        $totalSaldo = 0;

        if ($rowSaldo = $resultSumSaldo->fetch_assoc()) {
            $totalSaldo = $rowSaldo['total_saldo'];
        }

        // Inserir os dados na tabela fechamentos
        $natureza = $row['natureza'];
        $saldoAnterior = $row['saldo_anterior'];
        $totalEntrada = $row['total_entrada'];
        $totalSaida = $row['total_saida'];

        // Obter a data e hora atual no fuso horário de São Paulo
        $dataFechamento = date("Y-m-d H:i:s"); // Formato YYYY-MM-DD HH:MM:SS

        // Preparando a consulta para inserir os dados na tabela fechamentos
        $insertSql = "INSERT INTO fechamentos (username, natureza, saldo_anterior, total_entrada, total_saida, saldo_atual, data_fechamento) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Verificando se a consulta foi preparada corretamente
        if ($stmt = $conn->prepare($insertSql)) {
            // Ligando os parâmetros à consulta preparada
            $stmt->bind_param("sssddds", $username, $natureza, $saldoAnterior, $totalEntrada, $totalSaida, $totalSaldo, $dataFechamento);

            // Executando a consulta
            if ($stmt->execute()) {
                // Sucesso na inserção
                $fechamentoRealizado = true;
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao inserir dados na tabela fechamentos: ' . $stmt->error]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta: ' . $conn->error]);
            exit;
        }
    }

    if ($fechamentoRealizado) {
        // 2. Atualizar a tabela fechamento, transferindo saldo_atual para saldo_anterior e apagando os outros campos
        $updateSql = "UPDATE fechamento SET saldo_anterior = saldo_atual, total_entrada = 0, total_saida = 0, saldo_atual = 0";
        if ($conn->query($updateSql) === TRUE) {
            
            // 3. Limpar a tabela transicao
            $clearTransicaoSql = "DELETE FROM transicao";
            if ($conn->query($clearTransicaoSql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Fechamento realizado com sucesso e tabela transicao limpa!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao limpar a tabela transicao: ' . $conn->error]);
            }

        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar tabela fechamento: ' . $conn->error]);
        }
    } else {
        // Caso haja saldo negativo
        echo json_encode(['success' => false, 'message' => 'Não foi possível realizar o fechamento. A natureza "' . $naturezaComDivergencia . '" encontra-se com divergência devido a saldo negativo.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Nenhum dado encontrado para o fechamento.']);
}

// Fechando a conexão com o banco de dados
$conn->close();
?>

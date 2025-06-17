<?php

// Iniciar a sessão
session_start();

// Configurações do banco de dados
$host = 'localhost';
$user = 'root'; // Substitua pelo seu usuário do banco de dados
$password = ''; // Substitua pela sua senha
$dbname = 'gm_sicbd'; // Substitua pelo nome do seu banco de dados

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados do formulário
    $produto = $_POST['produto'];  
    $classificacao = $_POST['classificacao'];
    $natureza = $_POST['natureza'];
    $contabil = $_POST['contabil'];
    $descricao = $_POST['descricao']; 
    $unidade = $_POST['unidade'];
    $localizacao = $_POST['localizacao'];
    $custo = $_POST['custo'];  
    $preco_medio = $_POST['preco_medio'];
    $quantidade = $_POST['quantidade']; 
    $nf = $_POST['nf'];

    // Garantir que o valor de custo e preço médio sejam numéricos e com precisão
    $custo = str_replace('.', '', $custo);  
    $custo = str_replace(',', '.', $custo); // Substitui a vírgula por ponto decimal
    $preco_medio = str_replace('.', '', $preco_medio); 
    $preco_medio = str_replace(',', '.', $preco_medio); 

    // Garantir que o valor seja numérico (tipo float)
    $custo = (float) $custo; 
    $preco_medio = (float) $preco_medio;
    $quantidade = (float) $quantidade; 

    // Verifique se o valor de custo, preco_medio e quantidade são numéricos válidos
    if (!is_numeric($custo) || !is_numeric($preco_medio) || !is_numeric($quantidade)) {
        echo "Erro: O valor de custo, preço médio ou quantidade não é válido!";
        exit;
    }

    // Calculando o novo total de entrada
    $novo_total_entrada = $quantidade * $preco_medio;

    // Consulta para verificar se o produto já existe
    $sql_verifica = "SELECT id, quantidade, custo, preco_medio FROM produtos WHERE produto = ?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    if (!$stmt_verifica) {
        echo "Erro na preparação da consulta: " . $conn->error;
        exit;
    }

    $stmt_verifica->bind_param("s", $produto);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        // Produto já existe, atualiza a quantidade, custo e preço médio
        $stmt_verifica->bind_result($id_existente, $quantidade_existente, $custo_existente, $preco_medio_existente);
        $stmt_verifica->fetch();

        // Calcula o novo preço médio ponderado
        $total_valor_existente = $quantidade_existente * $preco_medio_existente;
        $total_valor_novo = $quantidade * $preco_medio;
        $nova_quantidade = $quantidade_existente + $quantidade;
        $novo_preco_medio = ($total_valor_existente + $total_valor_novo) / $nova_quantidade;

        // Atualiza a tabela produtos
        $sql_atualiza = "UPDATE produtos SET quantidade = ?, custo = ?, preco_medio = ?, descricao = ? WHERE id = ?";
        $stmt_atualiza = $conn->prepare($sql_atualiza);

        if ($stmt_atualiza) {
           $stmt_atualiza->bind_param("dddsi", $nova_quantidade, $custo, $novo_preco_medio, $descricao, $id_existente);


            if ($stmt_atualiza->execute()) {
                echo "Produto já existe. Quantidade, custo, preço médio e descrição atualizados.";

                // Registrar transação de entrada
                $query_transacao = "INSERT INTO transicao (material_id, quantidade, data, tipo) VALUES (?, ?, ?, 'entrada')";
                $transacaoStmt = $conn->prepare($query_transacao);
                $transacaoStmt->bind_param('iis', $id_existente, $quantidade, date('Y-m-d H:i:s'));
                if (!$transacaoStmt->execute()) {
                    echo "Erro ao registrar a transação: " . $transacaoStmt->error;
                    exit;
                }
                $transacaoStmt->close();
            } else {
                echo "Erro ao atualizar a quantidade: " . $stmt_atualiza->error;
            }

            $stmt_atualiza->close();
        } else {
            echo "Erro na preparação da consulta de atualização: " . $conn->error;
        }
    } else {
        // Produto não existe, insere no banco de dados
        $sql_insere = "INSERT INTO produtos (produto, classificacao, natureza, contabil, descricao, unidade, localizacao, custo, quantidade, preco_medio, nf, tipo_operacao, data_cadastro) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'cadastrado', NOW())";
        $stmt_insere = $conn->prepare($sql_insere);

        if ($stmt_insere) {
            $stmt_insere->bind_param(
                "sssssssddds",
                $produto,
                $classificacao,
                $natureza,
                $contabil,
                $descricao, 
                $unidade,
                $localizacao,
                $custo, 
                $quantidade,
                $preco_medio,
                $nf
            );

            if ($stmt_insere->execute()) {
                $produto_id = $conn->insert_id;

                // Registrar transação de entrada
                $query_transacao = "INSERT INTO transicao (material_id, quantidade, data, tipo) VALUES (?, ?, ?, 'entrada')";
                $transacaoStmt = $conn->prepare($query_transacao);
                $transacaoStmt->bind_param('iis', $produto_id, $quantidade, date('Y-m-d H:i:s')); 
                if (!$transacaoStmt->execute()) {
                    echo "Erro ao registrar a transação: " . $transacaoStmt->error;
                    exit;
                }
                $transacaoStmt->close();
            } else {
                echo "Erro ao cadastrar o produto: " . $stmt_insere->error;
            }

            $stmt_insere->close();
        } else {
            echo "Erro na preparação da consulta de inserção: " . $conn->error;
        }
    }

    // Atualiza o fechamento
    $sql_fe = "SELECT saldo_atual, total_entrada FROM fechamento WHERE natureza = ?";
    $stmt_fe = $conn->prepare($sql_fe);
    $stmt_fe->bind_param("s", $natureza);
    $stmt_fe->execute();
    $stmt_fe->store_result();

    if ($stmt_fe->num_rows > 0) {
        // Já existe um fechamento para essa natureza, atualiza os valores
        $stmt_fe->bind_result($saldo_atual, $total_entrada_existente);
        $stmt_fe->fetch();

        // Calcula o novo total de entrada e saldo atual
        $novo_total_entrada = $total_entrada_existente + $novo_total_entrada;
        $novo_saldo_atual = $saldo_atual + $novo_total_entrada;

        // Atualiza o fechamento
        $sql_update_fe = "UPDATE fechamento SET total_entrada = ?, saldo_atual = ? WHERE natureza = ?";
        $stmt_update_fe = $conn->prepare($sql_update_fe);
        $stmt_update_fe->bind_param("dds", $novo_total_entrada, $novo_saldo_atual, $natureza);
        if ($stmt_update_fe->execute()) {
            echo "Fechamento atualizado com sucesso!";
        } else {
            echo "Erro ao atualizar fechamento: " . $stmt_update_fe->error;
        }
        $stmt_update_fe->close();
    } else {
        // Não existe um fechamento para essa natureza, insere um novo registro
        $total_entrada = $novo_total_entrada;
        $sql_insert_fe = "INSERT INTO fechamento (natureza, total_entrada, saldo_atual, data_fechamento) VALUES (?, ?, ?, NOW())";
        $stmt_insert_fe = $conn->prepare($sql_insert_fe);
        $stmt_insert_fe->bind_param("sdd", $natureza, $total_entrada, $total_entrada);

        if ($stmt_insert_fe->execute()) {
            echo "Novo fechamento inserido com sucesso!";
        } else {
            echo "Erro ao inserir fechamento: " . $stmt_insert_fe->error;
        }

        $stmt_insert_fe->close();
    }

    // Registro no log_eventos
    $sql_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())";
    $stmt_log = $conn->prepare($sql_log);

    if ($stmt_log) {
        $tipo_operacao = "cadastrou ou atualizou o produto no estoque";
        $stmt_log->bind_param("ss", $username, $tipo_operacao);

        if ($stmt_log->execute()) {
            // Redireciona para a página 'mensagem.php' em views, com os parâmetros necessários
            header('Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=sucesso2&pagina=/Sistema-CENTRAL-ERP/homeestoque.php');
            exit();
        } else {
            echo "Erro ao registrar ação no log: " . $stmt_log->error;
        }

        $stmt_log->close();
    } else {
        echo "Erro na preparação da consulta do log: " . $conn->error;
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
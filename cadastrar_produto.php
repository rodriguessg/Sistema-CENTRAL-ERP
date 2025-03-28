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
    $produto = $_POST['produto'];  // Produto selecionado no campo select
    $classificacao = $_POST['classificacao'];
    $natureza = $_POST['natureza'];
    $contabil = $_POST['contabil'];
    $descricao = $_POST['descricao']; // Descrição do produto (campo preenchido automaticamente)
    $unidade = $_POST['unidade'];
    $localizacao = $_POST['localizacao'];
    $custo = $_POST['custo'];
    $quantidade = $_POST['quantidade'];
    $preco_medio = $_POST['preco_medio'];
    $nf = $_POST['nf'];
    
    // Registro da transação de entrada
    $data = date('Y-m-d H:i:s');
    
    // Consulta para verificar se o produto já existe
    $sql_verifica = "SELECT id, quantidade FROM produtos WHERE produto = ?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    if (!$stmt_verifica) {
        echo "Erro na preparação da consulta: " . $conn->error;
        exit;
    }

    $stmt_verifica->bind_param("s", $produto);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        // Produto já existe, atualiza a quantidade
        $stmt_verifica->bind_result($id_existente, $quantidade_existente);
        $stmt_verifica->fetch();

        $nova_quantidade = $quantidade_existente + $quantidade;
        $sql_atualiza = "UPDATE produtos SET quantidade = ?, descricao = ? WHERE id = ?";
        $stmt_atualiza = $conn->prepare($sql_atualiza);

        if ($stmt_atualiza) {
            $stmt_atualiza->bind_param("dsi", $nova_quantidade, $descricao, $id_existente);

            if ($stmt_atualiza->execute()) {
                echo "Produto já existe. Quantidade e descrição atualizadas.";

                // Registrar transação de entrada
                $query_transacao = "INSERT INTO transicao (material_id, quantidade, data, tipo) VALUES (?, ?, ?, 'entrada')";
                $transacaoStmt = $conn->prepare($query_transacao);
                $transacaoStmt->bind_param('iis', $id_existente, $quantidade, $data); // Usando o ID do produto e a quantidade
                if (!$transacaoStmt->execute()) {
                    echo "Erro ao registrar a transação: " . $transacaoStmt->error;
                    exit;
                }
                $transacaoStmt->close();

                // Registro no log_eventos
                $sql_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())";
                $stmt_log = $conn->prepare($sql_log);

                if ($stmt_log) {
                    $tipo_operacao = "atualizou o produto";
                    $stmt_log->bind_param("ss", $username, $tipo_operacao);

                    if ($stmt_log->execute()) {
                        echo " Ação registrada no log com sucesso.";
                    } else {
                        echo "Erro ao registrar ação no log: " . $stmt_log->error;
                    }

                    $stmt_log->close();
                } else {
                    echo "Erro na preparação da consulta do log: " . $conn->error;
                }
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
                $descricao,  // Descrição do produto
                $unidade,
                $localizacao,
                $custo,
                $quantidade,
                $preco_medio,
                $nf
            );

            if ($stmt_insere->execute()) {
                // Pega o ID do produto inserido
                $produto_id = $conn->insert_id;

                // Registrar transação de entrada
                $query_transacao = "INSERT INTO transicao (material_id, quantidade, data, tipo) VALUES (?, ?, ?, 'entrada')";
                $transacaoStmt = $conn->prepare($query_transacao);
                $transacaoStmt->bind_param('iis', $produto_id, $quantidade, $data); // Usando o ID do produto e a quantidade
                if (!$transacaoStmt->execute()) {
                    echo "Erro ao registrar a transação: " . $transacaoStmt->error;
                    exit;
                }
                $transacaoStmt->close();

                // Registro no log_eventos
                $sql_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())";
                $stmt_log = $conn->prepare($sql_log);

                if ($stmt_log) {
                    $tipo_operacao = "cadastrou o produto no estoque";
                    $stmt_log->bind_param("ss", $username, $tipo_operacao);

                    if ($stmt_log->execute()) {
                        // Redirecionar para a página de sucesso
                        header('Location: mensagem.php?mensagem=sucesso2&pagina=homeestoque.php');
                        exit();
                    } else {
                        echo "Erro ao registrar ação no log: " . $stmt_log->error;
                    }

                    $stmt_log->close();
                } else {
                    echo "Erro na preparação da consulta do log: " . $conn->error;
                }
            } else {
                echo "Erro ao cadastrar o produto: " . $stmt_insere->error;
            }

            $stmt_insere->close();
        } else {
            echo "Erro na preparação da consulta de inserção: " . $conn->error;
        }
    }

    $stmt_verifica->close();
}

// Fecha a conexão com o banco de dados
$conn->close();

?>

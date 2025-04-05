<?php
// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

// Criando a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Função para calcular o tempo estimado para acabar o estoque
function calcularTempoEstimado($material_id, $quantidade_estoque, $conn) {
    // Consultar a quantidade diária média de vendas
    $sql_media = "SELECT SUM(quantidade) / DATEDIFF(CURDATE(), MIN(data)) AS quantidade_diaria_media
                  FROM transicao
                  WHERE tipo = 'saída' AND material_id = ?";
    $stmt = $conn->prepare($sql_media);
    $stmt->bind_param("i", $material_id);
    $stmt->execute();
    $stmt->bind_result($quantidade_diaria_media);
    $stmt->fetch();
    $stmt->close();

    // Calcular tempo estimado se a quantidade diária de vendas for maior que 0
    if ($quantidade_diaria_media > 0) {
        return $quantidade_estoque / $quantidade_diaria_media;
    } else {
        return null; // Se não houver saída registrada
    }
}

// Consultar o produto com maior quantidade de saídas
$sql_produto = "SELECT material_id, SUM(quantidade) AS total_saida
                FROM transicao
                WHERE tipo = 'saída'
                GROUP BY material_id
                ORDER BY total_saida DESC
                LIMIT 1";
$result = $conn->query($sql_produto);

$response = ["success" => false, "produto_mais_saida" => null, "tempo_estimado" => null];

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $material_id = $row['material_id'];
    $total_saida = $row['total_saida'];

    // Consultar a descrição do produto com base no material_id
    $sql_descricao = "SELECT descricao FROM produtos WHERE id = ?";
    $stmt_descricao = $conn->prepare($sql_descricao);
    $stmt_descricao->bind_param("i", $material_id);
    $stmt_descricao->execute();
    $stmt_descricao->bind_result($descricao_produto);
    $stmt_descricao->fetch();
    $stmt_descricao->close();

    // Consultar a quantidade em estoque do produto
    $sql_estoque = "SELECT quantidade FROM produtos WHERE id = ?";
    $stmt_estoque = $conn->prepare($sql_estoque);
    $stmt_estoque->bind_param("i", $material_id);
    $stmt_estoque->execute();
    $stmt_estoque->bind_result($quantidade_estoque);
    $stmt_estoque->fetch();
    $stmt_estoque->close();

    // Calcular tempo estimado de esgotamento do estoque
    $tempo_estimado = calcularTempoEstimado($material_id, $quantidade_estoque, $conn);

    // Calcular a projeção de vendas para 1 ano (365 dias)
    $sql_media_diaria = "SELECT SUM(quantidade) / DATEDIFF(CURDATE(), MIN(data)) AS quantidade_diaria_media
                         FROM transicao
                         WHERE tipo = 'saída' AND material_id = ?";
    $stmt_media = $conn->prepare($sql_media_diaria);
    $stmt_media->bind_param("i", $material_id);
    $stmt_media->execute();
    $stmt_media->bind_result($quantidade_diaria_media);
    $stmt_media->fetch();
    $stmt_media->close();

    // Projeção de vendas para 1 ano (365 dias)
    if ($quantidade_diaria_media > 0) {
        $projecao_ano = $quantidade_diaria_media * 365;
    } else {
        $projecao_ano = 0; // Se não houver vendas diárias, a projeção será 0
    }

    // Adicionar informações ao retorno
    $response["success"] = true;
    $response["produto_mais_saida"] = [
        "material_id" => $material_id,
        "descricao" => $descricao_produto, // Descrição do produto
        "total_saida" => $total_saida,
        "tempo_estimado" => $tempo_estimado,
        "projecao_ano" => $projecao_ano, // Projeção de vendas para 1 ano
        "tempo_estimado_dias" => ceil($tempo_estimado) // Projeção de tempo até o estoque acabar
    ];
}

$conn->close();

// Retornar a resposta em JSON
echo json_encode($response);
?>

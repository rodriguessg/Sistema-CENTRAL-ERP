<?php
// Incluir o arquivo de conexão com o banco de dados
include 'banco.php';

// Verificar se a categoria foi passada via GET
if (isset($_GET['categoria'])) {
    $categoria = $_GET['categoria'];

    // Definir o código base para cada categoria
    $codigoBase = 0;
    switch ($categoria) {
        case 'equipamentos_informatica':
            $codigoBase = 600428000012477;
            break;
        case 'bens_achados':
            $codigoBase = 705100000000196;
            break;
        case 'moveis_utensilios':
            $codigoBase = 450518000002335;
            break;
        case 'reserva_bens_moveis':
            $codigoBase = 460000000000000;
            break;
        case 'bens_com_baixo_valor':
                $codigoBase = 000000000000001;
                break;
        default:
            echo " ";
            exit;
    }

    // Buscar o último código gerado para a categoria
    $query_codigo = "SELECT MAX(codigo) AS ultimo_codigo FROM patrimonio WHERE categoria = ?";
    $stmt = $con->prepare($query_codigo);
    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Se houver um código anterior, incrementar, caso contrário, usar o código base
    $novoCodigo = $row && $row['ultimo_codigo'] ? $row['ultimo_codigo'] + 1 : $codigoBase;

    // Retornar o novo código
    echo $novoCodigo;
}
?>

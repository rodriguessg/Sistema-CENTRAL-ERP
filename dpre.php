<?php
include 'banco.php'; // Inclua sua conexão com o banco de dados
$query = "SELECT codigo, nome, data_aquisicao, valor FROM patrimonio";
$result = $con->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') . "' 
                  data-nome='" . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . "' 
                  data-data_aquisicao='" . htmlspecialchars($row['data_aquisicao'], ENT_QUOTES, 'UTF-8') . "' 
                  data-valor='" . htmlspecialchars($row['valor'], ENT_QUOTES, 'UTF-8') . "'>" 
             . htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') . "</option>";
    }
}
?>

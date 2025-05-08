<?php
// Configuração da conexão com o banco de dados
$host = 'localhost';  // Endereço do servidor do banco de dados
$dbname = 'gm_sicbd';  // Nome do banco de dados
$username = 'root';  // Nome de usuário do banco de dados
$password = '';  // Senha do banco de dados

try {
    // Conectando ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consultando contratos encerrados
    $sql = "SELECT * FROM gestao_contratos WHERE situacao = 'encerrado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Verificando se há contratos
    $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($contratos) > 0) {
        echo "<h3>Contratos Encerrados</h3>";
        echo "<table border='1'>";
        echo "<thead>
                <tr>
                    <th>Nome do Contrato</th>
                    <th>Valor Inicial</th>
                    <th>Situação</th>
                    <th>Ações</th>
                </tr>
              </thead>";
        echo "<tbody>";
        
        foreach ($contratos as $contrato) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($contrato['titulo']) . "</td>";
            // echo "<td>" . htmlspecialchars($contrato['valor_inicial']) . "</td>";
            echo "<td>" . htmlspecialchars($contrato['valor_contrato']) . "</td>";
            echo "<td>" . htmlspecialchars($contrato['situacao']) . "</td>";
            echo "<td><button onclick='iniciarPrestacao(" . $contrato['id'] . ")'>Iniciar Prestação de Contas</button></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "Nenhum contrato encontrado com a situação 'encerrado'.";
    }
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>

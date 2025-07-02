<?php
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        // Excluir viagem pelo ID
        $id = $_POST['id_filter'] ?? '';
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM viagens WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: homebonde.php?deleted=1');
            exit;
        }
    } elseif ($action === 'edit') {
        // Alterar viagem pelo ID
        $id = $_POST['id_filter'] ?? '';
        if ($id) {
            $sql = "UPDATE viagens SET 
                modelo_bonde=?, saida=?, retorno=?, maquinista=?, agente=?, hora=?, pagantes=?, moradores=?, grat_pcd_idoso=?, gratuidade=?, passageiros=?, viagem=?, data=?
                WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['modelo_bonde'], $_POST['saida'], $_POST['retorno'], $_POST['maquinista'], $_POST['agente'],
                $_POST['hora'], $_POST['pagantes'], $_POST['moradores'], $_POST['grat_pcd_idoso'],
                $_POST['gratuidade'], $_POST['passageiros'], $_POST['viagem'], $_POST['data'], $id
            ]);
            header('Location: homebonde.php?edited=1');
            exit;
        }
    } elseif ($action === 'clear_all') {
        // Limpar todas as viagens
        $pdo->exec("DELETE FROM viagens");
        header('Location: homebonde.php?cleared=1');
        exit;
    } elseif ($action === 'filter') {
        // Filtrar viagem pelo ID e redirecionar com parâmetro GET
        $id = $_POST['id_filter'] ?? '';
        if ($id) {
            header("Location: homebonde.php?filter_id=$id");
            exit;
        }
    } else {
        // Adicionar nova viagem (já implementado em add_viagem.php)
        header('Location: add_viagem.php');
        exit;
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>

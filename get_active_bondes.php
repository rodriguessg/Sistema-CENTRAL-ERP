<?php
     header('Content-Type: application/json');

     try {
         $host = 'localhost';
         $dbname = 'gm_sicbd';
         $username = 'root';
         $password = '';

         $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

         $stmt = $pdo->query("SELECT id, modelo, capacidade, ativo, ano_fabricacao, descricao FROM bondes ORDER BY modelo ASC");
         $bondes = $stmt->fetchAll(PDO::FETCH_ASSOC);

         echo json_encode($bondes);
     } catch (PDOException $e) {
         http_response_code(500);
         echo json_encode(['error' => 'Erro ao carregar bondes: ' . $e->getMessage()]);
     }
     exit();
     ?>
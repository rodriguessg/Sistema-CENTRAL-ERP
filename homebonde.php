<?php
session_start();
include 'header.php';

// Definir fuso horário de São Paulo (BRT, UTC-3)
date_default_timezone_set('America/Sao_Paulo');

// Database configuration
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar apenas o último acidente registrado
    $stmt_check = $pdo->query("
        SELECT paralizar_sistema, status 
        FROM acidentes 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $ultimo = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($ultimo && $ultimo['paralizar_sistema'] === 'Sim' && strtolower($ultimo['status']) === 'em andamento') {
        // Bloquear acesso à página
        echo "<div style='text-align: center; padding: 20px; background-color: #f9f9f9; border-radius: 5px; border: 1px solid #ddd; margin: 20px auto; max-width: 600px;'>";
        echo "<h2>Operação Indisponível</h2>";
        echo "<p>Não é possível realizar novas operações devido a uma ocorrência em andamento. Por favor, resolva a ocorrência pendente em <a href='reportacidentes.php'>Registrar Ocorrências</a>.</p>";
        echo "</div>";
        exit();
    }

    // Buscar todos os bondes da tabela
    $stmt = $pdo->query("SELECT id, modelo, capacidade, ativo, ano_fabricacao, descricao FROM bondes ORDER BY modelo ASC");
    $bondes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $bondes = [
        ['id' => 1, 'modelo' => 'BONDE 17', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2010, 'descricao' => 'Bonde padrão'],
        ['id' => 2, 'modelo' => 'BONDE 16', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2009, 'descricao' => 'Bonde clássico'],
        ['id' => 3, 'modelo' => 'BONDE 19', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2011, 'descricao' => 'Bonde renovado'],
        ['id' => 4, 'modelo' => 'BONDE 22', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2013, 'descricao' => 'Bonde moderno'],
        ['id' => 5, 'modelo' => 'BONDE 18', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2010, 'descricao' => 'Bonde intermediário'],
        ['id' => 6, 'modelo' => 'BONDE 20', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2012, 'descricao' => 'Bonde atualizado']
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Viagens - Bondes Santa Teresa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== IMPORTAÇÕES E FONTES ===== */
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

        /* ===== VARIÁVEIS CSS ===== */
        :root {
          /* Cores Principais */
          --primary-color: #192844;
          --secondary-color: #472774;
          --accent-color: #667eea;
          --success-color: #10b981;
          --warning-color: #f59e0b;
          --danger-color: #ef4444;
          --info-color: #3b82f6;

          /* Gradientes */
          --primary-gradient: linear-gradient(135deg, #192844 0%, #472774 100%);
          --secondary-gradient: linear-gradient(135deg, #472774 0%, #6a4c93 100%);
          --accent-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
          --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
          --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
          --glass-gradient: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);

          /* Cores de Texto */
          --text-primary: #1f2937;
          --text-secondary: #6b7280;
          --text-muted: #9ca3af;
          --text-light: #d1d5db;

          /* Cores de Fundo */
          --bg-primary: #ffffff;
          --bg-secondary: #f8fafc;
          --bg-tertiary: #f1f5f9;
          --bg-dark: #0f172a;

          /* Bordas e Sombras */
          --border-color: #e5e7eb;
          --border-light: #f3f4f6;
          --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
          --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
          --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
          --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);

          /* Raios de Borda */
          --radius-sm: 6px;
          --radius-md: 8px;
          --radius-lg: 12px;
          --radius-xl: 16px;

          /* Transições */
          --transition-fast: all 0.15s ease;
          --transition-normal: all 0.3s ease;
          --transition-slow: all 0.5s ease;
        }

        /* ===== RESET E BASE ===== */
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }

        body {
          font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
          line-height: 1.6;
          color: var(--text-primary);
          background: var(--bg-secondary);
          font-size: 14px;
          overflow-x: hidden;
        }

        /* ===== CONTAINERS PRINCIPAIS - COMPACTO ===== */
        .main-content {
          padding: 0.75rem;
          max-width: 1400px;
          margin: 0 auto;
        }

        .form-container {
          background: var(--bg-primary);
          border-radius: var(--radius-lg);
          box-shadow: var(--shadow-lg);
          border: 1px solid var(--border-color);
          overflow: hidden;
          transition: var(--transition-normal);
          flex: 2;
        }

        .form-container:hover {
          box-shadow: var(--shadow-xl);
        }

        /* ===== CABEÇALHOS DE SEÇÃO - COMPACTO ===== */
        .section-header {
          background: var(--glass-gradient);
          backdrop-filter: blur(10px);
          padding: 1rem 0.75rem;
          border-bottom: 1px solid var(--border-color);
          display: flex;
          align-items: center;
          gap: 0.75rem;
          position: relative;
        }

        .section-header::before {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 3px;
          background: var(--primary-gradient);
        }

        .header-icon {
          width: 40px;
          height: 40px;
          border-radius: var(--radius-md);
          background: var(--primary-gradient);
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-size: 1.125rem;
          box-shadow: var(--shadow-md);
          flex-shrink: 0;
        }

        .header-content h1,
        .header-content h2,
        .header-content h3 {
          margin: 0;
          font-size: 1.25rem;
          font-weight: 700;
          color: var(--text-primary);
          line-height: 1.2;
        }

        .header-content p {
          margin: 0.125rem 0 0 0;
          font-size: 0.8rem;
          color: var(--text-secondary);
          line-height: 1.4;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .header-section {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            margin: 0.5rem 0;
            overflow: hidden;
            transition: var(--transition-normal);
        }

        .header-section:hover {
            box-shadow: var(--shadow-xl);
        }

        .header-section .section-header {
            background: var(--glass-gradient);
            backdrop-filter: blur(10px);
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
        }

        .header-section .section-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
        }

        /* ===== FORMULÁRIOS - COMPACTO ===== */
        .form-grid {
          padding: 1.25rem;
        }

        .form-row {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
          gap: 1rem;
          margin-bottom: 1rem;
        }

        .input-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 0 1.25rem;
        }

        .input-item {
          display: flex;
          flex-direction: column;
          gap: 0.375rem;
          margin-bottom: 1rem;
        }

        .input-item label {
          display: flex;
          align-items: center;
          gap: 0.375rem;
          font-size: 0.8rem;
          font-weight: 600;
          color: var(--text-primary);
          margin-bottom: 0.125rem;
        }

        .input-item label i {
          width: 14px;
          height: 14px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: var(--accent-color);
          font-size: 0.8rem;
        }

        .input-wrapper {
          position: relative;
          display: flex;
          align-items: center;
        }

        .input-item input,
        .input-item select,
        .input-item textarea {
          width: 100%;
          padding: 0.625rem 0.75rem;
          border: 2px solid var(--border-color);
          border-radius: var(--radius-md);
          font-size: 0.8rem;
          color: var(--text-primary);
          background: var(--bg-primary);
          transition: var(--transition-normal);
          font-family: inherit;
          line-height: 1.4;
        }

        .input-item input:focus,
        .input-item select:focus,
        .input-item textarea:focus {
          outline: none;
          border-color: var(--accent-color);
          box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .input-item input:read-only,
        .input-item select:disabled {
          background: var(--bg-tertiary);
          color: var(--text-muted);
          cursor: not-allowed;
        }

        .input-item input::placeholder,
        .input-item textarea::placeholder {
          color: var(--text-muted);
        }

        .input-item textarea {
          resize: vertical;
          min-height: 80px;
        }

        /* ===== BOTÕES - COMPACTO ===== */
        .form-actions,
        .buttons-section {
          display: flex;
          gap: 0.75rem;
          justify-content: flex-start;
          align-items: center;
          padding: 1rem 1.25rem;
          background: var(--bg-tertiary);
          border-top: 1px solid var(--border-color);
          flex-wrap: wrap;
        }

        button[type="submit"],
        .btn-primary,
        .btn-secondary,
        .btn-success,
        .btn-export,
        .btn-filter,
        .btn-clear,
        .buttons-section button {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 0.375rem;
          padding: 0.625rem 1rem;
          font-size: 0.8rem;
          font-weight: 600;
          border-radius: var(--radius-md);
          cursor: pointer;
          transition: var(--transition-normal);
          border: none;
          font-family: inherit;
          text-decoration: none;
          position: relative;
          overflow: hidden;
          min-height: 36px;
        }

        button[type="submit"],
        .btn-primary,
        .buttons-section button:not(.btn-secondary) {
          background: var(--primary-gradient);
          color: white;
          box-shadow: var(--shadow-md);
        }

        button[type="submit"]:hover,
        .btn-primary:hover,
        .buttons-section button:not(.btn-secondary):hover {
          box-shadow: var(--shadow-lg);
          transform: translateY(-2px);
        }

        .btn-secondary {
          background: var(--bg-primary);
          color: var(--text-primary);
          border: 2px solid var(--border-color);
          box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
          background: var(--bg-tertiary);
          border-color: var(--accent-color);
        }

        .buttons-section button:disabled {
            background: var(--bg-tertiary);
            color: var(--text-muted);
            cursor: not-allowed;
            transform: none;
            box-shadow: var(--shadow-sm);
        }

        /* ===== TABELAS - COMPACTO ===== */
        .table-section {
          background: var(--bg-primary);
          border-radius: var(--radius-lg);
          border: 1px solid var(--border-color);
          overflow: hidden;
          box-shadow: var(--shadow-lg);
          margin: 0.75rem 0;
        }

        .table-header {
          background: var(--glass-gradient);
          padding: 1rem 1.25rem;
          border-bottom: 1px solid var(--border-color);
          display: flex;
          align-items: center;
          justify-content: space-between;
          flex-wrap: wrap;
          gap: 0.75rem;
        }

        .table-header h3 {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          margin: 0;
          font-size: 1rem;
          font-weight: 600;
          color: var(--text-primary);
        }

        .table-header h3 i {
          color: var(--accent-color);
        }

        .table-info {
          display: flex;
          align-items: center;
          gap: 0.375rem;
        }

        .record-count {
          display: flex;
          align-items: center;
          gap: 0.375rem;
          padding: 0.375rem 0.75rem;
          background: var(--bg-tertiary);
          border-radius: var(--radius-sm);
          font-size: 0.8rem;
          color: var(--text-secondary);
          border: 1px solid var(--border-light);
        }

        .record-count i {
          color: var(--info-color);
        }

        .table-container {
          overflow-x: auto;
          max-height: 600px;
          overflow-y: auto;
        }

        .data-table,
        table {
          width: 100%;
          border-collapse: separate;
          border-spacing: 0;
          background: var(--bg-primary);
          font-size: 0.8rem;
        }

        .data-table thead,
        table thead {
          position: sticky;
          top: 0;
          z-index: 10;
          background: var(--primary-gradient);
        }

        .data-table th,
        table th {
          color: white;
          padding: 0.75rem 0.5rem;
          text-align: left;
          font-weight: 600;
          font-size: 0.8rem;
          border: none;
          white-space: nowrap;
        }

        .data-table th:first-child,
        table th:first-child {
          border-radius: var(--radius-sm) 0 0 0;
        }

        .data-table th:last-child,
        table th:last-child {
          border-radius: 0 var(--radius-sm) 0 0;
        }

        .data-table th i,
        table th i {
          margin-right: 0.375rem;
          opacity: 0.9;
        }

        .data-table td,
        table td {
          padding: 0.75rem 0.5rem;
          border-bottom: 1px solid var(--border-light);
          color: var(--text-primary);
          vertical-align: middle;
        }

        .data-table tr:hover,
        table tr:hover {
          background: rgba(102, 126, 234, 0.05);
          transition: var(--transition-fast);
        }

        .data-table tr:nth-child(even),
        table tr:nth-child(even) {
          background: rgba(248, 250, 252, 0.5);
        }

        .data-table tr:nth-child(even):hover,
        table tr:nth-child(even):hover {
          background: rgba(102, 126, 234, 0.05);
        }

        /* ===== MODAL DE DESCRIÇÃO ===== */
        .modal-overlay,
        .modal {
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0, 0, 0, 0.6);
          backdrop-filter: blur(8px);
          z-index: 1000;
          display: flex;
          align-items: center;
          justify-content: center;
          opacity: 0;
          visibility: hidden;
          transition: var(--transition-normal);
          padding: 1rem;
        }

        .modal {
            display: none;
        }

        .modal.active,
        .modal-overlay.active {
          opacity: 1;
          visibility: visible;
          display: flex;
        }

        .modal-content {
          background: var(--bg-primary);
          border-radius: var(--radius-xl);
          box-shadow: var(--shadow-xl);
          border: 1px solid var(--border-color);
          max-width: 700px;
          width: 100%;
          max-height: 85vh;
          overflow: hidden;
          transform: scale(0.9) translateY(20px);
          transition: var(--transition-normal);
          position: relative;
        }

        .modal.active .modal-content,
        .modal-overlay.active .modal-content {
          transform: scale(1) translateY(0);
        }

        .modal-header {
          background: var(--primary-gradient);
          color: white;
          padding: 1.5rem;
          display: flex;
          align-items: center;
          justify-content: space-between;
          position: relative;
          overflow: hidden;
        }

        .modal-header::before {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: var(--glass-gradient);
          backdrop-filter: blur(10px);
        }

        .modal-header-content {
          position: relative;
          z-index: 2;
          display: flex;
          align-items: center;
          gap: 1rem;
        }

        .modal-icon {
          width: 48px;
          height: 48px;
          border-radius: var(--radius-lg);
          background: rgba(255, 255, 255, 0.2);
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1.25rem;
          backdrop-filter: blur(10px);
          border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .modal-title {
          font-size: 1.25rem;
          font-weight: 700;
          margin: 0;
        }

        .modal-subtitle {
          font-size: 0.875rem;
          opacity: 0.9;
          margin: 0.25rem 0 0 0;
        }

        .modal-close,
        .close-modal {
          position: relative;
          z-index: 2;
          background: rgba(255, 255, 255, 0.2);
          border: 1px solid rgba(255, 255, 255, 0.3);
          color: white;
          width: 40px;
          height: 40px;
          border-radius: var(--radius-md);
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          transition: var(--transition-normal);
          backdrop-filter: blur(10px);
        }

        .modal-close:hover,
        .close-modal:hover {
          background: rgba(255, 255, 255, 0.3);
          transform: scale(1.1);
        }

        .modal-body {
          padding: 2rem;
          max-height: 500px;
          overflow-y: auto;
        }

        .modal-form {
            padding: 1.5rem;
        }

        .modal-form .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
            margin-bottom: 1rem;
            padding: 0;
        }

        .modal-form .input-group label {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.125rem;
        }

        .modal-form .input-group input {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.8rem;
            color: var(--text-primary);
            background: var(--bg-primary);
            transition: var(--transition-normal);
            font-family: inherit;
            line-height: 1.4;
        }

        .modal-form .input-group input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .modal-actions {
          display: flex;
          gap: 0.75rem;
          justify-content: flex-end;
          padding: 1rem 1.5rem;
          background: var(--bg-tertiary);
          border-top: 1px solid var(--border-color);
        }

        .modal-actions button {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 0.375rem;
          padding: 0.625rem 1rem;
          font-size: 0.8rem;
          font-weight: 600;
          border-radius: var(--radius-md);
          cursor: pointer;
          transition: var(--transition-normal);
          border: none;
          font-family: inherit;
          min-height: 36px;
        }

        .modal-actions button[type="submit"] {
          background: var(--success-gradient);
          color: white;
          box-shadow: var(--shadow-md);
        }

        .modal-actions button[type="submit"]:hover {
          box-shadow: var(--shadow-lg);
          transform: translateY(-2px);
        }

        .modal-actions button[type="button"] {
          background: var(--bg-primary);
          color: var(--text-primary);
          border: 2px solid var(--border-color);
          box-shadow: var(--shadow-sm);
        }

        .modal-actions button[type="button"]:hover {
          background: var(--bg-tertiary);
          border-color: var(--accent-color);
        }

        /* ===== PAGINAÇÃO - COMPACTO ===== */
        .pagination {
          display: flex;
          justify-content: center;
          align-items: center;
          gap: 0.375rem;
          padding: 0.75rem;
          background: var(--bg-tertiary);
          border-top: 1px solid var(--border-color);
        }

        .pagination button {
          display: flex;
          align-items: center;
          justify-content: center;
          min-width: 32px;
          height: 32px;
          padding: 0 0.5rem;
          background: var(--bg-primary);
          color: var(--text-primary);
          border: 2px solid var(--border-color);
          border-radius: var(--radius-md);
          cursor: pointer;
          transition: var(--transition-normal);
          font-weight: 600;
          font-size: 0.8rem;
        }

        .pagination button:hover {
          background: var(--primary-gradient);
          color: white;
          border-color: transparent;
        }

        .pagination button.active {
          background: var(--primary-gradient);
          color: white;
          border-color: transparent;
        }

        .pagination button:disabled {
          opacity: 0.5;
          cursor: not-allowed;
        }

        /* ===== SEÇÃO DE BONDES ===== */
        .bondes-container {
            flex: 1;
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            margin: 0.5rem 0;
            overflow: hidden;
            transition: var(--transition-normal);
            max-height: fit-content;
        }

        .bondes-container:hover {
            box-shadow: var(--shadow-xl);
        }

        .bondes-header {
            background: var(--glass-gradient);
            backdrop-filter: blur(10px);
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .bondes-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
        }

        .bondes-header h3 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .bondes-header h3 i {
            color: var(--accent-color);
        }

        .bondes-content {
            padding: 1.25rem;
        }

        .bondes-content p {
            margin: 0 0 1rem 0;
            font-size: 0.8rem;
            color: var(--text-secondary);
            line-height: 1.4;
            font-style: italic;
        }

        .bonde-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            margin-bottom: 0.75rem;
            transition: var(--transition-normal);
        }

        .bonde-item:hover {
            border-color: var(--accent-color);
            box-shadow: var(--shadow-sm);
        }

        .bonde-item:last-child {
            margin-bottom: 0;
        }

        .bonde-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent-color);
            cursor: pointer;
        }

        .bonde-item label {
            cursor: pointer;
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.8rem;
            flex: 1;
        }

        /* ===== PROGRESS BAR ===== */
        .progress-container {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        .progress-bar {
            width: 100%;
            height: 24px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            overflow: hidden;
            position: relative;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--success-gradient);
            text-align: center;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            line-height: 24px;
            transition: width 0.3s ease;
            border-radius: var(--radius-md);
            position: relative;
        }

        /* ===== COUNTS SECTION ===== */
        .counts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
            padding: 0 1.25rem;
        }

        .total-box {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            box-shadow: var(--shadow-md);
            transition: var(--transition-normal);
        }

        .total-box:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .total-box .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .total-box .section-title i {
            color: var(--accent-color);
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-light);
            font-size: 0.8rem;
        }

        .total-item:last-child {
            border-bottom: none;
            font-weight: 600;
            color: var(--text-primary);
        }

        .total-item span:first-child {
            color: var(--text-secondary);
        }

        .total-item span:last-child {
            font-weight: 600;
            color: var(--text-primary);
        }

        /* ===== ID INPUT CONTAINER ===== */
        .id-input-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: auto;
        }

        .id-input-container label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .id-input-container input {
            padding: 0.5rem 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.8rem;
            color: var(--text-primary);
            background: var(--bg-primary);
            transition: var(--transition-normal);
            width: 120px;
        }

        .id-input-container input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 1024px) {
          .container {
            flex-direction: column;
            gap: 1rem;
          }

          .form-row,
          .input-group {
            grid-template-columns: 1fr;
          }

          .table-header {
            flex-direction: column;
            align-items: flex-start;
          }

          .counts-section {
            grid-template-columns: 1fr;
          }
        }

        @media (max-width: 768px) {
          .main-content,
          .container
         {
            padding: 0.5rem;
          }

          .section-header,
          .bondes-header {
            padding: 0.75rem;
            flex-direction: column;
            text-align: center;
          }

          .header-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
          }

          .header-content h1,
          .header-content h2,
          .header-content h3 {
            font-size: 1.125rem;
          }

          .form-grid,
          .bondes-content {
            padding: 1rem;
          }

          .input-group {
            padding: 0 1rem;
          }

          .table-header {
            padding: 0.75rem;
          }

          .table-container {
            font-size: 0.7rem;
          }

          .data-table th,
          .data-table td,
          table th,
          table td {
            padding: 0.5rem 0.25rem;
          }

          .pagination {
            flex-wrap: wrap;
          }

          .modal-content {
            margin: 0.5rem;
            max-height: 90vh;
          }

          .modal-header {
            padding: 1rem;
          }

          .modal-body,
          .modal-form {
            padding: 1rem;
          }

          .buttons-section {
            flex-direction: column;
            align-items: stretch;
          }

          .buttons-section button {
            width: 100%;
          }

          .id-input-container {
            margin-left: 0;
            justify-content: center;
          }
        }

        @media (max-width: 480px) {
          .form-container,
          .bondes-container {
            margin: 0.5rem 0;
            border-radius: var(--radius-md);
          }

          .section-header,
          .bondes-header {
            padding: 0.625rem;
          }

          .header-icon {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
          }

          .header-content h1,
          .header-content h2,
          .header-content h3 {
            font-size: 1rem;
          }

          .form-grid,
          .bondes-content {
            padding: 0.75rem;
          }

          .input-group {
            padding: 0 0.75rem;
          }

          .input-item input,
          .input-item select,
          .input-item textarea {
            padding: 0.5rem;
            font-size: 0.75rem;
          }

          button[type="submit"],
          .btn-primary,
          .buttons-section button {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            min-height: 32px;
          }

          .modal-overlay,
          .modal {
            padding: 0.5rem;
          }
        }

        /* ===== UTILITÁRIOS ===== */
        .hidden {
          display: none !important;
        }

        .visible {
          display: block !important;
        }

        .text-center {
          text-align: center;
        }

        .font-bold {
          font-weight: 700;
        }

        .font-semibold {
          font-weight: 600;
        }

        /* ===== REGRAS DE CORES PARA TRANSAÇÕES ===== */
        .transaction-row {
            transition: var(--transition-normal);
        }

        .transaction-row.ida {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%) !important;
            border-left: 4px solid var(--success-color) !important;
        }

        .transaction-row.ida:hover {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.08) 100%) !important;
        }

        .transaction-row.retorno {
            background: linear-gradient(90deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%) !important;
            border-left: 4px solid var(--danger-color) !important;
        }

        .transaction-row.retorno:hover {
            background: linear-gradient(90deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.08) 100%) !important;
        }

        .transaction-row.retorno-pendente {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%) !important;
            border-left: 4px solid var(--warning-color) !important;
        }

        .transaction-row.retorno-pendente:hover {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0.08) 100%) !important;
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.ida {
            background: var(--success-gradient);
            color: white;
        }

        .status-badge.chegada {
            background: var(--danger-gradient);
            color: white;
        }

        .status-badge.retorno-pendente {
            background: var(--warning-gradient);
            color: white;
        }

        /* Ícones de status */
        .status-icon {
            width: 16px;
            height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 0.6rem;
        }

        .status-icon.ida {
            background: var(--success-color);
            color: white;
        }

        .status-icon.chegada {
            background: var(--danger-color);
            color: white;
        }

        .status-icon.retorno-pendente {
            background: var(--warning-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="caderno">
        <div class="form-container" id="controle-viagem">
            <div class="header-section">
                <div class="section-header">
                    <div class="header-icon">
                        <i class="fas fa-train"></i>
                    </div>
                    <div class="header-content">
                        <h3>CADASTRAMENTO DE TRANSAÇÕES</h3>
                        <p>Sistema de controle de viagens dos bondes de Santa Teresa</p>
                    </div>
                </div>
            </div>
            
            <form id="viagem-form" method="POST" action="add_viagem.php">
                <div class="input-group">
                    <div class="input-item">
                        <label for="bonde"><i class="fas fa-train"></i> BONDE</label>
                        <select id="bonde" name="bonde" required>
                            <option value="">Selecione</option>
                            <?php
                            $activeBondes = false;
                            foreach ($bondes as $bonde) {
                                if ($bonde['ativo'] == 1) {
                                    echo "<option value=\"{$bonde['modelo']}\">{$bonde['modelo']}</option>";
                                    $activeBondes = true;
                                }
                            }
                            if (!$activeBondes) {
                                echo "<option value=\"\" disabled>Nenhum bonde ativo</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="saida"><i class="fas fa-arrow-up"></i> PARTIDA</label>
                        <select id="saida" name="saida" required>
                            <option value="Carioca">Carioca</option>
                            <option value="D.Irmãos">D.Irmãos</option>
                            <option value="Paula Mattos">Paula Mattos</option>
                            <option value="Silvestre">Silvestre</option>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="retorno"><i class="fas fa-arrow-down"></i> CHEGADA</label>
                        <select id="retorno" name="retorno">
                            <option value="">Selecione (para chegada)</option>
                            <option value="Carioca">Carioca</option>
                            <option value="D.Irmãos">D.Irmãos</option>
                            <option value="Paula Mattos">Paula Mattos</option>
                            <option value="Silvestre">Silvestre</option>
                            <option value="Oficina">Oficina</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-group">
                    <div class="input-item">
                        <label for="maquinistas"><i class="fas fa-user-tie"></i> MAQUINISTAS</label>
                        <select id="maquinistas" name="maquinistas" required>
                            <option value="">Selecione</option>
                            <option value="Sergio Lima">Sergio Lima</option>
                            <option value="Adriano">Adriano</option>
                            <option value="Helio">Helio</option>
                            <option value="M. Celestino">M. Celestino</option>
                            <option value="Leonardo">Leonardo</option>
                            <option value="Andre">Andre</option>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="agentes"><i class="fas fa-user"></i> AGENTES</label>
                        <select id="agentes" name="agentes" required>
                            <option value="">Selecione</option>
                            <option value="Samir">Samir</option>
                            <option value="Vinicius">Vinicius</option>
                            <option value="P. Nascimento">P. Nascimento</option>
                            <option value="Oliveira">Oliveira</option>
                            <option value="Carlos">Carlos</option>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="hora"><i class="fas fa-clock"></i> HORA</label>
                        <input type="text" id="hora" name="hora" value="00:00:00" readonly>
                    </div>
                </div>
                
                <div class="input-group">
                    <div class="input-item">
                        <label for="pagantes"><i class="fas fa-dollar-sign"></i> PAGANTES</label>
                        <input type="number" id="pagantes" name="pagantes" value="0" min="0" required>
                    </div>
                    <div class="input-item">
                        <label for="moradores"><i class="fas fa-home"></i> MORADORES (gratuidade) </label>
                        <input type="number" id="moradores" name="moradores" value="0" min="0" required>
                    </div>
                    <div class="input-item">
                        <label for="grat_pcd_idoso"><i class="fas fa-wheelchair"></i> GRAT. PCD/IDOSO</label>
                        <input type="number" id="grat_pcd_idoso" name="grat_pcd_idoso" value="0" min="0" required>
                    </div>
                </div>
                
                <div class="input-group">
                    <div class="input-item">
                        <label for="gratuidade"><i class="fas fa-gift"></i> GRATUIDADE</label>
                        <input type="number" id="gratuidade" name="gratuidade" value="0" readonly>
                    </div>
                    <div class="input-item">
                        <label for="passageiros"><i class="fas fa-users"></i> PASSAGEIROS</label>
                        <input type="number" id="passageiros" name="passageiros" value="0" readonly>
                    </div>
                  <div class="input-item">
                        <label for="viagem"><i class="fas fa-route"></i> VIAGEM</label>
                        <input type="number" id="viagem" name="viagem" value="1" min="1" required readonly>
                    </div>
                </div>
                
                <div class="input-group">
                    <div class="input-item">
                        <label for="data"><i class="fas fa-calendar"></i> DATA</label>
                        <input type="date" id="data" name="data" required>
                    </div>
                    <div class="input-item progress-container">
                        <label><i class="fas fa-chart-bar"></i> CAPACIDADE DO BONDE (Máx. 32 Passageiros)</label>
                        <div class="progress-bar">
                            <div class="progress-bar-fill" id="progress-bar-fill">0%</div>
                        </div>
                    </div>
                </div>
                
                <div class="buttons-section">
                    <button type="submit" id="add-btn"><i class="fas fa-plus"></i> Adicionar</button>
                    <button type="button" id="clear-form-btn" class="btn-secondary"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="button" id="delete-btn" disabled><i class="fas fa-trash"></i> Excluir</button>
                    <button type="button" id="alter-btn" disabled><i class="fas fa-edit"></i> Alterar</button>
                    <button type="button" id="return-btn" style="display: none;"><i class="fas fa-undo"></i> Registrar Retorno</button>
                    <button type="button" id="clear-transactions-btn"><i class="fas fa-broom"></i> Limpar Transações</button>
                    <button type="button" id="add-bonde-btn"><i class="fas fa-plus-circle"></i> Adicionar Bonde</button>
                    <button type="button" id="add-staff-btn"><i class="fas fa-user-plus"></i> Adicionar Maquinistas ou Agentes</button>
                     <button type="button" id="manage-bondes-btn" class="btn-primary">
                    <i class="fas fa-cog"></i> Gerenciar
                </button>
                    <div class="id-input-container">
                        <label for="id-filter">ID:</label>
                        <input type="text" id="id-filter" placeholder="Filtrar por ID">
                    </div>
                </div>
            </form>
            
            <div class="counts-section">
                <div class="total-box">
                    <div class="section-title"><i class="fas fa-arrow-up"></i> TOTAL DE PARTIDA </div>
                    <div class="total-item"><span>Pagantes</span><span id="total-subindo-pagantes">0</span></div>
                    <div class="total-item"><span>Total de Gratuidade</span><span id="total-subindo-gratuitos">0</span></div>
                    <div class="total-item"><span>Moradores</span><span id="total-subindo-moradores">0</span></div>
                     <div class="total-item"><span>Grat./Pcd/Idoso</span><span id="total-subindo-grat_pcd_idoso">0</span></div>
                    <div class="total-item"><span>Passageiros</span><span id="total-subindo-passageiros">0</span></div>
                    <div class="total-item"><span>Bondes Partida</span><span id="total-bondes-saida">0</span></div>
                </div>
                <div class="total-box">
                    <div class="section-title"><i class="fas fa-arrow-down"></i> TOTAL DE CHEGADA</div>
                    <div class="total-item"><span>Pagantes</span><span id="total-retorno-pagantes">0</span></div>
                    <div class="total-item"><span>Total de Gratuidade</span><span id="total-retorno-gratuitos">0</span></div>
                    <div class="total-item"><span>Moradores</span><span id="total-retorno-moradores">0</span></div>
                     <div class="total-item"><span>Grat./Pcd/Idoso</span><span id="total-retorno-grat_pcd_idoso">0</span></div>
                    <div class="total-item"><span>Passageiros</span><span id="total-retorno-passageiros">0</span></div>
                    <div class="total-item"><span>Bondes Chegada</span><span id="total-bondes-retorno">0</span></div>
                </div>
            </div>
            
            <div class="table-section">
                <div class="table-header">
                    <h3><i class="fas fa-table"></i> Registro de Viagens</h3>
                    <div class="record-count">
                        <i class="fas fa-info-circle"></i>
                        <span id="record-count">0 registros</span>
                    </div>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID-M</th>
                                <th><i class="fas fa-train"></i> Bondes</th>
                                <th><i class="fas fa-arrow-up"></i> Partida</th>
                                <th><i class="fas fa-arrow-down"></i> Chegada</th>
                                <th><i class="fas fa-user-tie"></i> Maquinista</th>
                                <th><i class="fas fa-user"></i> Agente</th>
                                <th><i class="fas fa-clock"></i> Hora</th>
                                <th><i class="fas fa-dollar-sign"></i> Pagantes</th>
                            
                                <th><i class="fas fa-gift"></i> Grat. PCD/Idoso</th>
                                <th><i class="fas fa-home"></i> Moradores</th>
                                <th><i class="fas fa-users"></i> Passageiros</th>
                                <th><i class="fas fa-route"></i> Tipo Viagem</th>
                                <th><i class="fas fa-calendar"></i> Data</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-table-body">
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <button id="prev-page" disabled><i class="fas fa-chevron-left"></i> Anterior</button>
                    <span id="page-info">Página 1 de 1</span>
                    <button id="next-page"><i class="fas fa-chevron-right"></i> Próximo</button>
                </div>
            </div>
        </div>
        
        <div class="bondes-container" style="display:none;">
            <div class="bondes-header">
                <h3><i class="fas fa-train"></i> Bondes Ativos</h3>
               
            </div>
            <div class="bondes-content">
                <p><em>Cadastre aqui os bondes que estão operacionais na atual data: <?php date_default_timezone_set('America/Sao_Paulo'); echo date('H:i d/m/Y'); ?></em></p>
                <?php foreach ($bondes as $bonde): ?>
                    <div class="bonde-item">
                        <input type="checkbox" id="bonde_<?php echo $bonde['id']; ?>" 
                               data-id="<?php echo $bonde['id']; ?>" 
                               data-modelo="<?php echo htmlspecialchars($bonde['modelo']); ?>" 
                               <?php echo $bonde['ativo'] ? 'checked' : ''; ?> 
                               onchange="updateBondeStatus(this)">
                        <label for="bonde_<?php echo $bonde['id']; ?>">
                            <strong><?php echo htmlspecialchars($bonde['modelo']); ?></strong><br>
                            <small>Cap: <?php echo $bonde['capacidade']; ?> | Ano: <?php echo $bonde['ano_fabricacao']; ?></small>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Bonde -->
    <div id="add-bonde-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div>
                        <h3 class="modal-title">Adicionar Novo Bonde</h3>
                        <p class="modal-subtitle">Cadastre um novo bonde no sistema</p>
                    </div>
                </div>
                <span class="close-modal" onclick="closeAddBondeModal()">
                    <i class="fas fa-times"></i>
                </span>
            </div>
            <form id="add-bonde-form" method="POST" action="add_bonde.php">
                <div class="modal-form">
                    <div class="input-group">
                        <label for="modelo"><i class="fas fa-tag"></i> Modelo</label>
                        <input type="text" id="modelo" name="modelo" required placeholder="Ex: BONDE 25">
                    </div>
                    <div class="input-group">
                        <label for="capacidade"><i class="fas fa-users"></i> Capacidade</label>
                        <input type="number" id="capacidade" name="capacidade" min="1" required placeholder="32">
                    </div>
                    <div class="input-group">
                        <label for="ano_fabricacao"><i class="fas fa-calendar-alt"></i> Ano de Fabricação</label>
                        <input type="number" id="ano_fabricacao" name="ano_fabricacao" min="1900" max="<?php echo date('Y'); ?>" required placeholder="<?php echo date('Y'); ?>">
                    </div>
                    <div class="input-group">
                        <label for="descricao"><i class="fas fa-info-circle"></i> Descrição</label>
                        <input type="text" id="descricao" name="descricao" required placeholder="Descrição do bonde">
                    </div>
                    <div class="input-group">
                        <label for="ativo">
                            <input type="checkbox" id="ativo" name="ativo" value="1" style="width: auto; margin-right: 0.5rem;">
                            <i class="fas fa-check-circle"></i> Ativo
                        </label>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="submit"><i class="fas fa-save"></i> Salvar</button>
                    <button type="button" onclick="closeAddBondeModal()"><i class="fas fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Gerenciar Bondes -->
    <div id="manage-bondes-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div>
                        <h3 class="modal-title">Gerenciar Bondes Ativos</h3>
                        <p class="modal-subtitle">Marque os bondes que estão operacionais</p>
                    </div>
                </div>
                <span class="close-modal" onclick="closeManageBondesModal()">
                    <i class="fas fa-times"></i>
                </span>
            </div>
            <div class="modal-body">
                <div id="modal-bondes-list">
                    <?php foreach ($bondes as $bonde): ?>
                        <div class="bonde-item">
                            <input type="checkbox" id="modal_bonde_<?php echo $bonde['id']; ?>" 
                                   data-id="<?php echo $bonde['id']; ?>" 
                                   data-modelo="<?php echo htmlspecialchars($bonde['modelo']); ?>" 
                                   <?php echo $bonde['ativo'] ? 'checked' : ''; ?> 
                                   onchange="updateBondeStatusFromModal(this)">
                            <label for="modal_bonde_<?php echo $bonde['id']; ?>">
                                <strong><?php echo htmlspecialchars($bonde['modelo']); ?></strong><br>
                                <small>Cap: <?php echo $bonde['capacidade']; ?> | Ano: <?php echo $bonde['ano_fabricacao']; ?> | <?php echo htmlspecialchars($bonde['descricao']); ?></small>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeManageBondesModal()"><i class="fas fa-check"></i> Concluído</button>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Funcionário -->
    <div id="add-staff-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h3 class="modal-title">Adicionar Maquinistas ou Agentes</h3>
                        <p class="modal-subtitle">Cadastre um novo maquinista ou agente no sistema</p>
                    </div>
                </div>
                <span class="close-modal" onclick="closeAddStaffModal()">
                    <i class="fas fa-times"></i>
                </span>
            </div>
            <form id="add-staff-form" method="POST" action="add_staff.php">
                <div class="modal-form">
                    <div class="input-group">
                        <label for="staff-nome"><i class="fas fa-user"></i> Nome</label>
                        <input type="text" id="staff-nome" name="nome" required placeholder="Nome do funcionário">
                    </div>
                    <div class="input-group">
                        <label for="staff-tipo"><i class="fas fa-briefcase"></i> Tipo</label>
                        <select id="staff-tipo" name="tipo" required>
                            <option value="">Selecione</option>
                            <option value="maquinista">Maquinista</option>
                            <option value="agente">Agente</option>
                        </select>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="submit"><i class="fas fa-save"></i> Salvar</button>
                    <button type="button" onclick="closeAddStaffModal()"><i class="fas fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="./bonde.js"></script>
    <script>
        function updateBondeStatus(checkbox) {
            const bondeId = checkbox.getAttribute('data-id');
            const modelo = checkbox.getAttribute('data-modelo');
            const ativo = checkbox.checked ? 1 : 0;
            const url = new URL('/Sistema-CENTRAL-ERP/update_bonde_status.php', window.location.origin);

            fetch(url.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: bondeId,
                    ativo: ativo
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateSelectOptions();
                    // Sincronizar checkbox do modal se existir
                    const modalCheckbox = document.getElementById('modal_bonde_' + bondeId);
                    if (modalCheckbox) {
                        modalCheckbox.checked = checkbox.checked;
                    }
                } else {
                    alert('Erro ao atualizar status do bonde: ' + data.message);
                    checkbox.checked = !checkbox.checked;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro na conexão com o servidor.');
                checkbox.checked = !checkbox.checked;
            });
        }

        function updateBondeStatusFromModal(checkbox) {
            const bondeId = checkbox.getAttribute('data-id');
            const sidebarCheckbox = document.getElementById('bonde_' + bondeId);
            
            // Sincronizar com o checkbox da sidebar
            if (sidebarCheckbox) {
                sidebarCheckbox.checked = checkbox.checked;
                // Chamar a função original
                updateBondeStatus(sidebarCheckbox);
            }
        }

        function updateSelectOptions() {
            const select = document.getElementById('bonde');
            const originalValue = select.value;

            fetch('/Sistema-CENTRAL-ERP/get_active_bondes.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro ao carregar bondes: ' + response.status);
                return response.json();
            })
            .then(data => {
                select.innerHTML = '<option value="">Selecione</option>';

                let hasActiveBondes = false;
                data.forEach(bonde => {
                    if (bonde.ativo == 1) {
                        const option = document.createElement('option');
                        option.value = bonde.modelo;
                        option.textContent = bonde.modelo;
                        select.appendChild(option);
                        hasActiveBondes = true;
                    }
                });

                if (!hasActiveBondes) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Nenhum bonde ativo';
                    option.disabled = true;
                    select.appendChild(option);
                }

                if (originalValue && data.some(bonde => bonde.modelo === originalValue && bonde.ativo == 1)) {
                    select.value = originalValue;
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar opções do select:', error);
                alert('Erro ao carregar a lista de bondes.');
            });
        }

        // Função para calcular e atualizar a barra de progresso
        function updateProgressBar() {
            const pagantes = parseInt(document.getElementById('pagantes').value) || 0;
            const moradores = parseInt(document.getElementById('moradores').value) || 0;
            const gratPcdIdoso = parseInt(document.getElementById('grat_pcd_idoso').value) || 0;
            const totalPassageiros = pagantes + moradores + gratPcdIdoso;
            const maxCapacity = 32;
            const percentage = (totalPassageiros / maxCapacity) * 100;
            const progressFill = document.getElementById('progress-bar-fill');
            progressFill.style.width = Math.min(percentage, 100) + '%';
            progressFill.textContent = Math.min(percentage, 100).toFixed(0) + '%';

            document.getElementById('gratuidade').value = gratPcdIdoso;
            document.getElementById('passageiros').value = totalPassageiros;
        }

        // Adiciona eventos para atualização em tempo real
        ['pagantes', 'moradores', 'grat_pcd_idoso'].forEach(id => {
            document.getElementById(id).addEventListener('input', updateProgressBar);
        });

        // Inicializa a barra de progresso
        updateProgressBar();

        // Inicializa as opções do <select>
        updateSelectOptions();

        // Funções para controlar o modal de adicionar bonde
        const addBondeModal = document.getElementById('add-bonde-modal');
        const addBondeBtn = document.getElementById('add-bonde-btn');

        addBondeBtn.addEventListener('click', () => {
            addBondeModal.style.display = 'flex';
            addBondeModal.classList.add('active');
        });

        function closeAddBondeModal() {
            addBondeModal.classList.remove('active');
            setTimeout(() => {
                addBondeModal.style.display = 'none';
            }, 300);
            document.getElementById('add-bonde-form').reset();
        }

        // Funções para controlar o modal de gerenciar bondes
        const manageBondesModal = document.getElementById('manage-bondes-modal');
        const manageBondesBtn = document.getElementById('manage-bondes-btn');

        manageBondesBtn.addEventListener('click', () => {
            manageBondesModal.style.display = 'flex';
            manageBondesModal.classList.add('active');
        });

        function closeManageBondesModal() {
            manageBondesModal.classList.remove('active');
            setTimeout(() => {
                manageBondesModal.style.display = 'none';
            }, 300);
        }

        // Funções para controlar o modal de adicionar funcionário
        const addStaffModal = document.getElementById('add-staff-modal');
        const addStaffBtn = document.getElementById('add-staff-btn');

        addStaffBtn.addEventListener('click', () => {
            addStaffModal.style.display = 'flex';
            addStaffModal.classList.add('active');
        });

        function closeAddStaffModal() {
            addStaffModal.classList.remove('active');
            setTimeout(() => {
                addStaffModal.style.display = 'none';
            }, 300);
            document.getElementById('add-staff-form').reset();
        }

        // Fechar os modais ao clicar fora deles
        window.addEventListener('click', (event) => {
            if (event.target === addBondeModal) {
                closeAddBondeModal();
            }
            if (event.target === manageBondesModal) {
                closeManageBondesModal();
            }
            if (event.target === addStaffModal) {
                closeAddStaffModal();
            }
        });

        // Manipular o envio do formulário de adicionar bonde via AJAX
        document.getElementById('add-bonde-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const data = {
                modelo: formData.get('modelo'),
                capacidade: formData.get('capacidade'),
                ano_fabricacao: formData.get('ano_fabricacao'),
                descricao: formData.get('descricao'),
                ativo: formData.get('ativo') ? 1 : 0
            };

            fetch('/Sistema-CENTRAL-ERP/add_bonde.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Bonde adicionado com sucesso!');
                    closeAddBondeModal();
                    location.reload(); // Recarrega a página para atualizar a lista de bondes
                } else {
                    alert('Erro ao adicionar bonde: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro na conexão com o servidor.');
            });
        });

        // Manipular o envio do formulário de adicionar funcionário via AJAX
        document.getElementById('add-staff-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const data = {
                nome: formData.get('nome'),
                tipo: formData.get('tipo')
            };

            fetch('/Sistema-CENTRAL-ERP/add_staff.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Funcionário adicionado com sucesso!');
                    closeAddStaffModal();
                    updateStaffSelects();
                    //location.reload(); // Recarrega a página para atualizar as listas de funcionários
                } else {
                    alert('Erro ao adicionar funcionário: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro na conexão com o servidor.');
            });
        });

        // Função para atualizar as listas de maquinistas e agentes
        function updateStaffSelects() {
            const maquinistaSelect = document.getElementById('maquinistas');
            const agenteSelect = document.getElementById('agentes');

            fetch('/Sistema-CENTRAL-ERP/get_staff.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro ao carregar funcionários: ' + response.status);
                return response.json();
            })
            .then(data => {
                // Atualizar select de maquinistas
                maquinistaSelect.innerHTML = '<option value="">Selecione</option>';
                data.maquinistas.forEach(maquinista => {
                    const option = document.createElement('option');
                    option.value = maquinista.nome;
                    option.textContent = maquinista.nome;
                    maquinistaSelect.appendChild(option);
                });

                // Atualizar select de agentes
                agenteSelect.innerHTML = '<option value="">Selecione</option>';
                data.agentes.forEach(agente => {
                    const option = document.createElement('option');
                    option.value = agente.nome;
                    option.textContent = agente.nome;
                    agenteSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Erro ao atualizar listas de funcionários:', error);
                alert('Erro ao carregar a lista de funcionários.');
            });
        }

        // Definir data atual por padrão
        document.getElementById('data').valueAsDate = new Date();

        // ===== SISTEMA DE CORES E ORGANIZAÇÃO DE TRANSAÇÕES ===== 

        function applyTransactionRules() {
           // console.log('🎨 Aplicando regras de cores e organização...');
            
            const tableBody = document.getElementById('transactions-table-body');
            if (!tableBody) {
            //    console.log('❌ Tabela não encontrada');
                return;
            }

            const rows = Array.from(tableBody.querySelectorAll('tr'));
            console.log(`📊 Encontradas ${rows.length} linhas na tabela`);

            if (rows.length === 0) {
           //     console.log('⚠️ Nenhuma linha encontrada na tabela');
                return;
            }

            // Store existing event listeners before modifying DOM
            const rowEventListeners = new Map();
            rows.forEach((row, index) => {
                const clonedRow = row.cloneNode(true);
                rowEventListeners.set(index, clonedRow);
            });

            // Aplicar cores e ícones sem destruir event listeners
            rows.forEach((row, index) => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 0) return;

               // console.log(`🔍 Processando linha ${index + 1}`);

                // Identificar colunas (baseado na estrutura da tabela)
                const retornoCell = cells[3]; // Coluna "Retorno"
                const tipoViagemCell = cells[11]; // Coluna "Tipo Viagem"

                if (!tipoViagemCell) return;

                const tipoViagem = tipoViagemCell.textContent.trim().toLowerCase();
                const retornoText = retornoCell ? retornoCell.textContent.trim() : '';

                console.log(`📝 Tipo: ${tipoViagem}, Retorno: "${retornoText}"`);

                // Remover classes anteriores
                row.classList.remove('transaction-row', 'ida', 'retorno', 'retorno-pendente');

                // Aplicar regras de cores
                if (tipoViagem === 'ida') {
                    if (retornoText === '' || retornoText === 'Pendente') {
                        // IDA SEM RETORNO = AMARELO (Pendente)
                        row.classList.add('transaction-row', 'retorno-pendente');
                        updateCellWithIcon(tipoViagemCell, '<span class="status-badge retorno-pendente"><i class="fas fa-clock"></i> Pendente</span>');
                  //      console.log('🟡 Aplicado: PENDENTE');
                    } else {
                        // IDA COM RETORNO = VERDE
                        row.classList.add('transaction-row', 'ida');
                        updateCellWithIcon(tipoViagemCell, '<span class="status-badge ida"><i class="fas fa-arrow-up"></i> Partida</span>');
                //        console.log('🟢 Aplicado: PARTIDA');
                    }
                } else if (tipoViagem === 'retorno') {
                    // RETORNO = VERMELHO
                    row.classList.add('transaction-row', 'retorno');
                    updateCellWithIcon(tipoViagemCell, '<span class="status-badge chegada"><i class="fas fa-arrow-down"></i> chegada</span>');
               //     console.log('🔴 Aplicado: chegada');
                }

                // Adicionar ícones nas colunas preservando event listeners
                addIconsToRowSafely(cells);
            });

            // Organizar transações
            organizeTransactionPairs();
        }

        function updateCellWithIcon(cell, newContent) {
            if (cell.innerHTML !== newContent) {
                cell.innerHTML = newContent;
                // Add pointer-events: none to icons to prevent click interference
                const icons = cell.querySelectorAll('i');
                icons.forEach(icon => {
                    icon.style.pointerEvents = 'none';
                });
            }
        }

        function addIconsToRowSafely(cells) {
            const iconMappings = [
                { index: 1, icon: 'fas fa-train', color: 'var(--accent-color)' }, // Bonde
                { index: 2, icon: 'fas fa-map-marker-alt', color: 'var(--success-color)' }, // Saída
                { index: 3, icon: 'fas fa-map-marker-alt', color: 'var(--danger-color)' }, // Retorno
                { index: 4, icon: 'fas fa-user-tie', color: 'var(--info-color)' }, // Maquinista
                { index: 5, icon: 'fas fa-user', color: 'var(--secondary-color)' }, // Agente
                { index: 6, icon: 'fas fa-clock', color: 'var(--warning-color)' } // Hora
            ];

            iconMappings.forEach(mapping => {
                const cell = cells[mapping.index];
                if (cell && !cell.querySelector('i')) {
                    const originalText = cell.textContent.trim();
                    if (originalText && originalText !== '') {
                        // Use createElement instead of innerHTML to preserve event listeners
                        const icon = document.createElement('i');
                        icon.className = mapping.icon;
                        icon.style.color = mapping.color;
                        icon.style.marginRight = '0.5rem';
                        icon.style.pointerEvents = 'none'; // Prevent icon from interfering with clicks
                        
                        // Clear cell and add icon + text
                        cell.textContent = originalText;
                        cell.insertBefore(icon, cell.firstChild);
                    }
                }
            });
        }

        function organizeTransactionPairs() {
           // console.log('🔄 Organizando pares ida/retorno...');
            
            const tableBody = document.getElementById('transactions-table-body');
            const rows = Array.from(tableBody.querySelectorAll('tr'));
            
            // Create a map for faster lookups
            const transactionMap = new Map();
            const organizedRows = [];
            const processedIds = new Set();

            // First pass: categorize all transactions
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 0) return;

                const id = cells[0].textContent.trim();
                const tipoViagem = cells[11].textContent.toLowerCase();
                const bonde = cells[1].textContent.replace(/<[^>]*>/g, '').trim();
                const maquinista = cells[4].textContent.replace(/<[^>]*>/g, '').trim();
                const data = cells[12].textContent.trim();
                
                const key = `${bonde}-${maquinista}-${data}`;
                
                if (!transactionMap.has(key)) {
                    transactionMap.set(key, { ida: null, retorno: null });
                }
                
                const entry = transactionMap.get(key);
                if (tipoViagem.includes('ida') || tipoViagem.includes('pendente')) {
                    entry.ida = row;
                } else if (tipoViagem.includes('chegada')) {
                    entry.retorno = row;
                }
            });

            // Second pass: organize pairs
            transactionMap.forEach((entry, key) => {
                if (entry.ida && !processedIds.has(entry.ida.querySelector('td').textContent.trim())) {
                    organizedRows.push(entry.ida);
                    processedIds.add(entry.ida.querySelector('td').textContent.trim());
                    
                    if (entry.retorno && !processedIds.has(entry.retorno.querySelector('td').textContent.trim())) {
                        organizedRows.push(entry.retorno);
                        processedIds.add(entry.retorno.querySelector('td').textContent.trim());
               //         console.log(`✅ Par organizado: ${key}`);
                    }
                }
            });

            // Add orphaned returns
            transactionMap.forEach((entry, key) => {
                if (entry.retorno && !processedIds.has(entry.retorno.querySelector('td').textContent.trim())) {
                    organizedRows.push(entry.retorno);
                    processedIds.add(entry.retorno.querySelector('td').textContent.trim());
                }
            });

            // Reorganize table using DocumentFragment to preserve event listeners
            const fragment = document.createDocumentFragment();
            organizedRows.forEach(row => fragment.appendChild(row));
            
            tableBody.innerHTML = '';
            tableBody.appendChild(fragment);
            
         //   console.log(`✅ Tabela reorganizada com ${organizedRows.length} linhas`);
        }

        let updateTimeout;
        const tableObserver = new MutationObserver((mutations) => {
            let shouldUpdate = false;
            
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' && 
                    (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0)) {
                    shouldUpdate = true;
                }
            });

            if (shouldUpdate) {
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => {
              //      console.log('🔄 Mudança detectada na tabela, aplicando regras...');
                    applyTransactionRules();
                }, 300); // Debounce to prevent rapid updates
            }
        });

        // Inicializar observer
        const tableBody = document.getElementById('transactions-table-body');
        if (tableBody) {
            tableObserver.observe(tableBody, {
                childList: true,
                subtree: true
            });
           // console.log('👁️ Observer da tabela inicializado');
        }

        // Interceptar função de atualização da tabela se existir
        if (typeof window.updateTransactionsTable === 'function') {
            const originalUpdate = window.updateTransactionsTable;
            window.updateTransactionsTable = function() {
            //    console.log('🔄 Interceptando updateTransactionsTable...');
                originalUpdate.apply(this, arguments);
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(applyTransactionRules, 200);
            };
        }

        // Aplicar regras quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
       //     console.log('📄 DOM carregado, aguardando dados...');
            setTimeout(applyTransactionRules, 1000);
        });
document.addEventListener('DOMContentLoaded', function() {
    const horaInput = document.getElementById('hora');

    function atualizarHora() {
        const agora = new Date();
        const opcoes = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZone: 'America/Sao_Paulo',
            hour12: false
        };
        const horaFormatada = agora.toLocaleTimeString('pt-BR', opcoes);
        horaInput.value = horaFormatada;
    }

    setInterval(atualizarHora, 1000);
    atualizarHora(); // Atualiza imediatamente ao carregar
});
        setInterval(() => {
            const tableBody = document.getElementById('transactions-table-body');
            if (tableBody && tableBody.children.length > 0) {
                const hasUntreatedRows = Array.from(tableBody.querySelectorAll('tr')).some(row => 
                    !row.classList.contains('transaction-row')
                );
                
                if (hasUntreatedRows) {
                  //  console.log('🔄 Linhas não tratadas encontradas, aplicando regras...');
                    applyTransactionRules();
                }
            }
        }, 5000); // Increased interval to reduce interference

        // console.log('🚀 Sistema de cores e organização inicializado!');

        // Função para preparar o formulário para registrar retorno
document.getElementById('return-btn').addEventListener('click', function() {
    const form = document.getElementById('viagem-form');
    const retornoSelect = document.getElementById('retorno');
    const saidaSelect = document.getElementById('saida');

    // Definir tipo de viagem como retorno
    document.getElementById('viagem').value = '1'; // Garantir que viagem seja 1
    // Preencher retorno com base na última partida, se aplicável
    const ultimaSaida = saidaSelect.value;
    if (ultimaSaida) {
        retornoSelect.value = ultimaSaida; // Retorno para o ponto de partida
    }

});

// Mostrar o botão de retorno quando houver uma ida registrada
function checkForReturn() {
    const tableBody = document.getElementById('transactions-table-body');
    const returnBtn = document.getElementById('return-btn');
    if (tableBody && returnBtn) {
        const idasPendentes = Array.from(tableBody.querySelectorAll('tr')).some(row => {
            const tipoViagemCell = row.cells[11].textContent.toLowerCase();
            return tipoViagemCell.includes('pendente');
        });
        returnBtn.style.display = idasPendentes ? 'inline-flex' : 'none';
    }
}

// Verificar a cada 5 segundos se há idas pendentes
setInterval(checkForReturn, 5000);
checkForReturn(); // Verificar ao carregar a página
    </script>
</body>
</html>

<?php
include 'header.php';

// Configuração da conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

try {
    // Criar conexão com o banco
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query para buscar os modelos da tabela bondes
    $sql = "SELECT modelo FROM bondes ORDER BY modelo";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $bondes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query para buscar os dados da tabela viagens
    $sql = "SELECT data, bonde, saida, retorno, maquinista, agente, hora, pagantes, moradores, gratuidade AS gratPcdIdoso FROM viagens";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $viagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Bondes Santa Teresa</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <!-- Added Lucide icons for better UI -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        /* ===== IMPORTAÇÕES E FONTES ===== */
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

        /* ===== RESET E BASE ===== */
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }

        body {
          font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
          background: #f8fafc;
          color: #1f2937;
          line-height: 1.6;
          font-size: 14px;
        }

        .container {
          padding: 0.75rem;
          max-width: 1400px;
          margin: 0 auto;
        }

        /* Adicionando CSS com especificidade máxima para os botões */
        #generate-report-btn,
        button#generate-report-btn,
        html body #generate-report-btn {
          background: #192844 !important; 
          background-color: #192844 !important; 
          color: #ffffff !important; 
          border: none !important; 
          padding: 12px 24px !important; 
          border-radius: 8px !important; 
          font-weight: 600 !important; 
          font-size: 14px !important; 
          cursor: pointer !important; 
          display: inline-flex !important; 
          align-items: center !important; 
          justify-content: center !important; 
          gap: 8px !important; 
          box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important; 
          transition: all 0.3s ease !important;
          font-family: 'Inter', sans-serif !important;
          text-decoration: none !important;
          outline: none !important;
          min-height: 44px !important;
          width: auto !important;
        }

        #generate-report-btn:hover,
        button#generate-report-btn:hover,
        html body #generate-report-btn:hover {
          background: #472774 !important;
          background-color: #472774 !important;
          transform: translateY(-1px) !important;
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        #generate-report-btn span,
        #generate-report-btn i,
        button#generate-report-btn span,
        button#generate-report-btn i {
          color: #ffffff !important;
        }

        #export-pdf-btn,
        button#export-pdf-btn,
        html body #export-pdf-btn {
          background: #10b981 !important; 
          background-color: #10b981 !important; 
          color: #ffffff !important; 
          border: none !important; 
          padding: 12px 24px !important; 
          border-radius: 8px !important; 
          font-weight: 600 !important; 
          font-size: 14px !important; 
          cursor: pointer !important; 
          display: inline-flex !important; 
          align-items: center !important; 
          justify-content: center !important; 
          gap: 8px !important; 
          box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important; 
          transition: all 0.3s ease !important;
          font-family: 'Inter', sans-serif !important;
          text-decoration: none !important;
          outline: none !important;
          min-height: 44px !important;
          width: auto !important;
        }

        #export-pdf-btn:hover:not(:disabled),
        button#export-pdf-btn:hover:not(:disabled) {
          background: #059669 !important;
          background-color: #059669 !important;
          transform: translateY(-1px) !important;
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        #export-pdf-btn span,
        #export-pdf-btn i,
        button#export-pdf-btn span,
        button#export-pdf-btn i {
          color: #ffffff !important;
        }

        /* ===== SEÇÃO DE FORMULÁRIO ===== */
        .form-section {
          background: #ffffff;
          border-radius: 12px;
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
          border: 1px solid #e5e7eb;
          margin: 0.5rem 0;
          overflow: hidden;
          transition: all 0.3s ease;
        }

        .form-section:hover {
          box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .form-section h2 {
          background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
          backdrop-filter: blur(10px);
          padding: 1rem 1.25rem;
          border-bottom: 1px solid #e5e7eb;
          margin: 0;
          font-size: 1.25rem;
          font-weight: 700;
          color: #1f2937;
          position: relative;
          display: flex;
          align-items: center;
          gap: 0.5rem;
        }

        .form-section h2::before {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 3px;
          background: linear-gradient(135deg, #192844 0%, #472774 100%);
        }

        .input-group {
          padding: 1.25rem;
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 1rem;
          align-items: end;
        }

        .input-item {
          display: flex;
          flex-direction: column;
          gap: 0.5rem;
        }

        .input-item label {
          font-weight: 600;
          color: #1f2937;
          font-size: 0.875rem;
          display: flex;
          align-items: center;
          gap: 0.5rem;
        }

        .input-item input,
        .input-item select {
          padding: 0.75rem;
          border: 1px solid #e5e7eb;
          border-radius: 8px;
          font-size: 0.875rem;
          transition: all 0.15s ease;
          background: #ffffff;
          color: #1f2937;
          height: 48px;
          display: flex;
          align-items: center;
        }

        .input-item input:focus,
        .input-item select:focus {
          outline: none;
          border-color: #667eea;
          box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* ===== BOTÕES ===== */
        .buttons-section {
          padding: 0 1.25rem 1.25rem;
          display: flex;
          gap: 0.75rem;
          flex-wrap: wrap;
        }

        /* ===== SEÇÕES DE TABELA ===== */
        .table-section,
        .summary-section,
        .bonde-total-section,
        .route-total-section,
        .hourly-carioca-section {
          background: #ffffff;
          border-radius: 12px;
          border: 1px solid #e5e7eb;
          overflow: hidden;
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
          margin: 0.75rem 0;
        }

        .summary-section h3,
        .bonde-total-section h3,
        .route-total-section h3,
        .hourly-carioca-section h3 {
          background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
          padding: 1rem 1.25rem;
          border-bottom: 1px solid #e5e7eb;
          margin: 0;
          font-size: 1rem;
          font-weight: 600;
          color: #1f2937;
          position: relative;
          display: flex;
          align-items: center;
          gap: 0.5rem;
        }

        .summary-section h3::before,
        .bonde-total-section h3::before,
        .route-total-section h3::before,
        .hourly-carioca-section h3::before {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 3px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* ===== TABELAS ===== */
        table {
          width: 100%;
          border-collapse: separate;
          border-spacing: 0;
          background: #ffffff;
          font-size: 0.8rem;
        }

        table thead {
          background: linear-gradient(135deg, #192844 0%, #472774 100%);
        }

        table th {
          color: white;
          padding: 0.75rem 0.5rem;
          text-align: center;
          font-weight: 700;
          font-size: 0.8rem;
          border: none;
          white-space: nowrap;
        }

        table th:first-child {
          border-radius: 6px 0 0 0;
        }

        table th:last-child {
          border-radius: 0 6px 0 0;
        }

        table td {
          padding: 0.75rem 0.5rem;
          border-bottom: 1px solid #f3f4f6;
          color: #1f2937;
          vertical-align: middle;
          text-align: center;
          font-weight: 600;
        }

        table tr:hover {
          background: rgba(102, 126, 234, 0.05);
          transition: all 0.15s ease;
        }

        table tr:nth-child(even) {
          background: rgba(248, 250, 252, 0.5);
        }

        table tr:nth-child(even):hover {
          background: rgba(102, 126, 234, 0.05);
        }

        /* ===== RESUMO ===== */
        #summary-content {
          padding: 1.25rem;
        }

        .summary-item {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 0.75rem 0;
          border-bottom: 1px solid #f3f4f6;
          font-weight: 500;
        }

        .summary-item:last-child {
          border-bottom: none;
          font-weight: 700;
          color: #192844;
        }

        /* ===== ÍCONES ===== */
        .icon {
          width: 16px;
          height: 16px;
          stroke-width: 2;
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 768px) {
          .input-group {
            grid-template-columns: 1fr;
            padding: 1rem;
          }

          .buttons-section {
            padding: 0 1rem 1rem;
            flex-direction: column;
          }

          table {
            font-size: 0.7rem;
          }

          table th,
          table td {
            padding: 0.5rem 0.25rem;
          }
        }

        @media (max-width: 480px) {
          .container {
            padding: 0.5rem;
          }

          .input-group {
            padding: 0.75rem;
          }

          .buttons-section {
            padding: 0 0.75rem 0.75rem;
          }
        }

        /* ===== ESTADOS ESPECIAIS ===== */
        .no-data {
          text-align: center;
          padding: 2rem 1rem;
          color: #9ca3af;
          font-style: italic.
        }

        /* ===== UTILITÁRIOS ===== */
        .hidden {
          display: none !important;
        }
    </style>
</head>
<body>
    <div class="caderno">
        <div class="header-section">
            <h1 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937; line-height: 1.2; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="file-text" class="icon"></i>
                Sistema de Relatórios - Bondes Santa Teresa
            </h1>
        </div>
        
        <div class="form-section">
            <h2>
                <i data-lucide="settings" class="icon"></i>
                Gerar Relatório
            </h2>
            <div class="input-group">
                <div class="input-item">
                    <label for="user-name">
                        <i data-lucide="user" class="icon"></i>
                        Nome do Usuário
                    </label>
                    <input type="text" id="user-name" placeholder="Digite seu nome" value="">
                </div>
                <div class="input-item">
                    <label for="user-registration">
                        <i data-lucide="id-card" class="icon"></i>
                        Matrícula
                    </label>
                    <input type="text" id="user-registration" placeholder="Digite sua matrícula" value="">
                </div>
                <div class="input-item">
                    <label for="report-type">
                        <i data-lucide="calendar" class="icon"></i>
                        Tipo de Relatório
                    </label>
                    <select id="report-type">
                        <option value="diario">Diário</option>
                        <option value="semanal">Semanal</option>
                        <option value="mensal">Mensal</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>
                <div class="input-item" id="date-input-container">
                    <label for="report-date">
                        <i data-lucide="calendar-days" class="icon"></i>
                        Data
                    </label>
                    <input type="date" id="report-date" value="2025-07-02">
                </div>
                <div class="input-item" id="month-input-container" style="display: none;">
                    <label for="report-month">
                        <i data-lucide="calendar-days" class="icon"></i>
                        Mês
                    </label>
                    <select id="report-month">
                        <option value="0">Janeiro</option>
                        <option value="1">Fevereiro</option>
                        <option value="2">Março</option>
                        <option value="3">Abril</option>
                        <option value="4">Maio</option>
                        <option value="5">Junho</option>
                        <option value="6">Julho</option>
                        <option value="7">Agosto</option>
                        <option value="8">Setembro</option>
                        <option value="9">Outubro</option>
                        <option value="10">Novembro</option>
                        <option value="11">Dezembro</option>
                    </select>
                </div>
                <div class="input-item">
                    <label for="bonde">
                        <i data-lucide="train" class="icon"></i>
                        Bonde
                    </label>
                    <select id="bonde">
                        <option value="">Todos</option>
                        <?php
                        foreach ($bondes as $bonde) {
                            echo '<option value="' . htmlspecialchars($bonde['modelo']) . '">' . htmlspecialchars($bonde['modelo']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="buttons-section">
                <!-- Simplificando estrutura dos botões para garantir contraste adequado -->
                <button 
                    id="generate-report-btn"
                    type="button"
                >
                    <i data-lucide="play" class="icon"></i>
                    <span>Gerar Relatório</span>
                </button>
                <button 
                    id="export-pdf-btn" 
                    type="button"
                    disabled 
                >
                    <i data-lucide="download" class="icon"></i>
                    <span>Exportar como PDF</span>
                </button>
            </div>
        </div>
        
        <div class="table-section" id="report-table-section" style="display: none;">
            <h3>
                <i data-lucide="table" class="icon"></i>
                Dados do Relatório
            </h3>
            <table id="report-table">
                <thead id="report-table-head"></thead>
                <tbody id="report-table-body"></tbody>
            </table>
        </div>
        
        <div class="summary-section" id="summary-section" style="display: none;">
            <h3>
                <i data-lucide="bar-chart-3" class="icon"></i>
                Resumo do Relatório
            </h3>
            <div id="summary-content"></div>
        </div>
        
        <div class="bonde-total-section" id="bonde-total-section" style="display: none;">
            <h3>
                <i data-lucide="train" class="icon"></i>
                Totais por Bonde
            </h3>
            <table id="bonde-total-table">
                <thead>
                    <tr>
                        <th>Bonde</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuitos</th>
                        <th>Total Passageiros</th>
                    </tr>
                </thead>
                <tbody id="bonde-total-table-body"></tbody>
            </table>
        </div>
        
        <div class="route-total-section" id="route-total-section" style="display: none;">
            <h3>
                <i data-lucide="map-pin" class="icon"></i>
                Totais por Rota
            </h3>
            <table id="route-total-table">
                <thead>
                    <tr>
                        <th>Saída</th>
                        <th>Retorno</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuitos</th>
                        <th>Total Passageiros</th>
                    </tr>
                </thead>
                <tbody id="route-total-table-body"></tbody>
            </table>
        </div>
        
        <div class="hourly-carioca-section" id="hourly-carioca-section" style="display: none;">
            <h3>
                <i data-lucide="clock" class="icon"></i>
                Passageiros por Hora (Carioca)
            </h3>
            <table id="hourly-carioca-table">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuitos</th>
                        <th>Total Passageiros</th>
                    </tr>
                </thead>
                <tbody id="hourly-carioca-table-body"></tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            updateDateInput();
            
            const generateBtn = document.getElementById('generate-report-btn');
            const exportBtn = document.getElementById('export-pdf-btn');
            
            if (generateBtn) {
                // Removendo todos os estilos existentes e aplicando novos
                generateBtn.removeAttribute('style');
                generateBtn.style.cssText = `
                    background: #1f2937 !important;
                    color: #ffffff !important;
                    border: 2px solid #1f2937 !important;
                    padding: 12px 24px !important;
                    border-radius: 8px !important;
                    font-weight: 600 !important;
                    font-size: 14px !important;
                    cursor: pointer !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    gap: 8px !important;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                    transition: all 0.3s ease !important;
                    font-family: 'Inter', sans-serif !important;
                    text-decoration: none !important;
                    outline: none !important;
                    min-height: 44px !important;
                    width: auto !important;
                `;
                
                // Aplicando estilos aos elementos filhos
                const generateBtnSpan = generateBtn.querySelector('span');
                const generateBtnIcon = generateBtn.querySelector('i');
                if (generateBtnSpan) {
                    generateBtnSpan.style.cssText = 'color: #ffffff !important;';
                }
                if (generateBtnIcon) {
                    generateBtnIcon.style.cssText = 'color: #ffffff !important;';
                }
                
                // Evento hover
                generateBtn.addEventListener('mouseenter', function() {
                    this.style.background = '#374151 !important';
                    this.style.borderColor = '#374151 !important';
                });
                
                generateBtn.addEventListener('mouseleave', function() {
                    this.style.background = '#1f2937 !important';
                    this.style.borderColor = '#1f2937 !important';
                });
            }
            
            if (exportBtn) {
                // Removendo todos os estilos existentes e aplicando novos
                exportBtn.removeAttribute('style');
                exportBtn.style.cssText = `
                    background: #059669 !important;
                    color: #ffffff !important;
                    border: 2px solid #059669 !important;
                    padding: 12px 24px !important;
                    border-radius: 8px !important;
                    font-weight: 600 !important;
                    font-size: 14px !important;
                    cursor: pointer !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    gap: 8px !important;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                    transition: all 0.3s ease !important;
                    font-family: 'Inter', sans-serif !important;
                    text-decoration: none !important;
                    outline: none !important;
                    min-height: 44px !important;
                    width: auto !important;
                `;
                
                // Aplicando estilos aos elementos filhos
                const exportBtnSpan = exportBtn.querySelector('span');
                const exportBtnIcon = exportBtn.querySelector('i');
                if (exportBtnSpan) {
                    exportBtnSpan.style.cssText = 'color: #ffffff !important;';
                }
                if (exportBtnIcon) {
                    exportBtnIcon.style.cssText = 'color: #ffffff !important;';
                }
                
                // Evento hover (apenas quando não estiver desabilitado)
                exportBtn.addEventListener('mouseenter', function() {
                    if (!this.disabled) {
                        this.style.background = '#047857 !important';
                        this.style.borderColor = '#047857 !important';
                    }
                });
                
                exportBtn.addEventListener('mouseleave', function() {
                    if (!this.disabled) {
                        this.style.background = '#059669 !important';
                        this.style.borderColor = '#059669 !important';
                    }
                });
            }
        });

        const { jsPDF } = window.jspdf;
        const reportTypeInput = document.getElementById('report-type');
        const reportDateInput = document.getElementById('report-date');
        const dateInputContainer = document.getElementById('date-input-container');
        const monthInputContainer = document.getElementById('month-input-container');
        const reportMonthInput = document.getElementById('report-month');
        const bondeInput = document.getElementById('bonde');
        const generateReportBtn = document.getElementById('generate-report-btn');
        const exportPdfBtn = document.getElementById('export-pdf-btn');
        const reportTableSection = document.getElementById('report-table-section');
        const reportTableHead = document.getElementById('report-table-head');
        const reportTableBody = document.getElementById('report-table-body');
        const summarySection = document.getElementById('summary-section');
        const summaryContent = document.getElementById('summary-content');
        const bondeTotalSection = document.getElementById('bonde-total-section');
        const bondeTotalTableBody = document.getElementById('bonde-total-table-body');
        const routeTotalSection = document.getElementById('route-total-section');
        const routeTotalTableBody = document.getElementById('route-total-table-body');
        const hourlyCariocaSection = document.getElementById('hourly-carioca-section');
        const hourlyCariocaTableBody = document.getElementById('hourly-carioca-table-body');

        let viagens = <?php echo json_encode($viagens); ?>;
        let currentReportData = null;

        function updateDateInput() {
            const reportType = reportTypeInput.value;
            dateInputContainer.innerHTML = '';
            monthInputContainer.style.display = 'none';
            let input;
            if (reportType === 'diario') {
                input = document.createElement('input');
                input.type = 'date';
                input.id = 'report-date';
                input.value = '2025-07-02';
                input.required = true;
                dateInputContainer.innerHTML = '<label for="report-date"><i data-lucide="calendar-days" class="icon"></i>Data</label>';
                lucide.createIcons();
                dateInputContainer.appendChild(input);
            } else if (reportType === 'semanal') {
                input = document.createElement('input');
                input.type = 'week';
                input.id = 'report-date';
                input.value = '2025-W27';
                input.required = true;
                dateInputContainer.innerHTML = '<label for="report-date"><i data-lucide="calendar-days" class="icon"></i>Semana</label>';
                lucide.createIcons();
                dateInputContainer.appendChild(input);
            } else if (reportType === 'mensal' || reportType === 'anual') {
                input = document.createElement('input');
                input.type = 'number';
                input.id = 'report-date';
                input.min = '2000';
                input.max = new Date().getFullYear();
                input.value = new Date().getFullYear();
                input.required = true;
                dateInputContainer.innerHTML = '<label for="report-date"><i data-lucide="calendar-days" class="icon"></i>Ano</label>';
                lucide.createIcons();
                dateInputContainer.appendChild(input);
                monthInputContainer.style.display = reportType === 'mensal' ? 'block' : 'none';
                if (reportType === 'mensal') {
                    reportMonthInput.value = new Date().getMonth();
                }
            }
        }

        function getWeekStartEnd(weekString) {
            const [year, week] = weekString.split('-W').map(Number);
            const jan1 = new Date(year, 0, 1);
            const firstMonday = new Date(year, 0, 1 + ((jan1.getDay() === 0 ? -6 : 1) - jan1.getDay()) + (week - 1) * 7);
            const weekEnd = new Date(firstMonday);
            weekEnd.setDate(firstMonday.getDate() + 6);
            return { start: firstMonday, end: weekEnd };
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function generateReport() {
            const reportType = reportTypeInput.value;
            const dateValue = document.getElementById('report-date')?.value;
            const monthValue = reportType === 'mensal' ? reportMonthInput.value : null;
            const bonde = bondeInput.value;
            let filteredViagens = viagens;

            if (!dateValue) {
                alert('Por favor, selecione uma data, semana ou ano.');
                return;
            }
            if (reportType === 'mensal' && !monthValue) {
                alert('Por favor, selecione um mês.');
                return;
            }

            if (bonde) {
                filteredViagens = viagens.filter(t => t.bonde === bonde);
            }

            reportTableSection.style.display = 'block';
            summarySection.style.display = 'block';
            bondeTotalSection.style.display = 'block';
            routeTotalSection.style.display = 'block';
            hourlyCariocaSection.style.display = 'block';
            reportTableHead.innerHTML = '';
            reportTableBody.innerHTML = '';
            summaryContent.innerHTML = '';
            bondeTotalTableBody.innerHTML = '';
            routeTotalTableBody.innerHTML = '';
            hourlyCariocaTableBody.innerHTML = '';
            exportPdfBtn.disabled = false;
            currentReportData = null;

            const bondes = <?php echo json_encode(array_column($bondes, 'modelo')); ?>;
            const rotas = [...new Set(viagens.map(t => JSON.stringify({ saida: t.saida, retorno: t.retorno })))].map(JSON.parse);
            const horas = [...new Set(viagens.map(t => t.hora))].sort();

            // Calculate totals per bonde
            const bondeTotals = bondes.map(bonde => {
                let bondeViagens = filteredViagens.filter(t => t.bonde === bonde);
                if (reportType === 'diario') {
                    bondeViagens = bondeViagens.filter(t => t.data === dateValue);
                } else if (reportType === 'semanal') {
                    const { start, end } = getWeekStartEnd(dateValue);
                filteredViagens = bondeViagens.filter(t => {
                    const transactionDate = new Date(t.data);
                    return transactionDate >= start && transactionDate <= end;
                });
                } else if (reportType === 'mensal') {
                    const year = parseInt(dateValue);
                    const month = parseInt(monthValue);
                    bondeViagens = bondeViagens.filter(t => {
                        const transactionDate = new Date(t.data);
                        return transactionDate.getFullYear() === year && transactionDate.getMonth() === month;
                    });
                } else if (reportType === 'anual') {
                    const year = parseInt(dateValue);
                    bondeViagens = bondeViagens.filter(t => new Date(t.data).getFullYear() === year);
                }
                const totalPagantes = bondeViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0);
                const totalMoradores = bondeViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0);
                const totalGratuitos = bondeViagens.reduce((sum, t) => sum + parseInt(t.gratPcdIdoso), 0);
                const total = totalPagantes + totalMoradores + totalGratuitos;
                return { bonde, totalPagantes, totalMoradores, totalGratuitos, total };
            }).filter(row => row.total > 0);

            // Render bonde totals table
            bondeTotals.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.bonde}</td>
                    <td>${row.totalPagantes}</td>
                    <td>${row.totalMoradores}</td>
                    <td>${row.totalGratuitos}</td>
                    <td>${row.total}</td>
                `;
                bondeTotalTableBody.appendChild(tr);
            });

            // Calculate route totals
            const routeTotals = rotas.map(rota => {
                let routeViagens = filteredViagens.filter(t => t.saida === rota.saida && t.retorno === rota.retorno);
                if (reportType === 'diario') {
                    routeViagens = routeViagens.filter(t => t.data === dateValue);
                } else if (reportType === 'semanal') {
                    const { start, end } = getWeekStartEnd(dateValue);
                    routeViagens = routeViagens.filter(t => {
                        const transactionDate = new Date(t.data);
                        return transactionDate >= start && transactionDate <= end;
                    });
                } else if (reportType === 'mensal') {
                    const year = parseInt(dateValue);
                    const month = parseInt(monthValue);
                    routeViagens = routeViagens.filter(t => {
                        const transactionDate = new Date(t.data);
                        return transactionDate.getFullYear() === year && transactionDate.getMonth() === month;
                    });
                } else if (reportType === 'anual') {
                    const year = parseInt(dateValue);
                    routeViagens = routeViagens.filter(t => new Date(t.data).getFullYear() === year);
                }
                const totalPagantes = routeViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0);
                const totalMoradores = routeViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0);
                const totalGratuitos = routeViagens.reduce((sum, t) => sum + parseInt(t.gratPcdIdoso), 0);
                const total = totalPagantes + totalMoradores + totalGratuitos;
                return { saida: rota.saida, retorno: rota.retorno, totalPagantes, totalMoradores, totalGratuitos, total };
            }).filter(row => row.total > 0);

            // Render route totals table
            routeTotals.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.saida}</td>
                    <td>${row.retorno}</td>
                    <td>${row.totalPagantes}</td>
                    <td>${row.totalMoradores}</td>
                    <td>${row.totalGratuitos}</td>
                    <td>${row.total}</td>
                `;
                routeTotalTableBody.appendChild(tr);
            });

            // Calculate hourly Carioca totals grouped by hour blocks
            const hourlyGroups = {};
            
            // Group trips by hour block
            filteredViagens.filter(t => t.saida === 'Carioca' || t.retorno === 'Carioca').forEach(viagem => {
                let shouldInclude = false;
                
                if (reportType === 'diario') {
                    shouldInclude = viagem.data === dateValue;
                } else if (reportType === 'semanal') {
                    const { start, end } = getWeekStartEnd(dateValue);
                    const transactionDate = new Date(viagem.data);
                    shouldInclude = transactionDate >= start && transactionDate <= end;
                } else if (reportType === 'mensal') {
                    const year = parseInt(dateValue);
                    const month = parseInt(monthValue);
                    const transactionDate = new Date(viagem.data);
                    shouldInclude = transactionDate.getFullYear() === year && transactionDate.getMonth() === month;
                } else if (reportType === 'anual') {
                    const year = parseInt(dateValue);
                    shouldInclude = new Date(viagem.data).getFullYear() === year;
                }
                
                if (shouldInclude) {
                    const hora = viagem.hora;
                    const hourBlock = hora.split(':')[0] + ':00'; // Extract hour and format as XX:00
                    
                    if (!hourlyGroups[hourBlock]) {
                        hourlyGroups[hourBlock] = {
                            totalPagantes: 0,
                            totalMoradores: 0,
                            totalGratuitos: 0
                        };
                    }
                    
                    hourlyGroups[hourBlock].totalPagantes += parseInt(viagem.pagantes);
                    hourlyGroups[hourBlock].totalMoradores += parseInt(viagem.moradores);
                    hourlyGroups[hourBlock].totalGratuitos += parseInt(viagem.gratPcdIdoso);
                }
            });
            
            // Convert to array and sort by hour
            const hourlyCariocaTotals = Object.keys(hourlyGroups)
                .sort()
                .map(hourBlock => {
                    const group = hourlyGroups[hourBlock];
                    const total = group.totalPagantes + group.totalMoradores + group.totalGratuitos;
                    return {
                        hora: hourBlock + ' - ' + hourBlock.split(':')[0] + ':59',
                        totalPagantes: group.totalPagantes,
                        totalMoradores: group.totalMoradores,
                        totalGratuitos: group.totalGratuitos,
                        total: total
                    };
                })
                .filter(row => row.total > 0);

            // Render hourly Carioca totals table
            hourlyCariocaTotals.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.hora}</td>
                    <td>${row.totalPagantes}</td>
                    <td>${row.totalMoradores}</td>
                    <td>${row.totalGratuitos}</td>
                    <td>${row.total}</td>
                `;
                hourlyCariocaTableBody.appendChild(tr);
            });

            if (reportType === 'diario') {
                filteredViagens = filteredViagens.filter(t => t.data === dateValue);
                reportTableHead.innerHTML = `
                    <tr>
                        <th>Bonde</th>
                        <th>Saída</th>
                        <th>Retorno</th>
                        <th>Maquinista</th>
                        <th>Agente</th>
                        <th>Hora</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuitos</th>
                        <th>Total Passageiros</th>
                    </tr>
                `;
                const reportData = [];
                bondes.forEach(bonde => {
                    rotas.forEach(rota => {
                        const bondesViagens = filteredViagens.filter(t => t.bonde === bonde && t.saida === rota.saida && t.retorno === rota.retorno);
                        if (bondesViagens.length > 0) {
                            bondesViagens.forEach(viagem => {
                                const pagantes = parseInt(viagem.pagantes);
                                const moradores = parseInt(viagem.moradores);
                                const gratuitos = parseInt(viagem.gratPcdIdoso);
                                const total = pagantes + moradores + gratuitos;
                                if (total > 0) {
                                    reportData.push({ 
                                        bonde: viagem.bonde, 
                                        saida: rota.saida, 
                                        retorno: rota.retorno, 
                                        maquinista: viagem.maquinista || 'N/A',
                                        agente: viagem.agente || 'N/A',
                                        hora: viagem.hora || 'N/A',
                                        pagantes, 
                                        moradores, 
                                        gratuitos, 
                                        total 
                                    });
                                }
                            });
                        }
                    });
                });

                reportData.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.bonde}</td>
                        <td>${row.saida}</td>
                        <td>${row.retorno}</td>
                        <td>${row.maquinista}</td>
                        <td>${row.agente}</td>
                        <td>${row.hora}</td>
                        <td>${row.pagantes}</td>
                        <td>${row.moradores}</td>
                        <td>${row.gratuitos}</td>
                        <td>${row.total}</td>
                    `;
                    reportTableBody.appendChild(tr);
                });

                const totalPagantes = reportData.reduce((sum, row) => sum + row.pagantes, 0);
                const totalMoradores = reportData.reduce((sum, row) => sum + row.moradores, 0);
                const totalGratuitos = reportData.reduce((sum, row) => sum + row.gratuitos, 0);
                const totalPassageiros = totalPagantes + totalMoradores + totalGratuitos;

                summaryContent.innerHTML = `
                    <div class="summary-item"><span>Total Pagantes</span><span>${totalPagantes}</span></div>
                    <div class="summary-item"><span>Total Moradores</span><span>${totalMoradores}</span></div>
                    <div class="summary-item"><span>Total Gratuitos</span><span>${totalGratuitos}</span></div>
                    <div class="summary-item"><span>Total Passageiros</span><span>${totalPassageiros}</span></div>
                `;
                currentReportData = { type: 'diario', date: dateValue, data: reportData, bondeTotals, routeTotals, hourlyCariocaTotals, summary: { totalPagantes, totalMoradores, totalGratuitos, totalPassageiros } };

            } else if (reportType === 'semanal') {
                const { start, end } = getWeekStartEnd(dateValue);
                filteredViagens = filteredViagens.filter(t => {
                    const transactionDate = new Date(t.data);
                    return transactionDate >= start && transactionDate <= end;
                });
                reportTableHead.innerHTML = `
                    <tr>
                        <th>Data</th>
                        <th>Bonde</th>
                        <th>Saída</th>
                        <th>Retorno</th>
                        <th>Maquinista</th>
                        <th>Agente</th>
                        <th>Hora</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuitos</th>
                        <th>Total Passageiros</th>
                    </tr>
                `;
                const reportData = [];
                const dates = [];
                let currentDate = new Date(start);
                while (currentDate <= end) {
                    dates.push(formatDate(currentDate));
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                dates.forEach(date => {
                    bondes.forEach(bonde => {
                        rotas.forEach(rota => {
                            const bondesViagens = filteredViagens.filter(t => t.data === date && t.bonde === bonde && t.saida === rota.saida && t.retorno === rota.retorno);
                            if (bondesViagens.length > 0) {
                                bondesViagens.forEach(viagem => {
                                    const pagantes = parseInt(viagem.pagantes);
                                    const moradores = parseInt(viagem.moradores);
                                    const gratuitos = parseInt(viagem.gratPcdIdoso);
                                    const total = pagantes + moradores + gratuitos;
                                    if (total > 0) {
                                        reportData.push({ 
                                            date, 
                                            bonde, 
                                            saida: rota.saida, 
                                            retorno: rota.retorno, 
                                            maquinista: viagem.maquinista || 'N/A',
                                            agente: viagem.agente || 'N/A',
                                            hora: viagem.hora || 'N/A',
                                            pagantes, 
                                            moradores, 
                                            gratuitos, 
                                            total 
                                        });
                                    }
                                });
                            }
                        });
                    });
                });

                reportData.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.date}</td>
                        <td>${row.bonde}</td>
                        <td>${row.saida}</td>
                        <td>${row.retorno}</td>
                        <td>${row.maquinista}</td>
                        <td>${row.agente}</td>
                        <td>${row.hora}</td>
                        <td>${row.pagantes}</td>
                        <td>${row.moradores}</td>
                        <td>${row.gratuitos}</td>
                        <td>${row.total}</td>
                    `;
                    reportTableBody.appendChild(tr);
                });

                const totalPagantes = reportData.reduce((sum, row) => sum + row.pagantes, 0);
                const totalMoradores = reportData.reduce((sum, row) => sum + row.moradores, 0);
                const totalGratuitos = reportData.reduce((sum, row) => sum + row.gratuitos, 0);
                const totalPassageiros = totalPagantes + totalMoradores + totalGratuitos;

                summaryContent.innerHTML = `
                    <div class="summary-item"><span>Total Pagantes</span><span>${totalPagantes}</span></div>
                    <div class="summary-item"><span>Total Moradores</span><span>${totalMoradores}</span></div>
                    <div class="summary-item"><span>Total Gratuitos</span><span>${totalGratuitos}</span></div>
                    <div class="summary-item"><span>Total Passageiros</span><span>${totalPassageiros}</span></div>
                `;
                currentReportData = { type: 'semanal', date: dateValue, data: reportData, bondeTotals, routeTotals, hourlyCariocaTotals, summary: { totalPagantes, totalMoradores, totalGratuitos, totalPassageiros } };

            } else if (reportType === 'mensal') {
                const year = parseInt(dateValue);
                const month = parseInt(monthValue);
                filteredViagens = filteredViagens.filter(t => {
                    const transactionDate = new Date(t.data);
                    return transactionDate.getFullYear() === year && transactionDate.getMonth() === month;
                });
                reportTableHead.innerHTML = `
                    <tr>
                        <th>Data</th>
                        <th>Bonde</th>
                        <th>Saída</th>
                        <th>Retorno</th>
                        <th>Maquinista</th>
                        <th>Agente</th>
                        <th>Hora</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuitos</th>
                        <th>Total Passageiros</th>
                    </tr>
                `;
                const reportData = [];
                const dates = [];
                const startDate = new Date(year, month, 1);
                const endDate = new Date(year, month + 1, 0);
                let currentDate = new Date(startDate);
                while (currentDate <= endDate) {
                    dates.push(formatDate(currentDate));
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                dates.forEach(date => {
                    bondes.forEach(bonde => {
                        rotas.forEach(rota => {
                            const bondesViagens = filteredViagens.filter(t => t.data === date && t.bonde === bonde && t.saida === rota.saida && t.retorno === rota.retorno);
                            if (bondesViagens.length > 0) {
                                bondesViagens.forEach(viagem => {
                                    const pagantes = parseInt(viagem.pagantes);
                                    const moradores = parseInt(viagem.moradores);
                                    const gratuitos = parseInt(viagem.gratPcdIdoso);
                                    const total = pagantes + moradores + gratuitos;
                                    if (total > 0) {
                                        reportData.push({ 
                                            date, 
                                            bonde, 
                                            saida: rota.saida, 
                                            retorno: rota.retorno, 
                                            maquinista: viagem.maquinista || 'N/A',
                                            agente: viagem.agente || 'N/A',
                                            hora: viagem.hora || 'N/A',
                                            pagantes, 
                                            moradores, 
                                            gratuitos, 
                                            total 
                                        });
                                    }
                                });
                            }
                        });
                    });
                });

                reportData.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.date}</td>
                        <td>${row.bonde}</td>
                        <td>${row.saida}</td>
                        <td>${row.retorno}</td>
                        <td>${row.maquinista}</td>
                        <td>${row.agente}</td>
                        <td>${row.hora}</td>
                        <td>${row.pagantes}</td>
                        <td>${row.moradores}</td>
                        <td>${row.gratuitos}</td>
                        <td>${row.total}</td>
                    `;
                    reportTableBody.appendChild(tr);
                });

                const totalPagantes = reportData.reduce((sum, row) => sum + row.pagantes, 0);
                const totalMoradores = reportData.reduce((sum, row) => sum + row.moradores, 0);
                const totalGratuitos = reportData.reduce((sum, row) => sum + row.gratuitos, 0);
                const totalPassageiros = totalPagantes + totalMoradores + totalGratuitos;
                const totalDays = dates.length;
                const mediaDiaria = totalDays > 0 ? Math.round(totalPassageiros / totalDays) : 0;

                summaryContent.innerHTML = `
                    <div class="summary-item"><span>Total Pagantes</span><span>${totalPagantes}</span></div>
                    <div class="summary-item"><span>Total Moradores</span><span>${totalMoradores}</span></div>
                    <div class="summary-item"><span>Total Gratuitos</span><span>${totalGratuitos}</span></div>
                    <div class="summary-item"><span>Total Passageiros</span><span>${totalPassageiros}</span></div>
  
                `;
                currentReportData = { type: 'mensal', date: `${year}-${String(month + 1).padStart(2, '0')}`, data: reportData, bondeTotals, routeTotals, hourlyCariocaTotals, summary: { totalPagantes, totalMoradores, totalGratuitos, totalPassageiros, mediaDiaria } };

            } else if (reportType === 'anual') {
                const year = parseInt(dateValue);
                filteredViagens = filteredViagens.filter(t => new Date(t.data).getFullYear() === year);
                reportTableHead.innerHTML = `
                    <tr>
                        <th>Bonde</th>
                        <th>Saída</th>
                        <th>Retorno</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuitos</th>
                        <th>Total Passageiros</th>
                
                    </tr>
                `;
                const reportData = [];
                bondes.forEach(bonde => {
                    rotas.forEach(rota => {
                        const bondesViagens = filteredViagens.filter(t => t.bonde === bonde && t.saida === rota.saida && t.retorno === rota.retorno);
                        if (bondesViagens.length > 0) {
                            const pagantes = bondesViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0);
                            const moradores = bondesViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0);
                            const gratuitos = bondesViagens.reduce((sum, t) => sum + parseInt(t.gratPcdIdoso), 0);
                            const total = pagantes + moradores + gratuitos;
                            const monthsWithData = new Set(bondesViagens.map(t => new Date(t.data).getMonth())).size;
                            const mediaMensal = monthsWithData > 0 ? Math.round(total / monthsWithData) : 0;
                            if (total > 0) {
                                reportData.push({ bonde, saida: rota.saida, retorno: rota.retorno, pagantes, moradores, gratuitos, total, mediaMensal });
                            }
                        }
                    });
                });

                reportData.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.bonde}</td>
                        <td>${row.saida}</td>
                        <td>${row.retorno}</td>
                        <td>${row.pagantes}</td>
                        <td>${row.moradores}</td>
                        <td>${row.gratuitos}</td>
                        <td>${row.total}</td>
               
                    `;
                    reportTableBody.appendChild(tr);
                });

                const totalPagantes = reportData.reduce((sum, row) => sum + row.pagantes, 0);
                const totalMoradores = reportData.reduce((sum, row) => sum + row.moradores, 0);
                const totalGratuitos = reportData.reduce((sum, row) => sum + row.gratuitos, 0);
                const totalPassageiros = totalPagantes + totalMoradores + totalGratuitos;
                const monthsWithData = new Set(filteredViagens.map(t => new Date(t.data).getMonth())).size;
                const mediaMensalTotal = monthsWithData > 0 ? Math.round(totalPassageiros / monthsWithData) : 0;

                summaryContent.innerHTML = `
                    <div class="summary-item"><span>Total Pagantes</span><span>${totalPagantes}</span></div>
                    <div class="summary-item"><span>Total Moradores</span><span>${totalMoradores}</span></div>
                    <div class="summary-item"><span>Total Gratuitos</span><span>${totalGratuitos}</span></div>
                    <div class="summary-item"><span>Total Passageiros</span><span>${totalPassageiros}</span></div>
                 
                `;
                currentReportData = { type: 'anual', date: dateValue, data: reportData, bondeTotals, routeTotals, hourlyCariocaTotals, summary: { totalPagantes, totalMoradores, totalGratuitos, totalPassageiros, mediaMensalTotal } };
            }

            if (reportTableBody.children.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="${reportTableHead.children[0].children.length}" style="text-align: center; color: #e74c3c;">Nenhum dado encontrado para o período selecionado.</td>`;
                reportTableBody.appendChild(tr);
                summarySection.style.display = 'none';
                bondeTotalSection.style.display = 'none';
                routeTotalSection.style.display = 'none';
                hourlyCariocaSection.style.display = 'none';
                exportPdfBtn.disabled = true;
            }
        }

       function exportToPDF() {
    if (!currentReportData) {
        alert('Por favor, gere um relatório antes de exportar.');
        return;
    }

    const userName = document.getElementById('user-name').value || 'USUÁRIO NÃO INFORMADO';
    const userRegistration = document.getElementById('user-registration').value || 'MATRÍCULA NÃO INFORMADA';
    const reportType = document.getElementById('report-type').value;
    const planType = reportType === 'anual' ? 'Plano Anual' : 'Plano Mensal';

    const doc = new jsPDF('landscape', 'mm', 'a4');
            
    doc.setFillColor(25, 40, 68);
    doc.rect(0, 0, 297, 40, 'F');
            
    // Main title with standardized font size
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(16);
    doc.setFont(undefined, 'bold');
    doc.text('Secretaria de Estado de Transporte', 148.5, 12, { align: 'center' });
    doc.text('e Mobilidade Urbana', 148.5, 20, { align: 'center' });
            
    doc.setFontSize(10);
    doc.setFont(undefined, 'bold');
    const currentDate = new Date().toLocaleDateString('pt-BR');
    doc.text(`Relatório Elaborado por: ${userName}`, 148.5, 28, { align: 'center' });
    doc.text(`Matrícula: ${userRegistration}`, 148.5, 32, { align: 'center' });
    doc.text(`Data do relatório: ${currentDate} | ${planType}`, 148.5, 36, { align: 'center' });

    doc.setDrawColor(25, 40, 68);
    doc.setLineWidth(1);
    doc.line(10, 45, 287, 45);

    // Reset text color for content
    doc.setTextColor(0, 0, 0);
            
    const dateValue = currentReportData.date;
            
    doc.setFontSize(14);
    doc.setFont(undefined, 'bold');
    doc.setTextColor(25, 40, 68);
    const title = `Relatório ${reportType.charAt(0).toUpperCase() + reportType.slice(1)} - Bondes Santa Teresa - ${dateValue}`;
    doc.text(title, 148.5, 55, { align: 'center' });

    const headers = Array.from(reportTableHead.children[0].children).map(th => th.textContent);
    const data = Array.from(reportTableBody.children).map(row =>
        Array.from(row.children).map(cell => cell.textContent)
    );

    if (headers.length === 0 || data.length === 0) {
        alert('Nenhum dado disponível para exportar.');
        return;
    }

    doc.autoTable({
        head: [headers],
        body: data,
        startY: 65,
        theme: 'grid',
        styles: { 
            fontSize: 9, 
            cellPadding: 3,
            halign: 'center',
            fontStyle: 'bold',
            lineColor: [25, 40, 68],
            lineWidth: 0.1 // Thinner lines as requested
        },
        headStyles: { 
            fillColor: [25, 40, 68], 
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            fontSize: 10,
            halign: 'center'
        },
        alternateRowStyles: { 
            fillColor: [248, 250, 252] 
        },
        didParseCell: function(data) {
            if (data.column.index === headers.indexOf('Retorno') && data.cell.text[0] === 'Carioca') {
                data.cell.styles.textColor = [220, 53, 69];
                data.cell.styles.fontStyle = 'bold';
            }
        }
    });

    let finalY = doc.lastAutoTable.finalY + 15;
            
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.setTextColor(25, 40, 68);
    doc.text('RESUMO EXECUTIVO', 148.5, finalY, { align: 'center' });
            
    finalY += 8;
            
    const summaryData = Object.entries(currentReportData.summary).map(([key, value]) => {
        const label = key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
        return [label, value];
    });

    doc.autoTable({
        body: summaryData,
        startY: finalY,
        theme: 'grid',
        styles: { 
            fontSize: 9, 
            cellPadding: 3,
            halign: 'center',
            fontStyle: 'bold',
            lineColor: [25, 40, 68],
            lineWidth: 0.1
        },
        columnStyles: {
            0: { cellWidth: 80, fillColor: [25, 40, 68], textColor: [255, 255, 255] },
            1: { cellWidth: 40, fillColor: [248, 250, 252] }
        }
    });

    finalY = doc.lastAutoTable.finalY + 20;
            
    // TOTAIS POR BONDE - primeira seção
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.setTextColor(25, 40, 68);
    doc.text('TOTAIS POR BONDE', 148.5, finalY, { align: 'center' });
            
    finalY += 8;
            
    const bondeHeaders = ['Bonde', 'Pagantes', 'Moradores', 'Gratuitos', 'Total'];
    const bondeData = currentReportData.bondeTotals.map(row => [row.bonde, row.totalPagantes, row.totalMoradores, row.totalGratuitos, row.total]);

    doc.autoTable({
        head: [bondeHeaders],
        body: bondeData,
        startY: finalY,
        margin: { left: 10, right: 10 },
        theme: 'grid',
        showHead: 'firstPage',
        styles: { 
            fontSize: 9, 
            cellPadding: 4,
            halign: 'center',
            fontStyle: 'bold',
            lineColor: [25, 40, 68],
            lineWidth: 0.1
        },
        headStyles: { 
            fillColor: [25, 40, 68], 
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            halign: 'center'
        },
        columnStyles: {
            0: { cellWidth: 40 }, // Bonde - mais espaço para o nome
            1: { cellWidth: 30 }, // Pagantes
            2: { cellWidth: 30 }, // Moradores  
            3: { cellWidth: 30 }, // Gratuitos
            4: { cellWidth: 30 }  // Total
        }
    });

    finalY = doc.lastAutoTable.finalY + 20;

    // TOTAIS POR ROTA - segunda seção
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.setTextColor(25, 40, 68);
    doc.text('TOTAIS POR ROTA', 148.5, finalY, { align: 'center' });
            
    finalY += 8;

    const routeHeaders = ['Saída', 'Retorno', 'Pagantes', 'Moradores', 'Gratuitos', 'Total'];
    const routeData = currentReportData.routeTotals.map(row => [row.saida, row.retorno, row.totalPagantes, row.totalMoradores, row.totalGratuitos, row.total]);

    doc.autoTable({
        head: [routeHeaders],
        body: routeData,
        startY: finalY,
        margin: { left: 10, right: 10 },
        theme: 'grid',
        showHead: 'firstPage',
        styles: { 
            fontSize: 9, 
            cellPadding: 4,
            halign: 'center',
            fontStyle: 'bold',
            lineColor: [25, 40, 68],
            lineWidth: 0.1
        },
        headStyles: { 
            fillColor: [25, 40, 68], 
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            halign: 'center'
        },
        columnStyles: {
            0: { cellWidth: 35 }, // Saída - mais espaço para nomes de locais
            1: { cellWidth: 35 }, // Retorno - mais espaço para nomes de locais
            2: { cellWidth: 25 }, // Pagantes
            3: { cellWidth: 25 }, // Moradores
            4: { cellWidth: 25 }, // Gratuitos
            5: { cellWidth: 25 }  // Total
        },
        didParseCell: function(data) {
            if ((data.column.index === 0 || data.column.index === 1) && data.cell.text[0] === 'Carioca') {
                data.cell.styles.textColor = [220, 53, 69];
                data.cell.styles.fontStyle = 'bold';
            }
        }
    });

    finalY = doc.lastAutoTable.finalY + 20;

    // PASSAGEIROS POR HORA - terceira seção
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.setTextColor(25, 40, 68);
    doc.text('PASSAGEIROS POR HORA', 148.5, finalY, { align: 'center' });
            
    finalY += 8;

    const hourlyCariocaHeaders = ['Hora', 'Pagantes', 'Moradores', 'Gratuitos', 'Total'];
    const hourlyCariocaData = currentReportData.hourlyCariocaTotals.slice(0, 8).map(row => [row.hora, row.totalPagantes, row.totalMoradores, row.totalGratuitos, row.total]);

    doc.autoTable({
        head: [hourlyCariocaHeaders],
        body: hourlyCariocaData,
        startY: finalY,
        margin: { left: 10, right: 10 },
        theme: 'grid',
        showHead: 'firstPage',
        styles: { 
            fontSize: 9, 
            cellPadding: 4,
            halign: 'center',
            fontStyle: 'bold',
            lineColor: [25, 40, 68],
            lineWidth: 0.1
        },
        headStyles: { 
            fillColor: [25, 40, 68], 
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            halign: 'center'
        },
        columnStyles: {
            0: { cellWidth: 50 }, // Hora - mais espaço para formato de hora
            1: { cellWidth: 25 }, // Pagantes
            2: { cellWidth: 25 }, // Moradores
            3: { cellWidth: 25 }, // Gratuitos
            4: { cellWidth: 25 }  // Total
        }
    });

    doc.setDrawColor(25, 40, 68);
    doc.setLineWidth(1);
    doc.line(10, 190, 287, 190);
            
    doc.setTextColor(25, 40, 68);
    doc.setFontSize(9);
    doc.setFont(undefined, 'bold');
    doc.text('Secretaria de Estado de Transporte e Mobilidade Urbana', 10, 195);
    doc.text(`Gerado em: ${new Date().toLocaleString('pt-BR')}`, 287, 195, { align: 'right' });
    doc.text('Página 1 de 1', 148.5, 195, { align: 'center' });
    
    doc.text(`Data do Relatório: ${dateValue}`, 148.5, 200, { align: 'center' });

    doc.save(`relatorio_${reportType}_${dateValue}.pdf`);
}

        reportTypeInput.addEventListener('change', updateDateInput);
        document.getElementById('generate-report-btn').addEventListener('click', generateReport);
        document.getElementById('export-pdf-btn').addEventListener('click', exportToPDF);

    </script>
</body>
</html>

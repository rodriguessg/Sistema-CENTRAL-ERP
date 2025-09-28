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

    // Query para buscar os dados da tabela viagens, incluindo grat_pcd_idoso
    $sql = "SELECT data, bonde, saida, retorno, maquinista, agente, hora, pagantes, moradores, grat_pcd_idoso, tipo_viagem FROM viagens";
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
    <link rel="stylesheet" href="src/bonde/style/relatoriobonde.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        /* Added styles for error messages and readonly inputs */
        .error-message {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin: 10px 0;
            font-weight: 600;
            display: none;
        }
        
        .error-message.show {
            display: block;
        }
        
        /* Removed readonly styles since we're allowing calendar interaction */
        input[type="date"]:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
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
        
        <!-- Added error message container -->
        <div id="error-container" class="error-message">
            <i data-lucide="alert-circle" style="display: inline; margin-right: 8px;"></i>
            <span id="error-text"></span>
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
            </div>
            <div class="filters-section">
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
                        <option value="periodo">Período Personalizado</option>
                    </select>
                </div>
                <div class="input-item" id="date-input-container">
                    <label for="report-date">
                        <i data-lucide="calendar-days" class="icon"></i>
                        Data
                    </label>
                    <input 
                        type="date" 
                        id="report-date" 
                        onkeydown="return false" 
                        onpaste="return false"
                    >
                </div>
                <div class="input-item" id="date-start-container" style="display: none;">
                    <label for="date-start">
                        <i data-lucide="calendar-days" class="icon"></i>
                        Data Inicial
                    </label>
                    <input 
                        type="date" 
                        id="date-start" 
                        onkeydown="return false" 
                        onpaste="return false"
                    >
                </div>
                <div class="input-item" id="date-end-container" style="display: none;">
                    <label for="date-end">
                        <i data-lucide="calendar-days" class="icon"></i>
                        Data Final
                    </label>
                    <input 
                        type="date" 
                        id="date-end" 
                        onkeydown="return false" 
                        onpaste="return false"
                    >
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
                        <th>Gratuitos PCD/Idoso</th>
                        <th>Gratuitos Totais</th>
                        <th>Total Passageiros</th>
                        <th>Total Viagens</th>
                        <th>Viagens Ida</th>
                        <th>Viagens Retorno</th>
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
                        <th>Gratuitos PCD/Idoso</th>
                        <th>Gratuitos Totais</th>
                        <th>Total Passageiros</th>
                        <th>Total Viagens</th>
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
                        <th>Subida</th>
                        <th>Retorno</th>
                    </tr>
                </thead>
                <tbody id="hourly-carioca-table-body"></tbody>
            </table>
        </div>

        <div class="general-totals-section" id="general-totals-section" style="display: none;">
            <h3>
                <i data-lucide="align-justify" class="icon"></i>
                Totais Gerais
            </h3>
            <div id="general-totals-content">
                <div class="summary-item"><span>Total Pagantes</span><span id="totalPagantes">0</span></div>
                <div class="summary-item"><span>Total Moradores</span><span id="totalMoradores">0</span></div>
                <div class="summary-item"><span>Total Gratuitos PCD/Idoso</span><span id="totalGratPcdIdoso">0</span></div>
                <div class="summary-item"><span>Total Gratuitos</span><span id="totalGratuitos">0</span></div>
                <div class="summary-item"><span>Total Passageiros</span><span id="totalPassageiros">0</span></div>
                <div class="summary-item"><span>Total Viagens</span><span id="totalViagens">0</span></div>
                <div class="summary-item"><span>Viagens Ida</span><span id="viagensIda">0</span></div>
                <div class="summary-item"><span>Viagens Retorno</span><span id="viagensRetorno">0</span></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            updateDateInput();
            
            function preventManualTyping() {
                const dateInputs = document.querySelectorAll('input[type="date"]');
                dateInputs.forEach(input => {
                    // Block all keyboard events except Tab navigation
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Tab' || (e.shiftKey && e.key === 'Tab')) {
                            return true;
                        }
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    });
                    
                    input.addEventListener('keypress', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    });
                    
                    input.addEventListener('keyup', function(e) {
                        if (e.key !== 'Tab' && !(e.shiftKey && e.key === 'Tab')) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }
                    });
                    
                    input.addEventListener('input', function(e) {
                        if (!e.isTrusted) {
                            return;
                        }
                        const currentValue = this.value;
                        const previousValue = this.getAttribute('data-previous-value') || '';
                        if (currentValue && !this.validity.valid) {
                            this.value = previousValue;
                        } else {
                            this.setAttribute('data-previous-value', currentValue);
                        }
                    });
                    
                    input.addEventListener('paste', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    });
                    
                    input.addEventListener('drop', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    });
                    
                    input.addEventListener('contextmenu', function(e) {
                        e.preventDefault();
                        return false;
                    });
                    
                    input.setAttribute('data-previous-value', input.value);
                    input.style.caretColor = 'transparent';
                    input.style.userSelect = 'none';
                });
            }
            
            preventManualTyping();
            
            const generateBtn = document.getElementById('generate-report-btn');
            const exportBtn = document.getElementById('export-pdf-btn');
            
            if (generateBtn) {
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
                
                const generateBtnSpan = generateBtn.querySelector('span');
                const generateBtnIcon = generateBtn.querySelector('i');
                if (generateBtnSpan) {
                    generateBtnSpan.style.cssText = 'color: #ffffff !important;';
                }
                if (generateBtnIcon) {
                    generateBtnIcon.style.cssText = 'color: #ffffff !important;';
                }
                
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
                
                const exportBtnSpan = exportBtn.querySelector('span');
                const exportBtnIcon = exportBtn.querySelector('i');
                if (exportBtnSpan) {
                    exportBtnSpan.style.cssText = 'color: #ffffff !important;';
                }
                if (exportBtnIcon) {
                    exportBtnIcon.style.cssText = 'color: #ffffff !important;';
                }
                
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
        const generalTotalsSection = document.getElementById('general-totals-section');

        let viagens = <?php echo json_encode($viagens); ?>;
        let currentReportData = null;

        function validateDate(dateString) {
            if (!dateString) return { valid: false, message: "Data não informada." };
            
            const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
            if (!dateRegex.test(dateString)) {
                return { valid: false, message: "Formato de data inválido. Use AAAA-MM-DD." };
            }
            
            const [year, month, day] = dateString.split('-').map(Number);
            
            if (year < 1900 || year > 2100) {
                return { valid: false, message: "Ano deve estar entre 1900 e 2100." };
            }
            
            if (month < 1 || month > 12) {
                return { valid: false, message: "Mês deve estar entre 01 e 12." };
            }
            
            const daysInMonth = new Date(year, month, 0).getDate();
            
            if (day < 1 || day > daysInMonth) {
                const monthNames = [
                    "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
                    "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
                ];
                return { 
                    valid: false, 
                    message: `O mês de ${monthNames[month - 1]} de ${year} não possui ${day} dias. Este mês tem apenas ${daysInMonth} dias.` 
                };
            }
            
            const testDate = new Date(year, month - 1, day);
            if (testDate.getFullYear() !== year || testDate.getMonth() !== month - 1 || testDate.getDate() !== day) {
                return { valid: false, message: "Data inválida detectada." };
            }
            
            return { valid: true, message: "" };
        }

        function showError(message) {
            const errorContainer = document.getElementById('error-container');
            const errorText = document.getElementById('error-text');
            errorText.textContent = message;
            errorContainer.classList.add('show');
            
            setTimeout(() => {
                errorContainer.classList.remove('show');
            }, 5000);
        }

        function hideError() {
            const errorContainer = document.getElementById('error-container');
            errorContainer.classList.remove('show');
        }

        function updateDateInput() {
            const reportType = reportTypeInput.value;
            dateInputContainer.innerHTML = '';
            monthInputContainer.style.display = 'none';
            document.getElementById('date-start-container').style.display = 'none';
            document.getElementById('date-end-container').style.display = 'none';
            
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
                preventManualTyping();
            } else if (reportType === 'periodo') {
                dateInputContainer.style.display = 'none';
                document.getElementById('date-start-container').style.display = 'block';
                document.getElementById('date-end-container').style.display = 'block';
                setTimeout(() => {
                    preventManualTyping();
                }, 100);
            } else if (reportType === 'semanal') {
                input = document.createElement('input');
                input.type = 'week';
                input.id = 'report-date';
                input.value = '2025-W27';
                input.required = true;
                dateInputContainer.innerHTML = '<label for="report-date"><i data-lucide="calendar-days" class="icon"></i>Semana</label>';
                lucide.createIcons();
                dateInputContainer.appendChild(input);
                setTimeout(() => {
                    const weekInput = document.getElementById('report-date');
                    if (weekInput) {
                        weekInput.addEventListener('keydown', function(e) {
                            if (e.key === 'Tab' || (e.shiftKey && e.key === 'Tab')) {
                                return true;
                            }
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        });
                        
                        weekInput.addEventListener('keypress', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        });
                        
                        weekInput.addEventListener('paste', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        });
                    }
                }, 100);
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
            hideError();
            
            const reportType = reportTypeInput.value;
            const dateValue = reportTypeInput.value === 'periodo' ? null : document.getElementById('report-date')?.value;
            const dateStart = document.getElementById('date-start')?.value;
            const dateEnd = document.getElementById('date-end')?.value;
            const monthValue = reportMonthInput?.value;
            const bondeValue = bondeInput.value;

            if (reportType === 'diario' && dateValue) {
                const validation = validateDate(dateValue);
                if (!validation.valid) {
                    showError(validation.message);
                    return;
                }
            } else if (reportType === 'periodo') {
                if (dateStart) {
                    const startValidation = validateDate(dateStart);
                    if (!startValidation.valid) {
                        showError("Data inicial inválida: " + startValidation.message);
                        return;
                    }
                }
                if (dateEnd) {
                    const endValidation = validateDate(dateEnd);
                    if (!endValidation.valid) {
                        showError("Data final inválida: " + endValidation.message);
                        return;
                    }
                }
                if (dateStart && dateEnd && dateStart > dateEnd) {
                    showError("A data inicial não pode ser posterior à data final.");
                    return;
                }
            }

            let filteredViagens = viagens.filter(t => !bondeValue || t.bonde === bondeValue);

            function isValidDate(dateStr) {
                if (!dateStr || dateStr === '0000-00-00' || dateStr === '' || dateStr === null) {
                    return false;
                }
                const regex = /^\d{4}-\d{2}-\d{2}$/;
                return regex.test(dateStr);
            }

            if (reportType === 'diario') {
                filteredViagens = filteredViagens.filter(t => t.data === dateValue);
            } else if (reportType === 'periodo') {
                if (dateStart && dateEnd) {
                    console.log("[v0] Date range:", dateStart, "to", dateEnd);
                    
                    filteredViagens = filteredViagens.filter(t => {
                        if (!isValidDate(t.data)) {
                            console.log("[v0] Invalid date found:", t.data);
                            return false;
                        }
                        
                        const tripDate = t.data.toString().trim();
                        const isInRange = tripDate >= dateStart && tripDate <= dateEnd;
                        
                        console.log("[v0] Checking trip:", tripDate, "Range:", dateStart, "to", dateEnd, "Result:", isInRange);
                        
                        return isInRange;
                    });
                }
            } else if (reportType === 'semanal') {
                const { start, end } = getWeekStartEnd(dateValue);
                const startStr = start.toISOString().split('T')[0];
                const endStr = end.toISOString().split('T')[0];
                filteredViagens = filteredViagens.filter(t => {
                    return t.data >= startStr && t.data <= endStr;
                });
            } else if (reportType === 'mensal') {
                const year = parseInt(dateValue);
                const month = parseInt(monthValue);
                const startOfMonth = `${year}-${String(month + 1).padStart(2, '0')}-01`;
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const endOfMonth = `${year}-${String(month + 1).padStart(2, '0')}-${String(daysInMonth).padStart(2, '0')}`;
                filteredViagens = filteredViagens.filter(t => {
                    return t.data >= startOfMonth && t.data <= endOfMonth;
                });
            } else if (reportType === 'anual') {
                const year = parseInt(dateValue);
                const startOfYear = `${year}-01-01`;
                const endOfYear = `${year}-12-31`;
                filteredViagens = filteredViagens.filter(t => {
                    return t.data >= startOfYear && t.data <= endOfYear;
                });
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

            const generalTotals = {
                totalPagantes: filteredViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0),
                totalMoradores: filteredViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0),
                totalGratPcdIdoso: filteredViagens.reduce((sum, t) => sum + parseInt(t.grat_pcd_idoso), 0),
                totalViagens: filteredViagens.length,
                viagensIda: filteredViagens.filter(t => t.tipo_viagem === 'ida').length,
                viagensRetorno: filteredViagens.filter(t => t.tipo_viagem === 'retorno').length
            };
            generalTotals.totalGratuitos = generalTotals.totalMoradores + generalTotals.totalGratPcdIdoso;
            generalTotals.totalPassageiros = generalTotals.totalPagantes + generalTotals.totalMoradores + generalTotals.totalGratPcdIdoso;

            const bondeTotals = bondes.map(bonde => {
                let bondeViagens = filteredViagens.filter(t => t.bonde === bonde);
                const totalPagantes = bondeViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0);
                const totalMoradores = bondeViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0);
                const totalGratPcdIdoso = bondeViagens.reduce((sum, t) => sum + parseInt(t.grat_pcd_idoso), 0);
                const totalGratuitos = totalMoradores + totalGratPcdIdoso;
                const total = totalPagantes + totalMoradores + totalGratPcdIdoso;
                
                const totalViagens = bondeViagens.length;
                const viagensIda = bondeViagens.filter(t => t.tipo_viagem === 'ida').length;
                const viagensRetorno = bondeViagens.filter(t => t.tipo_viagem === 'retorno').length;
                
                return { 
                    bonde, 
                    totalPagantes, 
                    totalMoradores, 
                    totalGratPcdIdoso,
                    totalGratuitos, 
                    total, 
                    totalViagens,
                    viagensIda,
                    viagensRetorno
                };
            }).filter(row => row.total > 0);

            bondeTotals.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.bonde}</td>
                    <td>${row.totalPagantes}</td>
                    <td>${row.totalMoradores}</td>
                    <td>${row.totalGratPcdIdoso}</td>
                    <td>${row.totalGratuitos}</td>
                    <td>${row.total}</td>
                    <td>${row.totalViagens}</td>
                    <td>${row.viagensIda}</td>
                    <td>${row.viagensRetorno}</td>
                `;
                bondeTotalTableBody.appendChild(tr);
            });

            const routeTotals = rotas.map(rota => {
                let rotaViagens = filteredViagens.filter(t => t.saida === rota.saida && t.retorno === rota.retorno);
                const totalPagantes = rotaViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0);
                const totalMoradores = rotaViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0);
                const totalGratPcdIdoso = rotaViagens.reduce((sum, t) => sum + parseInt(t.grat_pcd_idoso), 0);
                const totalGratuitos = totalMoradores + totalGratPcdIdoso;
                const total = totalPagantes + totalMoradores + totalGratPcdIdoso;
                const totalViagens = rotaViagens.length;
                
                return { 
                    saida: rota.saida, 
                    retorno: rota.retorno, 
                    totalPagantes, 
                    totalMoradores, 
                    totalGratPcdIdoso,
                    totalGratuitos, 
                    total,
                    totalViagens
                };
            });

            routeTotals.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.saida}</td>
                    <td>${row.retorno}</td>
                    <td>${row.totalPagantes}</td>
                    <td>${row.totalMoradores}</td>
                    <td>${row.totalGratPcdIdoso}</td>
                    <td>${row.totalGratuitos}</td>
                    <td>${row.total}</td>
                    <td>${row.totalViagens}</td>
                `;
                routeTotalTableBody.appendChild(tr);
            });

            const hourlyGroups = {};
            
            filteredViagens.filter(t => t.saida === 'Carioca' || t.retorno === 'Carioca').forEach(viagem => {
                const hora = viagem.hora;
                const hourBlock = hora.split(':')[0] + ':00';
                
                if (!hourlyGroups[hourBlock]) {
                    hourlyGroups[hourBlock] = {
                        subida: 0,
                        retorno: 0
                    };
                }
                
                const totalPassageiros = parseInt(viagem.pagantes) + parseInt(viagem.moradores) + parseInt(viagem.grat_pcd_idoso);
                
                if (viagem.saida === 'Carioca' && viagem.tipo_viagem === 'ida') {
                    hourlyGroups[hourBlock].subida += totalPassageiros;
                } else if (viagem.retorno === 'Carioca' && viagem.tipo_viagem === 'retorno') {
                    hourlyGroups[hourBlock].retorno += totalPassageiros;
                }
            });
            
            const hourlyCariocaTotals = Object.keys(hourlyGroups)
                .sort()
                .map(hourBlock => {
                    const group = hourlyGroups[hourBlock];
                    return {
                        hora: hourBlock + ' - ' + hourBlock.split(':')[0] + ':59',
                        subida: group.subida,
                        retorno: group.retorno
                    };
                })
                .filter(row => row.subida > 0 || row.retorno > 0);

            hourlyCariocaTotals.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.hora}</td>
                    <td>${row.subida}</td>
                    <td>${row.retorno}</td>
                `;
                hourlyCariocaTableBody.appendChild(tr);
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
                    <th>Gratuitos PCD/Idoso</th>
                    <th>Gratuitos Totais</th>
                    <th>Total Passageiros</th>
                </tr>
            `;

            const reportData = [];
            filteredViagens.forEach(viagem => {
                const pagantes = parseInt(viagem.pagantes);
                const moradores = parseInt(viagem.moradores);
                const gratPcdIdoso = parseInt(viagem.grat_pcd_idoso);
                const gratuitos = moradores + gratPcdIdoso;
                const total = pagantes + moradores + gratPcdIdoso;
                if (total > 0) {
                    reportData.push({ 
                        data: viagem.data,
                        bonde: viagem.bonde, 
                        saida: viagem.saida, 
                        retorno: viagem.retorno, 
                        maquinista: viagem.maquinista || 'N/A',
                        agente: viagem.agente || 'N/A',
                        hora: viagem.hora || 'N/A',
                        pagantes, 
                        moradores, 
                        gratPcdIdoso,
                        gratuitos, 
                        total 
                    });
                }
            });

            reportData.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.data}</td>
                    <td>${row.bonde}</td>
                    <td>${row.saida}</td>
                    <td>${row.retorno}</td>
                    <td>${row.maquinista}</td>
                    <td>${row.agente}</td>
                    <td>${row.hora}</td>
                    <td>${row.pagantes}</td>
                    <td>${row.moradores}</td>
                    <td>${row.gratPcdIdoso}</td>
                    <td>${row.gratuitos}</td>
                    <td>${row.total}</td>
                `;
                reportTableBody.appendChild(tr);
            });

            const summaryData = {
                'Total Pagantes': generalTotals.totalPagantes,
                'Total Moradores': generalTotals.totalMoradores,
                'Total Gratuitos PCD/Idoso': generalTotals.totalGratPcdIdoso,
                'Total Gratuitos': generalTotals.totalGratuitos,
                'Total Passageiros': generalTotals.totalPassageiros,
                'Total Viagens': generalTotals.totalViagens
            };

            summaryContent.innerHTML = `
                <div class="summary-item"><span>Total Pagantes</span><span>${generalTotals.totalPagantes}</span></div>
                <div class="summary-item"><span>Total Moradores</span><span>${generalTotals.totalMoradores}</span></div>
                <div class="summary-item"><span>Total Gratuitos PCD/Idoso</span><span>${generalTotals.totalGratPcdIdoso}</span></div>
                <div class="summary-item"><span>Total Gratuitos</span><span>${generalTotals.totalGratuitos}</span></div>
                <div class="summary-item"><span>Total Passageiros</span><span>${generalTotals.totalPassageiros}</span></div>
                <div class="summary-item"><span>Total Viagens</span><span>${generalTotals.totalViagens}</span></div>
            `;

            currentReportData = {
                date: reportType === 'periodo' ? `${dateStart} a ${dateEnd}` : dateValue,
                summary: summaryData,
                bondeTotals,
                routeTotals,
                hourlyCariocaTotals
            };

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
                    lineWidth: 0.1
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
                columnStyles: {
                    0: { cellWidth: 25 }, // Data
                    1: { cellWidth: 25 }, // Bonde
                    2: { cellWidth: 25 }, // Saída
                    3: { cellWidth: 25 }, // Retorno
                    4: { cellWidth: 25 }, // Maquinista
                    5: { cellWidth: 25 }, // Agente
                    6: { cellWidth: 20 }, // Hora
                    7: { cellWidth: 20 }, // Pagantes
                    8: { cellWidth: 20 }, // Moradores
                    9: { cellWidth: 20 }, // Gratuitos PCD/Idoso
                    10: { cellWidth: 20 }, // Gratuitos Totais
                    11: { cellWidth: 20 }  // Total Passageiros
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
            
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(25, 40, 68);
            doc.text('TOTAIS POR BONDE', 148.5, finalY, { align: 'center' });
            
            finalY += 8;
            
            const bondeHeaders = ['Bonde', 'Pagantes', 'Moradores', 'Gratuitos PCD/Idoso', 'Gratuitos Totais', 'Total Passageiros', 'Total Viagens', 'Viagens Ida', 'Viagens Retorno'];
            const bondeData = currentReportData.bondeTotals.map(row => [
                row.bonde, 
                row.totalPagantes, 
                row.totalMoradores, 
                row.totalGratPcdIdoso,
                row.totalGratuitos, 
                row.total,
                row.totalViagens,
                row.viagensIda,
                row.viagensRetorno
            ]);

            doc.autoTable({
                head: [bondeHeaders],
                body: bondeData,
                startY: finalY,
                margin: { left: 10, right: 10 },
                theme: 'grid',
                showHead: 'firstPage',
                styles: { 
                    fontSize: 8, 
                    cellPadding: 3,
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
                    0: { cellWidth: 25 }, // Bonde
                    1: { cellWidth: 20 }, // Pagantes
                    2: { cellWidth: 20 }, // Moradores
                    3: { cellWidth: 20 }, // Gratuitos PCD/Idoso
                    4: { cellWidth: 20 }, // Gratuitos Totais
                    5: { cellWidth: 20 }, // Total Passageiros
                    6: { cellWidth: 20 }, // Total Viagens
                    7: { cellWidth: 20 }, // Viagens Ida
                    8: { cellWidth: 20 }  // Viagens Retorno
                }
            });

            finalY = doc.lastAutoTable.finalY + 20;
            
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(25, 40, 68);
            doc.text('TOTAIS POR ROTA', 148.5, finalY, { align: 'center' });
            
            finalY += 8;

            const routeHeaders = ['Saída', 'Retorno', 'Pagantes', 'Moradores', 'Gratuitos PCD/Idoso', 'Gratuitos Totais', 'Total', 'Total Viagens'];
            const routeData = currentReportData.routeTotals.map(row => [
                row.saida, 
                row.retorno, 
                row.totalPagantes, 
                row.totalMoradores, 
                row.totalGratPcdIdoso,
                row.totalGratuitos, 
                row.total,
                row.totalViagens
            ]);

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
                    0: { cellWidth: 35 }, // Saída
                    1: { cellWidth: 35 }, // Retorno
                    2: { cellWidth: 20 }, // Pagantes
                    3: { cellWidth: 20 }, // Moradores
                    4: { cellWidth: 20 }, // Gratuitos PCD/Idoso
                    5: { cellWidth: 20 }, // Gratuitos Totais
                    6: { cellWidth: 20 },  // Total
                    7: { cellWidth: 20 }  // Total Viagens
                },
                didParseCell: function(data) {
                    if ((data.column.index === 0 || data.column.index === 1) && data.cell.text[0] === 'Carioca') {
                        data.cell.styles.textColor = [220, 53, 69];
                        data.cell.styles.fontStyle = 'bold';
                    }
                }
            });

            finalY = doc.lastAutoTable.finalY + 20;

            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(25, 40, 68);
            doc.text('PASSAGEIROS POR HORA', 148.5, finalY, { align: 'center' });
            
            finalY += 8;

            const hourlyCariocaHeaders = ['Hora', 'Subida', 'Retorno'];
            const hourlyCariocaData = currentReportData.hourlyCariocaTotals.slice(0, 8).map(row => [row.hora, row.subida, row.retorno]);

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
                    0: { cellWidth: 50 }, // Hora
                    1: { cellWidth: 25 }, // Subida
                    2: { cellWidth: 25 }  // Retorno
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
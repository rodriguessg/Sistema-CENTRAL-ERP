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
    $sql = "SELECT data, bonde, saida, retorno, maquinista, agente, hora, pagantes, moradores, gratuidade AS gratPcdIdoso, tipo_viagem FROM viagens";
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
                    <input type="date" id="report-date" value="2025-07-02">
                </div>
                <div class="input-item" id="date-start-container" style="display: none;">
                    <label for="date-start">
                        <i data-lucide="calendar-days" class="icon"></i>
                        Data Inicial
                    </label>
                    <input type="date" id="date-start" value="2025-07-01">
                </div>
                <div class="input-item" id="date-end-container" style="display: none;">
                    <label for="date-end">
                        <i data-lucide="calendar-days" class="icon"></i>
                        Data Final
                    </label>
                    <input type="date" id="date-end" value="2025-07-31">
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
                        <th>Gratuitos</th>
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
        const generalTotalsSection = document.getElementById('general-totals-section');

        let viagens = <?php echo json_encode($viagens); ?>;
        let currentReportData = null;

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
            } else if (reportType === 'periodo') {
                dateInputContainer.style.display = 'none';
                document.getElementById('date-start-container').style.display = 'block';
                document.getElementById('date-end-container').style.display = 'block';
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
            const dateValue = reportTypeInput.value === 'periodo' ? null : document.getElementById('report-date')?.value;
            const dateStart = document.getElementById('date-start')?.value;
            const dateEnd = document.getElementById('date-end')?.value;
            const monthValue = reportMonthInput?.value;
            const bondeValue = bondeInput.value;

            let filteredViagens = viagens.filter(t => !bondeValue || t.bonde === bondeValue);

            if (reportType === 'diario') {
                // Direct string comparison for daily reports
                filteredViagens = filteredViagens.filter(t => t.data === dateValue);
            } else if (reportType === 'periodo') {
                if (dateStart && dateEnd) {
                    filteredViagens = filteredViagens.filter(t => {
                        // Ensure consistent date format comparison
                        const tData = t.data.toString();
                        return tData >= dateStart && tData <= dateEnd;
                    });
                }
            } else if (reportType === 'semanal') {
                const { start, end } = getWeekStartEnd(dateValue);
                const startStr = start.toISOString().split('T')[0]; // Convert to YYYY-MM-DD
                const endStr = end.toISOString().split('T')[0]; // Convert to YYYY-MM-DD
                filteredViagens = filteredViagens.filter(t => {
                    return t.data >= startStr && t.data <= endStr;
                });
            } else if (reportType === 'mensal') {
                const year = parseInt(dateValue);
                const month = parseInt(monthValue);
                // Create start and end date strings in YYYY-MM-DD format
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
                totalGratuitos: filteredViagens.reduce((sum, t) => sum + parseInt(t.gratPcdIdoso), 0),
                totalViagens: filteredViagens.length,
                viagensIda: filteredViagens.filter(t => t.tipo_viagem === 'ida').length,
                viagensRetorno: filteredViagens.filter(t => t.tipo_viagem === 'retorno').length
            };
            generalTotals.totalPassageiros = generalTotals.totalPagantes + generalTotals.totalMoradores + generalTotals.totalGratuitos;

            const bondeTotals = bondes.map(bonde => {
                let bondeViagens = filteredViagens.filter(t => t.bonde === bonde);
                // No need to re-filter by date since filteredViagens already contains the correct date range
                
                const totalPagantes = bondeViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0);
                const totalMoradores = bondeViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0);
                const totalGratuitos = bondeViagens.reduce((sum, t) => sum + parseInt(t.gratPcdIdoso), 0);
                const total = totalPagantes + totalMoradores + totalGratuitos;
                
                const totalViagens = bondeViagens.length;
                const viagensIda = bondeViagens.filter(t => t.tipo_viagem === 'ida').length;
                const viagensRetorno = bondeViagens.filter(t => t.tipo_viagem === 'retorno').length;
                
                return { 
                    bonde, 
                    totalPagantes, 
                    totalMoradores, 
                    totalGratuitos, 
                    total, 
                    totalViagens,
                    viagensIda,
                    viagensRetorno
                };
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
                    <td>${row.totalViagens}</td>
                    <td>${row.viagensIda}</td>
                    <td>${row.viagensRetorno}</td>
                `;
                bondeTotalTableBody.appendChild(tr);
            });

            // Calculate route totals
            const routeTotals = rotas.map(rota => {
                let rotaViagens = filteredViagens.filter(t => t.saida === rota.saida && t.retorno === rota.retorno);
                const totalPagantes = rotaViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0);
                const totalMoradores = rotaViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0);
                const totalGratuitos = rotaViagens.reduce((sum, t) => sum + parseInt(t.gratPcdIdoso), 0);
                const total = totalPagantes + totalMoradores + totalGratuitos;
                const totalViagens = rotaViagens.length;
                
                return { 
                    saida: rota.saida, 
                    retorno: rota.retorno, 
                    totalPagantes, 
                    totalMoradores, 
                    totalGratuitos, 
                    total,
                    totalViagens
                };
            });

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
                    <td>${row.totalViagens}</td>
                `;
                routeTotalTableBody.appendChild(tr);
            });

            const hourlyGroups = {};
            
            console.log("[v0] Report type:", reportType);
            console.log("[v0] Date start:", dateStart);
            console.log("[v0] Date end:", dateEnd);
            console.log("[v0] Total viagens after filtering:", filteredViagens.length);
            
            // Group trips by hour block - usando apenas filteredViagens que já está filtrado corretamente
            filteredViagens.filter(t => t.saida === 'Carioca' || t.retorno === 'Carioca').forEach(viagem => {
                console.log("[v0] Processing trip:", viagem.data, viagem.hora, viagem.saida, viagem.retorno, viagem.tipo_viagem);
                
                const hora = viagem.hora;
                const hourBlock = hora.split(':')[0] + ':00'; // Extract hour and format as XX:00
                
                if (!hourlyGroups[hourBlock]) {
                    hourlyGroups[hourBlock] = {
                        subida: 0,
                        retorno: 0
                    };
                }
                
                const totalPassageiros = parseInt(viagem.pagantes) + parseInt(viagem.moradores) + parseInt(viagem.gratPcdIdoso);
                
                // Determine direction based on saida/retorno and tipo_viagem
                if (viagem.saida === 'Carioca' && viagem.tipo_viagem === 'ida') {
                    hourlyGroups[hourBlock].subida += totalPassageiros;
                    console.log("[v0] Added to subida:", totalPassageiros, "for hour", hourBlock);
                } else if (viagem.retorno === 'Carioca' && viagem.tipo_viagem === 'retorno') {
                    hourlyGroups[hourBlock].retorno += totalPassageiros;
                    console.log("[v0] Added to retorno:", totalPassageiros, "for hour", hourBlock);
                }
            });
            
            console.log("[v0] Final hourly groups:", hourlyGroups);
            
            // Convert to array and sort by hour
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

            // Render hourly Carioca totals table
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
                    <th>Gratuitos</th>
                    <th>Total Passageiros</th>
                </tr>
            `;

            const reportData = [];
            filteredViagens.forEach(viagem => {
                const pagantes = parseInt(viagem.pagantes);
                const moradores = parseInt(viagem.moradores);
                const gratuitos = parseInt(viagem.gratPcdIdoso);
                const total = pagantes + moradores + gratuitos;
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
                    <td>${row.gratuitos}</td>
                    <td>${row.total}</td>
                `;
                reportTableBody.appendChild(tr);
            });

            const summaryData = {
                'Total Pagantes': generalTotals.totalPagantes,
                'Total Moradores': generalTotals.totalMoradores,
                'Total Gratuitos': generalTotals.totalGratuitos,
                'Total Passageiros': generalTotals.totalPassageiros,
                'Total Viagens': generalTotals.totalViagens
            };

            summaryContent.innerHTML = `
                <div class="summary-item"><span>Total Pagantes</span><span>${generalTotals.totalPagantes}</span></div>
                <div class="summary-item"><span>Total Moradores</span><span>${generalTotals.totalMoradores}</span></div>
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
            
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.setTextColor(25, 40, 68);
    doc.text('TOTAIS POR BONDE', 148.5, finalY, { align: 'center' });
            
    finalY += 8;
            
    const bondeHeaders = ['Bonde', 'Pagantes', 'Moradores', 'Gratuitos', 'Total Passageiros', 'Total Viagens', 'Viagens Ida', 'Viagens Retorno'];
    const bondeData = currentReportData.bondeTotals.map(row => [
        row.bonde, 
        row.totalPagantes, 
        row.totalMoradores, 
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
            1: { cellWidth: 25 }, // Pagantes
            2: { cellWidth: 25 }, // Moradores  
            3: { cellWidth: 25 }, // Gratuitos
            4: { cellWidth: 30 }, // Total Passageiros
            5: { cellWidth: 25 }, // Total Viagens
            6: { cellWidth: 25 }, // Viagens Ida
            7: { cellWidth: 25 }  // Viagens Retorno
        }
    });

    finalY = doc.lastAutoTable.finalY + 20;
            
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.setTextColor(25, 40, 68);
    doc.text('TOTAIS POR ROTA', 148.5, finalY, { align: 'center' });
            
    finalY += 8;

            const routeHeaders = ['Saída', 'Retorno', 'Pagantes', 'Moradores', 'Gratuitos', 'Total', 'Total Viagens'];
            const routeData = currentReportData.routeTotals.map(row => [row.saida, row.retorno, row.totalPagantes, row.totalMoradores, row.totalGratuitos, row.total, row.totalViagens]);

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
            5: { cellWidth: 25 },  // Total
            6: { cellWidth: 25 }  // Total Viagens
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
            0: { cellWidth: 50 }, // Hora - mais espaço para formato de hora
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

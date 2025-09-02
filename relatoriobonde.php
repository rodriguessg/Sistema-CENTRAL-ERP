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
    $sql = "SELECT data, bonde, saida, retorno, hora, pagantes, moradores, gratuidade AS gratPcdIdoso FROM viagens";
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
    <link rel="stylesheet" href="./src/bonde/style/relatoriobonde.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Relatórios - Bondes Santa Teresa</h1>
            <img src="logo-placeholder.png" alt="Logo Bondes Santa Teresa">
        </div>
        <div class="form-section">
            <h2>Gerar Relatório</h2>
            <div class="input-group">
                <div class="input-item">
                    <label for="report-type">Tipo de Relatório</label>
                    <select id="report-type">
                        <option value="diario">Diário</option>
                        <option value="semanal">Semanal</option>
                        <option value="mensal">Mensal</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>
                <div class="input-item" id="date-input-container">
                    <label for="report-date">Data</label>
                    <input type="date" id="report-date" value="2025-07-02">
                </div>
                <div class="input-item" id="month-input-container" style="display: none;">
                    <label for="report-month">Mês</label>
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
                    <label for="bonde">Bonde</label>
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
                <button id="generate-report-btn">Gerar Relatório</button>
                <button id="export-pdf-btn" disabled>Exportar como PDF</button>
            </div>
        </div>
        <div class="table-section" id="report-table-section" style="display: none;">
            <table id="report-table">
                <thead id="report-table-head"></thead>
                <tbody id="report-table-body"></tbody>
            </table>
        </div>
        <div class="summary-section" id="summary-section" style="display: none;">
            <h3>Resumo do Relatório</h3>
            <div id="summary-content"></div>
        </div>
        <div class="bonde-total-section" id="bonde-total-section" style="display: none;">
            <h3>Totais por Bonde</h3>
            <table id="bonde-total-table">
                <thead>
                    <tr>
                        <th>Bonde</th>
                        <th>Total Passageiros</th>
                    </tr>
                </thead>
                <tbody id="bonde-total-table-body"></tbody>
            </table>
        </div>
        <div class="route-total-section" id="route-total-section" style="display: none;">
            <h3>Totais por Rota</h3>
            <table id="route-total-table">
                <thead>
                    <tr>
                        <th>Saída</th>
                        <th>Retorno</th>
                        <th>Total Passageiros</th>
                    </tr>
                </thead>
                <tbody id="route-total-table-body"></tbody>
            </table>
        </div>
        <div class="hourly-carioca-section" id="hourly-carioca-section" style="display: none;">
            <h3>Passageiros por Hora (Carioca)</h3>
            <table id="hourly-carioca-table">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Total Passageiros</th>
                    </tr>
                </thead>
                <tbody id="hourly-carioca-table-body"></tbody>
            </table>
        </div>
    </div>

    <script>
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
                dateInputContainer.innerHTML = '<label for="report-date">Data</label>';
                dateInputContainer.appendChild(input);
            } else if (reportType === 'semanal') {
                input = document.createElement('input');
                input.type = 'week';
                input.id = 'report-date';
                input.value = '2025-W27';
                input.required = true;
                dateInputContainer.innerHTML = '<label for="report-date">Semana</label>';
                dateInputContainer.appendChild(input);
            } else if (reportType === 'mensal' || reportType === 'anual') {
                input = document.createElement('input');
                input.type = 'number';
                input.id = 'report-date';
                input.min = '2000';
                input.max = new Date().getFullYear();
                input.value = new Date().getFullYear();
                input.required = true;
                dateInputContainer.innerHTML = '<label for="report-date">Ano</label>';
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
                    bondeViagens = bondeViagens.filter(t => {
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
                const total = bondeViagens.reduce((sum, t) => sum + parseInt(t.pagantes) + parseInt(t.moradores) + parseInt(t.gratPcdIdoso), 0);
                return { bonde, total };
            }).filter(row => row.total > 0);

            // Render bonde totals table
            bondeTotals.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.bonde}</td>
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
                const total = routeViagens.reduce((sum, t) => sum + parseInt(t.pagantes) + parseInt(t.moradores) + parseInt(t.gratPcdIdoso), 0);
                return { saida: rota.saida, retorno: rota.retorno, total };
            }).filter(row => row.total > 0);

            // Render route totals table
            routeTotals.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.saida}</td>
                    <td>${row.retorno}</td>
                    <td>${row.total}</td>
                `;
                routeTotalTableBody.appendChild(tr);
            });

            // Calculate hourly Carioca totals
            const hourlyCariocaTotals = horas.map(hora => {
                let cariocaViagens = filteredViagens.filter(t => t.saida === 'Carioca' || t.retorno === 'Carioca').filter(t => t.hora === hora);
                if (reportType === 'diario') {
                    cariocaViagens = cariocaViagens.filter(t => t.data === dateValue);
                } else if (reportType === 'semanal') {
                    const { start, end } = getWeekStartEnd(dateValue);
                    cariocaViagens = cariocaViagens.filter(t => {
                        const transactionDate = new Date(t.data);
                        return transactionDate >= start && transactionDate <= end;
                    });
                } else if (reportType === 'mensal') {
                    const year = parseInt(dateValue);
                    const month = parseInt(monthValue);
                    cariocaViagens = cariocaViagens.filter(t => {
                        const transactionDate = new Date(t.data);
                        return transactionDate.getFullYear() === year && transactionDate.getMonth() === month;
                    });
                } else if (reportType === 'anual') {
                    const year = parseInt(dateValue);
                    cariocaViagens = cariocaViagens.filter(t => new Date(t.data).getFullYear() === year);
                }
                const total = cariocaViagens.reduce((sum, t) => sum + parseInt(t.pagantes) + parseInt(t.moradores) + parseInt(t.gratPcdIdoso), 0);
                return { hora, total };
            }).filter(row => row.total > 0);

            // Render hourly Carioca totals table
            hourlyCariocaTotals.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.hora}</td>
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
                            if (total > 0) {
                                reportData.push({ bonde, saida: rota.saida, retorno: rota.retorno, pagantes, moradores, gratuitos, total });
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
                                const pagantes = bondesViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0);
                                const moradores = bondesViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0);
                                const gratuitos = bondesViagens.reduce((sum, t) => sum + parseInt(t.gratPcdIdoso), 0);
                                const total = pagantes + moradores + gratuitos;
                                if (total > 0) {
                                    reportData.push({ date, bonde, saida: rota.saida, retorno: rota.retorno, pagantes, moradores, gratuitos, total });
                                }
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
                                const pagantes = bondesViagens.reduce((sum, t) => sum + parseInt(t.pagantes), 0);
                                const moradores = bondesViagens.reduce((sum, t) => sum + parseInt(t.moradores), 0);
                                const gratuitos = bondesViagens.reduce((sum, t) => sum + parseInt(t.gratPcdIdoso), 0);
                                const total = pagantes + moradores + gratuitos;
                                if (total > 0) {
                                    reportData.push({ date, bonde, saida: rota.saida, retorno: rota.retorno, pagantes, moradores, gratuitos, total });
                                }
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

            const doc = new jsPDF();
            const reportType = currentReportData.type;
            const dateValue = currentReportData.date;
            const title = `Relatório ${reportType.charAt(0).toUpperCase() + reportType.slice(1)} - Bondes Santa Teresa (${dateValue})`;
            doc.setFontSize(16);
            doc.text(title, 10, 10);

            // Main report table
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
                startY: 20,
                theme: 'grid',
                styles: { fontSize: 10, cellPadding: 2 },
                headStyles: { fillColor: [52, 152, 219], textColor: [255, 255, 255] },
                alternateRowStyles: { fillColor: [249, 251, 253] }
            });

            // Summary section
            let finalY = doc.lastAutoTable.finalY + 10;
            doc.setFontSize(12);
            doc.text('Resumo do Relatório', 10, finalY);
            finalY += 10;

            Object.entries(currentReportData.summary).forEach(([key, value]) => {
                const label = key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
                doc.text(`${label}: ${value}`, 10, finalY);
                finalY += 10;
            });

            // Bonde totals table
            finalY += 10;
            doc.setFontSize(12);
            doc.text('Totais por Bonde', 10, finalY);
            finalY += 10;

            const bondeHeaders = ['Bonde', 'Total Passageiros'];
            const bondeData = currentReportData.bondeTotals.map(row => [row.bonde, row.total]);

            doc.autoTable({
                head: [bondeHeaders],
                body: bondeData,
                startY: finalY,
                theme: 'grid',
                styles: { fontSize: 10, cellPadding: 2 },
                headStyles: { fillColor: [52, 152, 219], textColor: [255, 255, 255] },
                alternateRowStyles: { fillColor: [249, 251, 253] }
            });

            // Route totals table
            finalY = doc.lastAutoTable.finalY + 10;
            doc.setFontSize(12);
            doc.text('Totais por Rota', 10, finalY);
            finalY += 10;

            const routeHeaders = ['Saída', 'Retorno', 'Total Passageiros'];
            const routeData = currentReportData.routeTotals.map(row => [row.saida, row.retorno, row.total]);

            doc.autoTable({
                head: [routeHeaders],
                body: routeData,
                startY: finalY,
                theme: 'grid',
                styles: { fontSize: 10, cellPadding: 2 },
                headStyles: { fillColor: [52, 152, 219], textColor: [255, 255, 255] },
                alternateRowStyles: { fillColor: [249, 251, 253] }
            });

            // Hourly Carioca totals table
            finalY = doc.lastAutoTable.finalY + 10;
            doc.setFontSize(12);
            doc.text('Passageiros por Hora (Carioca)', 10, finalY);
            finalY += 10;

            const hourlyCariocaHeaders = ['Hora', 'Total Passageiros'];
            const hourlyCariocaData = currentReportData.hourlyCariocaTotals.map(row => [row.hora, row.total]);

            doc.autoTable({
                head: [hourlyCariocaHeaders],
                body: hourlyCariocaData,
                startY: finalY,
                theme: 'grid',
                styles: { fontSize: 10, cellPadding: 2 },
                headStyles: { fillColor: [52, 152, 219], textColor: [255, 255, 255] },
                alternateRowStyles: { fillColor: [249, 251, 253] }
            });

            doc.save(`relatorio_${reportType}_${dateValue}.pdf`);
        }

        reportTypeInput.addEventListener('change', updateDateInput);
        generateReportBtn.addEventListener('click', generateReport);
        exportPdfBtn.addEventListener('click', exportToPDF);

        document.addEventListener('DOMContentLoaded', () => {
            updateDateInput();
        });
    </script>
</body>
</html>
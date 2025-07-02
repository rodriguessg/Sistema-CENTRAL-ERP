<?php
include 'header.php';
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
                        <option value="BONDE 17">BONDE 17</option>
                        <option value="BONDE 16">BONDE 16</option>
                        <option value="BONDE 19">BONDE 19</option>
                        <option value="BONDE 22">BONDE 22</option>
                        <option value="BONDE 18">BONDE 18</option>
                        <option value="BONDE 20">BONDE 20</option>
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

        let transactions = [];
        let currentReportData = null;

        function loadTransactions() {
            const storedTransactions = localStorage.getItem('bondesSantaTeresaTransactions');
            if (storedTransactions) {
                transactions = JSON.parse(storedTransactions);
            } else {
                transactions = [
                    { data: '2025-07-01', bonde: 'BONDE 17', pagantes: 50, moradores: 10, gratPcdIdoso: 5 },
                    { data: '2025-07-02', bonde: 'BONDE 16', pagantes: 60, moradores: 15, gratPcdIdoso: 8 },
                    { data: '2025-06-15', bonde: 'BONDE 19', pagantes: 45, moradores: 12, gratPcdIdoso: 3 },
                    { data: '2025-06-01', bonde: 'BONDE 17', pagantes: 55, moradores: 8, gratPcdIdoso: 4 },
                    { data: '2025-05-10', bonde: 'BONDE 20', pagantes: 30, moradores: 5, gratPcdIdoso: 2 }
                ];
                localStorage.setItem('bondesSantaTeresaTransactions', JSON.stringify(transactions));
            }
        }

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
            let filteredTransactions = transactions;

            if (!dateValue) {
                alert('Por favor, selecione uma data, semana ou ano.');
                return;
            }
            if (reportType === 'mensal' && !monthValue) {
                alert('Por favor, selecione um mês.');
                return;
            }

            if (bonde) {
                filteredTransactions = transactions.filter(t => t.bonde === bonde);
            }

            reportTableSection.style.display = 'block';
            summarySection.style.display = 'block';
            reportTableHead.innerHTML = '';
            reportTableBody.innerHTML = '';
            summaryContent.innerHTML = '';
            exportPdfBtn.disabled = false;
            currentReportData = null;

            const bondes = ['BONDE 16', 'BONDE 17', 'BONDE 18', 'BONDE 19', 'BONDE 20', 'BONDE 22'];

            if (reportType === 'diario') {
                filteredTransactions = filteredTransactions.filter(t => t.data === dateValue);
                reportTableHead.innerHTML = `
                    <tr>
                        <th>Bonde</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuitos</th>
                        <th>Total Passageiros</th>
                    </tr>
                `;
                const reportData = bondes.map(bonde => {
                    const bondesTransactions = filteredTransactions.filter(t => t.bonde === bonde);
                    const pagantes = bondesTransactions.reduce((sum, t) => sum + t.pagantes, 0);
                    const moradores = bondesTransactions.reduce((sum, t) => sum + t.moradores, 0);
                    const gratuitos = bondesTransactions.reduce((sum, t) => sum + t.gratPcdIdoso, 0);
                    const total = pagantes + moradores + gratuitos;
                    return { bonde, pagantes, moradores, gratuitos, total };
                }).filter(row => bonde ? row.bonde === bonde : row.total > 0);

                reportData.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.bonde}</td>
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
                currentReportData = { type: 'diario', date: dateValue, data: reportData, summary: { totalPagantes, totalMoradores, totalGratuitos, totalPassageiros } };

            } else if (reportType === 'semanal') {
                const { start, end } = getWeekStartEnd(dateValue);
                filteredTransactions = filteredTransactions.filter(t => {
                    const transactionDate = new Date(t.data);
                    return transactionDate >= start && transactionDate <= end;
                });
                reportTableHead.innerHTML = `
                    <tr>
                        <th>Data</th>
                        <th>Bonde</th>
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
                        const bondesTransactions = filteredTransactions.filter(t => t.data === date && t.bonde === bonde);
                        if (bondesTransactions.length > 0 || !bonde) {
                            const pagantes = bondesTransactions.reduce((sum, t) => sum + t.pagantes, 0);
                            const moradores = bondesTransactions.reduce((sum, t) => sum + t.moradores, 0);
                            const gratuitos = bondesTransactions.reduce((sum, t) => sum + t.gratPcdIdoso, 0);
                            const total = pagantes + moradores + gratuitos;
                            if (total > 0) {
                                reportData.push({ date, bonde, pagantes, moradores, gratuitos, total });
                            }
                        }
                    });
                });

                reportData.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.date}</td>
                        <td>${row.bonde}</td>
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
                currentReportData = { type: 'semanal', date: dateValue, data: reportData, summary: { totalPagantes, totalMoradores, totalGratuitos, totalPassageiros } };

            } else if (reportType === 'mensal') {
                const year = parseInt(dateValue);
                const month = parseInt(monthValue);
                filteredTransactions = filteredTransactions.filter(t => {
                    const transactionDate = new Date(t.data);
                    return transactionDate.getFullYear() === year && transactionDate.getMonth() === month;
                });
                reportTableHead.innerHTML = `
                    <tr>
                        <th>Data</th>
                        <th>Bonde</th>
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
                        const bondesTransactions = filteredTransactions.filter(t => t.data === date && t.bonde === bonde);
                        if (bondesTransactions.length > 0 || !bonde) {
                            const pagantes = bondesTransactions.reduce((sum, t) => sum + t.pagantes, 0);
                            const moradores = bondesTransactions.reduce((sum, t) => sum + t.moradores, 0);
                            const gratuitos = bondesTransactions.reduce((sum, t) => sum + t.gratPcdIdoso, 0);
                            const total = pagantes + moradores + gratuitos;
                            if (total > 0) {
                                reportData.push({ date, bonde, pagantes, moradores, gratuitos, total });
                            }
                        }
                    });
                });

                reportData.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.date}</td>
                        <td>${row.bonde}</td>
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
                    <div class="summary-item"><span>Média Diária</span><span>${mediaDiaria}</span></div>
                `;
                currentReportData = { type: 'mensal', date: `${year}-${String(month + 1).padStart(2, '0')}`, data: reportData, summary: { totalPagantes, totalMoradores, totalGratuitos, totalPassageiros, mediaDiaria } };

            } else if (reportType === 'anual') {
                const year = parseInt(dateValue);
                filteredTransactions = filteredTransactions.filter(t => new Date(t.data).getFullYear() === year);
                reportTableHead.innerHTML = `
                    <tr>
                        <th>Bonde</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuitos</th>
                        <th>Total Passageiros</th>
                        <th>Média Mensal Passageiros</th>
                    </tr>
                `;
                const reportData = bondes.map(bonde => {
                    const bondesTransactions = filteredTransactions.filter(t => t.bonde === bonde);
                    const pagantes = bondesTransactions.reduce((sum, t) => sum + t.pagantes, 0);
                    const moradores = bondesTransactions.reduce((sum, t) => sum + t.moradores, 0);
                    const gratuitos = bondesTransactions.reduce((sum, t) => sum + t.gratPcdIdoso, 0);
                    const total = pagantes + moradores + gratuitos;
                    const monthsWithData = new Set(bondesTransactions.map(t => new Date(t.data).getMonth())).size;
                    const mediaMensal = monthsWithData > 0 ? Math.round(total / monthsWithData) : 0;
                    return { bonde, pagantes, moradores, gratuitos, total, mediaMensal };
                }).filter(row => bonde ? row.bonde === bonde : row.total > 0);

                reportData.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.bonde}</td>
                        <td>${row.pagantes}</td>
                        <td>${row.moradores}</td>
                        <td>${row.gratuitos}</td>
                        <td>${row.total}</td>
                        <td>${row.mediaMensal}</td>
                    `;
                    reportTableBody.appendChild(tr);
                });

                const totalPagantes = reportData.reduce((sum, row) => sum + row.pagantes, 0);
                const totalMoradores = reportData.reduce((sum, row) => sum + row.moradores, 0);
                const totalGratuitos = reportData.reduce((sum, row) => sum + row.gratuitos, 0);
                const totalPassageiros = totalPagantes + totalMoradores + totalGratuitos;
                const monthsWithData = new Set(filteredTransactions.map(t => new Date(t.data).getMonth())).size;
                const mediaMensalTotal = monthsWithData > 0 ? Math.round(totalPassageiros / monthsWithData) : 0;

                summaryContent.innerHTML = `
                    <div class="summary-item"><span>Total Pagantes</span><span>${totalPagantes}</span></div>
                    <div class="summary-item"><span>Total Moradores</span><span>${totalMoradores}</span></div>
                    <div class="summary-item"><span>Total Gratuitos</span><span>${totalGratuitos}</span></div>
                    <div class="summary-item"><span>Total Passageiros</span><span>${totalPassageiros}</span></div>
                    <div class="summary-item"><span>Média Mensal Total</span><span>${mediaMensalTotal}</span></div>
                `;
                currentReportData = { type: 'anual', date: dateValue, data: reportData, summary: { totalPagantes, totalMoradores, totalGratuitos, totalPassageiros, mediaMensalTotal } };
            }

            if (reportTableBody.children.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="${reportTableHead.children[0].children.length}" style="text-align: center; color: #e74c3c;">Nenhum dado encontrado para o período selecionado.</td>`;
                reportTableBody.appendChild(tr);
                summarySection.style.display = 'none';
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

            let finalY = doc.lastAutoTable.finalY + 10;
            doc.setFontSize(12);
            doc.text('Resumo do Relatório', 10, finalY);
            finalY += 10;

            Object.entries(currentReportData.summary).forEach(([key, value]) => {
                const label = key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
                doc.text(`${label}: ${value}`, 10, finalY);
                finalY += 10;
            });

            doc.save(`relatorio_${reportType}_${dateValue}.pdf`);
        }

        reportTypeInput.addEventListener('change', updateDateInput);

        generateReportBtn.addEventListener('click', generateReport);

        exportPdfBtn.addEventListener('click', exportToPDF);

        document.addEventListener('DOMContentLoaded', () => {
            loadTransactions();
            updateDateInput();
        });
    </script>
</body>
</html>
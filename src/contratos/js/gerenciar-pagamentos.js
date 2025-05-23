// Contador global para índices únicos
let editableRowCounter = 0;

// Cache de dados
let currentContractData = null;
let currentPayments = [];
let currentContractDetails = null;

// Função utilitária para sanitizar strings para JSON
function sanitizeForJson(str) {
  if (typeof str !== "string") return str;
  return str
    .replace(/[\x00-\x1F\x7F-\x9F]/g, "")
    .replace(/[\u{1F000}-\u{1FFFF}]/gu, "")
    .replace(/\n/g, " ")
    .trim();
}

// Função para validar JSON
function isValidJson(data) {
  try {
    JSON.stringify(data);
    return true;
  } catch (e) {
    console.error("Erro ao validar JSON:", e.message);
    return false;
  }
}

// Função para calcular a diferença em meses entre duas datas
function getMonthDifference(startDate, endDate) {
  const start = new Date(startDate);
  const end = new Date(endDate);
  return (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth()) + 1;
}

// Função para gerar botões de ação
function generateActionButtons(paymentId, isSubRow, isEditable, rowIndex) {
  if (isEditable) {
    return `
      <button class="btn btn-primary btn-sm btn-action" onclick="saveSinglePayment(${rowIndex}, this)">
        <i class="bi bi-save"></i>
      </button>
    `;
  }
  return `
    <button class="btn btn-warning btn-sm btn-action" onclick="editPayment(${paymentId}, this)" title="Editar">
      <i class="bi bi-pencil"></i>
    </button>
    <button class="btn btn-danger btn-sm btn-action" onclick="deletePayment(${paymentId}, this)" title="Excluir">
      <i class="bi bi-trash"></i>
    </button>
    ${!isSubRow ? `
      <button class="btn btn-success btn-sm btn-action" onclick="addSource(${paymentId}, this)" title="Adicionar Fonte">
        <i class="bi bi-plus-circle"></i>
      </button>
    ` : ""}
  `;
}

// Função para renderizar conteúdo de uma célula
function renderCellContent(payment, isEditable = false, rowIndex = null) {
  const isSubRow = !!payment.fonte_adicional;
  const fields = [
    { key: "mes", type: "text", readonly: true },
    { key: "empenho", type: "text" },
    { key: "tipo", type: "text" },
    { key: "nota_empenho", type: "text" },
    { key: "valor_contrato", type: "number", readonly: true },
    { key: "creditos_ativos", type: "text" },
    { key: "fonte", type: "text" },
    { key: "SEI", type: "text" },
    { key: "nota_fiscal", type: "text" },
    { key: "envio_pagamento", type: "text" },
    { key: "vencimento_fatura", type: "date" },
    { key: "valor_liquidado", type: "number" },
    { key: "valor_liquidado_ag", type: "number" },
    { key: "ordem_bancaria", type: "text" },
    { key: "data_atualizacao", type: "date" },
    { key: "data_pagamento", type: "date" },
  ];

  const cells = fields.map((field) => {
    const value = payment[field.key] || (field.type === "number" ? 0 : "");
    if (isEditable) {
      const readonly = field.readonly ? "readonly" : "";
      const inputType = field.type === "number" ? 'type="number" step="0.01"' : `type="${field.type}"`;
      return `<td><input ${inputType} value="${sanitizeForJson(value)}" class="form-control form-control-sm" data-key="${field.key}" ${readonly}></td>`;
    }
    if (field.key === "fonte" && isSubRow) {
      return `<td><span class="fonte-badge">${payment.fonte || ""} (Fonte ${payment.fonte_adicional})</span></td>`;
    }
    return `<td>${value}</td>`;
  });

  // Adicionar célula de ações
  cells.push(`<td>${generateActionButtons(payment.id, isSubRow, isEditable, rowIndex)}${isEditable && isSubRow ? `<input type="hidden" value="${sanitizeForJson(payment.fonte_adicional)}" data-key="fonte_adicional">` : ""}</td>`);

  return cells.join("");
}

// Função para criar uma linha da tabela
function createTableRow(payment, isEditable = false, rowIndex = null) {
  const isSubRow = !!payment.fonte_adicional;
  const tr = document.createElement("tr");
  tr.classList.add(isEditable ? "editable" : "custom-read-only");
  if (isSubRow) tr.classList.add("custom-sub-row");
  if (payment.id) tr.dataset.paymentId = payment.id;
  tr.dataset.paymentData = JSON.stringify(payment);
  if (isEditable) tr.dataset.rowIndex = rowIndex;
  tr.innerHTML = renderCellContent(payment, isEditable, rowIndex);
  return tr;
}

// Função para calcular o total de valor_liquidado por ano
function calculateYearTotal(year, payments, includeAdditionalSources = false) {
  return payments.reduce((total, payment) => {
    const paymentYear = payment.mes ? payment.mes.split("/")[1] : "";
    if (paymentYear === year && (includeAdditionalSources || !payment.fonte_adicional)) {
      return total + Number.parseFloat(payment.valor_liquidado || 0);
    }
    return total;
  }, 0);
}

// Função para atualizar o total de um ano na interface
function updateYearTotal(year, includeAdditionalSources = false) {
  const yearTbody = document.querySelector(`.year-tbody[data-year="${year}"]`);
  if (!yearTbody) return;

  const totalValorLiquidado = calculateYearTotal(year, currentPayments, includeAdditionalSources);
  const yearDetails = yearTbody.closest(".custom-year-details");
  if (yearDetails) {
    const totalElement = yearDetails.querySelector(".year-total p");
    if (totalElement) {
      totalElement.innerHTML = `<strong>Total Valor Liquidado (${year}):</strong> R$ ${totalValorLiquidado.toFixed(2)}`;
    }
  }
}

// Função para exibir o resumo do processo
async function showResumoProcesso(rowData) {
  const contractData = typeof rowData === "string" ? JSON.parse(rowData) : rowData;
  console.log("Abrindo resumo para contrato:", contractData);
  currentContractData = contractData;
  await loadContractsAndPayments(contractData);
}

// Função para carregar contratos e pagamentos
async function loadContractsAndPayments(contractData) {
  const tbody = document.getElementById("contratosTableBody");
  tbody.innerHTML = "";
  editableRowCounter = 0;

  const contractTitleHeader = document.getElementById("contractTitleHeader");
  const titulo = contractData.titulo || "Desconhecido";
  const sei = contractData.SEI || "N/A";
  const agencia_bancaria = contractData.agencia_bancaria || "N/A";
  const seiLink = sei !== "N/A"
    ? `<a href="https://sei.rj.gov.br/sei/controlador_externo.php?acao=procedimento_trabalhar&acao_origem=procedimento_pesquisar&id_procedimento=${encodeURIComponent(sei)}" target="_blank" rel="noopener noreferrer" title="Link de acesso direto ao processo SEI" class="sei-link">SEI: ${sei}</a>`
    : "SEI: N/A";

  contractTitleHeader.innerHTML = `
    <i class="fa fa-credit-card"></i>
    <span>PARCELAS DO CONTRATO /</span>
    <span style="color: #0056b3;">${titulo}</span> /
    <span>(${seiLink})</span>
    Conta Bancária /<span class="account-number">${agencia_bancaria}</span>
  `;

  try {
    const contractResponse = await fetch(`./get_contract_details.php?titulo=${encodeURIComponent(contractData.titulo)}`);
    if (!contractResponse.ok) throw new Error("Erro ao carregar detalhes do contrato");
    const contractDetails = await contractResponse.json();
    currentContractDetails = contractDetails;

    let numParcelas = contractDetails.num_parcelas || 1;
    const dataInicio = contractDetails.data_cadastro ? new Date(contractDetails.data_cadastro) : new Date();
    const dataValidade = contractDetails.validade ? new Date(contractDetails.validade) : null;
    const valorContrato = contractDetails.valor_contrato || contractData.valor_contrato || 0;
    const situacao = contractData.situacao || "ativo";

    const paymentResponse = await fetch(`./get_payment.php?contrato_titulo=${encodeURIComponent(contractData.titulo)}`);
    if (!paymentResponse.ok) throw new Error("Erro ao carregar pagamentos");
    currentPayments = await paymentResponse.json();
    console.log("Pagamentos carregados:", currentPayments);

    const paymentsByYear = {};
    const mesesPagos = currentPayments.filter((p) => !p.fonte_adicional).map((p) => p.mes);
    currentPayments.forEach((payment) => {
      const year = payment.mes ? payment.mes.split("/")[1] : "Sem Ano";
      paymentsByYear[year] = paymentsByYear[year] || [];
      paymentsByYear[year].push(payment);
    });

    if (situacao.toLowerCase() === "renovado" && dataValidade && !isNaN(dataValidade) && dataValidade >= dataInicio) {
      numParcelas = getMonthDifference(dataInicio, dataValidade);
      console.log(`Contrato renovado: Novo número de parcelas (${numParcelas}) com base na validade (${dataValidade.toISOString().split("T")[0]})`);
    } else if (situacao.toLowerCase() === "renovado") {
      console.warn("Data de validade inválida. Usando num_parcelas original:", numParcelas);
      alert("Aviso: Data de validade do contrato renovado inválida. Usando número de parcelas original.");
    }

    const mesesParcelas = [];
    for (let i = 0; i < numParcelas; i++) {
      const dataParcela = new Date(dataInicio);
      dataParcela.setMonth(dataInicio.getMonth() + i);
      const mesFormatado = `${String(dataParcela.getMonth() + 1).padStart(2, "0")}/${dataParcela.getFullYear()}`;
      if (!mesesPagos.includes(mesFormatado)) {
        mesesParcelas.push(mesFormatado);
      }
    }

    const todasParcelasPagas = mesesParcelas.length === 0;
    const isEncerrado = situacao.toLowerCase() === "encerrado" || todasParcelasPagas;

    const parcelasByYear = {};
    mesesParcelas.forEach((mes) => {
      const year = mes.split("/")[1];
      parcelasByYear[year] = parcelasByYear[year] || [];
      parcelasByYear[year].push(mes);
    });

    const currentYear = new Date().getFullYear().toString();
    showTab("gerenciar");

    const years = [...new Set([...Object.keys(paymentsByYear), ...Object.keys(parcelasByYear)])].sort((a, b) => b - a);
    years.forEach((year) => {
      const details = document.createElement("details");
      details.classList.add("custom-year-details");
      if (year === currentYear) details.setAttribute("open", "");

      const summary = document.createElement("summary");
      summary.innerHTML = `<i class="fas fa-calendar-alt"></i> Ano ${year}`;
      details.appendChild(summary);

      const tableContainer = document.createElement("div");
      tableContainer.classList.add("table-container");

      const table = document.createElement("table");
      table.classList.add("custom-table");
      table.innerHTML = `
        <thead>
          <tr>
            <th><i class="fas fa-calendar-month"></i> Mês da Fatura</th>
            <th><i class="fas fa-file-invoice"></i> Empenho</th>
            <th><i class="fas fa-tag"></i> Tipo</th>
            <th><i class="fas fa-file-alt"></i> Nota de Empenho</th>
            <th><i class="fas fa-dollar-sign"></i> Valor Contrato</th>
            <th><i class="fas fa-cogs"></i> Créditos Ativos</th>
            <th><i class="fas fa-industry"></i> Fonte</th>
            <th><i class="fas fa-file-signature"></i> Numero do SEI</th>
            <th><i class="fas fa-file"></i> Nota Fiscal</th>
            <th><i class="fas fa-paper-plane"></i> Envio Pagamento</th>
            <th><i class="fas fa-calendar-day"></i> Vencimento Fatura</th>
            <th><i class="fas fa-check-circle"></i> Valor Liquidado</th>
            <th><i class="fas fa-check-circle"></i> Valor Liquidado AG</th>
            <th><i class="fas fa-credit-card"></i> Ordem Bancária</th>
            <th><i class="fas fa-sync-alt"></i> Data Atualização</th>
            <th><i class="fas fa-calendar-check"></i> Data Pagamento</th>
            <th><i class="fas fa-cogs"></i> Ações</th>
          </tr>
        </thead>
        <tbody class="year-tbody" data-year="${year}"></tbody>
      `;

      const yearTbody = table.querySelector("tbody");
      tableContainer.appendChild(table);
      details.appendChild(tableContainer);

      if (paymentsByYear[year]) {
        paymentsByYear[year].forEach((payment) => {
          yearTbody.appendChild(createTableRow(payment));
        });
      }

      if (parcelasByYear[year] && !isEncerrado) {
        const valorParcela = valorContrato / numParcelas;
        parcelasByYear[year].forEach((mes) => {
          const payment = {
            mes,
            empenho: contractData.empenho || "",
            tipo: contractData.tipo || "",
            nota_empenho: contractData.nota_empenho || "",
            valor_contrato: valorParcela.toFixed(2),
            creditos_ativos: contractData.creditos_ativos || "",
            fonte: contractData.fonte || "",
            SEI: contractData.SEI || "",
            nota_fiscal: contractData.nota_fiscal || "",
            envio_pagamento: contractData.envio_pagamento || "",
            vencimento_fatura: contractData.validade || "",
            valor_liquidado: contractData.valor_liquidado || 0,
            valor_liquidado_ag: contractData.valor_liquidado_ag || 0,
            ordem_bancaria: contractData.ordem_bancaria || "",
            data_atualizacao: contractData.data_atualizacao || "",
            data_pagamento: new Date().toISOString().split("T")[0],
          };
          yearTbody.appendChild(createTableRow(payment, true, editableRowCounter++));
        });
      } else if (parcelasByYear[year] && isEncerrado) {
        const valorParcela = valorContrato / numParcelas;
        parcelasByYear[year].forEach((mes) => {
          const payment = {
            mes,
            empenho: contractData.empenho || "",
            tipo: contractData.tipo || "",
            nota_empenho: contractData.nota_empenho || "",
            valor_contrato: valorParcela.toFixed(2),
            creditos_ativos: contractData.creditos_ativos || "",
            fonte: contractData.fonte || "",
            SEI: contractData.SEI || "",
            nota_fiscal: contractData.nota_fiscal || "",
            envio_pagamento: contractData.envio_pagamento || "",
            vencimento_fatura: contractData.validade || "",
            valor_liquidado: contractData.valor_liquidado || 0,
            valor_liquidado_ag: contractData.valor_liquidado_ag || 0,
            ordem_bancaria: contractData.ordem_bancaria || "",
            data_atualizacao: contractData.data_atualizacao || "",
            data_pagamento: new Date().toISOString().split("T")[0],
          };
          yearTbody.appendChild(createTableRow(payment));
        });
      }

      const totalDiv = document.createElement("div");
      totalDiv.classList.add("year-total");
      const paymentsData = encodeURIComponent(JSON.stringify(paymentsByYear[year] || []));
      totalDiv.innerHTML = `
        <p><strong>Total Valor Liquidado (${year}):</strong> R$ ${calculateYearTotal(year, currentPayments, true).toFixed(2)}</p>
        <button class="prestacao" onclick="showTab('prestacao'); generateAccountabilityReport('${year}', '${paymentsData}', true)">
          <i class="bi bi-file-earmark-text"></i> Prestação de Contas
        </button>
      `;
      details.appendChild(totalDiv);
      tbody.appendChild(details);
    });

    tbody.dataset.contractTitle = contractData.titulo;
    initializeHorizontalScroll();
  } catch (error) {
    console.error("Erro ao carregar dados:", error);
    alert("Erro ao carregar dados: " + error.message);
  }
}

// Função para adicionar uma nova linha na tabela
function addTableRow(payment) {
  const year = payment.mes ? payment.mes.split("/")[1] : "Sem Ano";
  const yearTbody = document.querySelector(`.year-tbody[data-year="${year}"]`);
  if (!yearTbody) {
    console.error("Não foi possível encontrar o tbody para o ano:", year);
    return false;
  }

  const tr = createTableRow(payment);
  const isSubRow = !!payment.fonte_adicional;

  if (isSubRow) {
    const rowsWithSameMonth = Array.from(yearTbody.querySelectorAll("tr")).filter((row) => {
      try {
        const data = JSON.parse(row.dataset.paymentData || "{}");
        return data.mes === payment.mes;
      } catch {
        return false;
      }
    });

    let lastRow = null;
    for (const row of rowsWithSameMonth) {
      const rowData = JSON.parse(row.dataset.paymentData || "{}");
      if (
        !rowData.fonte_adicional ||
        (rowData.fonte_adicional && payment.fonte_adicional && rowData.fonte_adicional < payment.fonte_adicional)
      ) {
        lastRow = row;
      }
    }

    if (lastRow) {
      lastRow.insertAdjacentElement("afterend", tr);
    } else {
      yearTbody.appendChild(tr);
    }
  } else {
    const allRows = Array.from(yearTbody.querySelectorAll("tr:not(.custom-sub-row)"));
    const [paymentMonth, paymentYear] = payment.mes.split("/");
    const paymentDate = new Date(paymentYear, Number.parseInt(paymentMonth) - 1);
    let insertAfter = null;

    for (const row of allRows) {
      try {
        const rowData = JSON.parse(row.dataset.paymentData || "{}");
        if (rowData.mes) {
          const [rowMonth, rowYear] = rowData.mes.split("/");
          const rowDate = new Date(rowYear, Number.parseInt(rowMonth) - 1);
          if (rowDate <= paymentDate) {
            insertAfter = row;
          } else {
            break;
          }
        }
      } catch (e) {
        console.error("Erro ao comparar datas:", e);
      }
    }

    if (insertAfter) {
      let lastSubRow = insertAfter;
      let nextRow = insertAfter.nextElementSibling;
      while (nextRow && nextRow.classList.contains("custom-sub-row")) {
        lastSubRow = nextRow;
        nextRow = nextRow.nextElementSibling;
      }
      lastSubRow.insertAdjacentElement("afterend", tr);
    } else if (allRows.length > 0) {
      yearTbody.insertBefore(tr, yearTbody.firstChild);
    } else {
      yearTbody.appendChild(tr);
    }
  }

  updateYearTotal(year, true);
  return true;
}

// Função para salvar um pagamento
async function saveSinglePayment(rowIndex, button) {
  const contractTitle = document.getElementById("contratosTableBody").dataset.contractTitle;
  const row = document.querySelector(`#contratosTableBody .editable[data-row-index="${rowIndex}"]`);
  if (!row) {
    alert("Linha não encontrada.");
    return;
  }

  button.disabled = true;
  const originalContent = button.innerHTML;
  button.innerHTML = '<i class="bi bi-hourglass-split"></i>';

  const paymentData = { contrato_titulo: sanitizeForJson(contractTitle) };
  const inputs = row.querySelectorAll("input");
  const columns = [
    "mes",
    "empenho",
    "tipo",
    "nota_empenho",
    "valor_contrato",
    "creditos_ativos",
    "fonte",
    "SEI",
    "nota_fiscal",
    "envio_pagamento",
    "vencimento_fatura",
    "valor_liquidado",
    "valor_liquidado_ag",
    "ordem_bancaria",
    "data_atualizacao",
    "data_pagamento",
    "fonte_adicional",
  ];

  inputs.forEach((input) => {
    const key = input.getAttribute("data-key");
    if (columns.includes(key)) {
      if (input.type === "number") {
        paymentData[key] = Number.parseFloat(input.value) || 0;
      } else if (input.type === "date") {
        paymentData[key] = input.value || null;
      } else {
        paymentData[key] = sanitizeForJson(input.value) || null;
      }
    }
  });

  if (!paymentData.mes) {
    alert("O campo Mês é obrigatório.");
    button.disabled = false;
    button.innerHTML = originalContent;
    return;
  }
  if (!paymentData.empenho) {
    alert("O campo Empenho é obrigatório.");
    button.disabled = false;
    button.innerHTML = originalContent;
    return;
  }

  const paymentId = row.dataset.paymentId;
  if (paymentId && !row.classList.contains("sub-row")) {
    paymentData.id = Number.parseInt(paymentId);
    console.log("Salvando edição para paymentId:", paymentId, "Dados:", paymentData);
  } else {
    console.log("Salvando novo pagamento:", paymentData);
  }

  if (!isValidJson(paymentData)) {
    alert("Erro: Dados inválidos para envio.");
    console.error("Dados inválidos:", paymentData);
    button.disabled = false;
    button.innerHTML = originalContent;
    return;
  }

  try {
    const response = await fetch("./save_payment.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(paymentData),
    });

    if (!response.ok) {
      const text = await response.text();
      console.error("Erro HTTP:", response.status, text);
      throw new Error(`Erro HTTP: ${response.status}. Resposta: ${text.substring(0, 200)}`);
    }

    const data = await response.json();
    if (data.success) {
      alert("Pagamento salvo com sucesso!");
      const payment = data.payment || { ...paymentData, id: data.id || paymentId || Date.now() };

      if (paymentId) {
        const index = currentPayments.findIndex((p) => p.id == paymentId);
        if (index !== -1) {
          currentPayments[index] = payment;
        }
        row.innerHTML = renderCellContent(payment);
        row.classList.remove("editable", "sub-row");
        row.classList.add("custom-read-only");
        if (payment.fonte_adicional) row.classList.add("custom-sub-row");
        delete row.dataset.rowIndex;
        row.dataset.paymentData = JSON.stringify(payment);
      } else {
        currentPayments.push(payment);
        row.remove();
        addTableRow(payment);
      }

      updateYearTotal(payment.mes.split("/")[1], true);
      initializeHorizontalScroll();
    } else {
      console.error("Erro retornado pelo servidor:", data.message);
      alert("Erro ao salvar pagamento: " + data.message);
      button.disabled = false;
      button.innerHTML = originalContent;
    }
  } catch (error) {
    console.error("Erro ao salvar pagamento:", error);
    alert("Erro ao salvar pagamento: " + error.message);
    button.disabled = false;
    button.innerHTML = originalContent;
  }
}

// Função para editar um pagamento
function editPayment(paymentId, button) {
  console.log("Iniciando edição para paymentId:", paymentId);
  const row = document.querySelector(`tr[data-payment-id="${paymentId}"]`);
  if (!row) {
    alert("Linha não encontrada para edição.");
    return;
  }

  let payment;
  try {
    payment = JSON.parse(row.dataset.paymentData || "{}");
  } catch (e) {
    console.error("Erro ao parsear paymentData:", e);
    alert("Erro ao carregar dados do pagamento.");
    return;
  }

  row.classList.remove("custom-read-only");
  row.classList.add("editable");
  if (payment.fonte_adicional) row.classList.add("sub-row");
  row.dataset.rowIndex = editableRowCounter;
  row.innerHTML = renderCellContent(payment, true, editableRowCounter++);
}

// Função para adicionar uma nova fonte
function addSource(paymentId, button) {
  console.log("Iniciando addSource para paymentId:", paymentId);
  const row = document.querySelector(`tr[data-payment-id="${paymentId}"]`);
  if (!row) {
    alert("Linha não encontrada para adicionar fonte.");
    return;
  }

  let payment;
  try {
    payment = JSON.parse(row.dataset.paymentData || "{}");
  } catch (e) {
    console.error("Erro ao parsear paymentData:", e);
    alert("Erro ao carregar dados do pagamento.");
    return;
  }

  const existingSources = currentPayments.filter((p) => p.mes === payment.mes && p.fonte_adicional).length;
  const fonteNumber = existingSources + 1;
  const newSource = {
    ...payment,
    fonte: `Fonte ${fonteNumber}`,
    fonte_adicional: `Fonte ${fonteNumber}`,
    id: null,
  };

  const tr = createTableRow(newSource, true, editableRowCounter++);
  row.insertAdjacentElement("afterend", tr);
}

// Função para excluir um pagamento
async function deletePayment(id, button) {
  console.log("Iniciando exclusão para paymentId:", id);
  if (!id) {
    alert("ID do pagamento não fornecido");
    return;
  }

  if (!confirm("Tem certeza que deseja excluir este pagamento?")) {
    return;
  }

  button.disabled = true;
  const originalContent = button.innerHTML;
  button.innerHTML = '<i class="bi bi-hourglass-split"></i>';

  try {
    const response = await fetch(`./delete_payment.php?id=${encodeURIComponent(id)}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    });

    const data = await response.json();
    if (data.success) {
      alert("Pagamento excluído com sucesso!");
      const index = currentPayments.findIndex((p) => p.id == id);
      let year = "";
      if (index !== -1) {
        year = currentPayments[index].mes ? currentPayments[index].mes.split("/")[1] : "";
        currentPayments.splice(index, 1);
      }

      const row = document.querySelector(`tr[data-payment-id="${id}"]`);
      if (row) row.remove();
      if (year) updateYearTotal(year, true);
      initializeHorizontalScroll();
    } else {
      alert("Erro ao excluir pagamento: " + (data.message || "Erro desconhecido"));
      button.disabled = false;
      button.innerHTML = originalContent;
    }
  } catch (error) {
    console.error("Erro ao excluir pagamento:", error);
    alert("Erro ao excluir pagamento: " + error.message);
    button.disabled = false;
    button.innerHTML = originalContent;
  }
}

// Função para gerar relatório de prestação de contas
function generateAccountabilityReport(year, encodedPayments, appendToPrestacao = false) {
  const payments = JSON.parse(decodeURIComponent(encodedPayments));
  const reportContainer = document.createElement("div");
  reportContainer.classList.add("report-container");
  reportContainer.innerHTML = `
    <h3>Prestação de Contas - Ano ${year}</h3>
    <p><strong>Contrato:</strong> ${document.getElementById("contratosTableBody").dataset.contractTitle}</p>
    <p><strong>Data do Relatório:</strong> ${new Date().toLocaleDateString("pt-BR")}</p>
    <table class="report-table">
      <thead>
        <tr>
          <th>Mês</th>
          <th>Empenho</th>
          <th>Fonte</th>
          <th>Valor Liquidado</th>
          <th>Data Pagamento</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  `;

  const reportTbody = reportContainer.querySelector("tbody");
  const totalLiquidado = calculateYearTotal(year, payments, true);
  payments.forEach((payment) => {
    const isSubRow = !!payment.fonte_adicional;
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${payment.mes || ""}</td>
      <td>${payment.empenho || ""}</td>
      <td>${payment.fonte || ""}${isSubRow ? ` (Fonte ${payment.fonte_adicional})` : ""}</td>
      <td>R$ ${(Number.parseFloat(payment.valor_liquidado || 0)).toFixed(2)}</td>
      <td>${payment.data_pagamento || ""}</td>
    `;
    reportTbody.appendChild(tr);
  });

  const totalRow = document.createElement("tr");
  totalRow.innerHTML = `
    <td colspan="3"><strong>Total</strong></td>
    <td><strong>R$ ${totalLiquidado.toFixed(2)}</strong></td>
    <td></td>
  `;
  reportTbody.appendChild(totalRow);

  if (appendToPrestacao) {
    const prestacaoContent = document.getElementById("prestacao");
    prestacaoContent.innerHTML = "";
    prestacaoContent.appendChild(reportContainer);
  } else {
    const container = document.getElementById("gerenciar");
    const existingReport = container.querySelector(".report-container");
    if (existingReport) existingReport.remove();
    container.appendChild(reportContainer);
  }

  updateYearTotal(year, true);
}

// Função para mostrar uma aba
function showTab(tabId) {
  const tabs = document.querySelectorAll(".tab");
  const contents = document.querySelectorAll(".form-container");
  tabs.forEach((tab) => tab.classList.remove("active"));
  contents.forEach((content) => (content.style.display = "none"));
  document.querySelector(`.tab[data-tab="${tabId}"]`).classList.add("active");
  document.getElementById(tabId).style.display = "block";
}

// Função para inicializar rolagem horizontal
function initializeHorizontalScroll() {
  const scrollableElements = [
    document.getElementById("contratosTable"),
    document.getElementById("contratosTableBody"),
    ...document.querySelectorAll(".table-container"),
  ];

  scrollableElements.forEach((element) => {
    if (!element) return;
    if (element.scrollWidth > element.clientWidth) {
      element.classList.add("has-horizontal-scroll");
      if (!element.querySelector(".scroll-indicator")) {
        const indicator = document.createElement("div");
        indicator.className = "scroll-indicator";
        indicator.textContent = "Deslize →";
        element.appendChild(indicator);
      }
    } else {
      element.classList.remove("has-horizontal-scroll");
    }
  });
}
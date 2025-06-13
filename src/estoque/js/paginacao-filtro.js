/* Variáveis globais */
let paginaAtual = 1;
const itensPorPagina = 7;

/* Função para carregar os dados da tabela com filtro e paginação */
function carregarTabela(pagina, filtro = "") {
  fetch(
    `paginasTabelaestoque.php?pagina=${pagina}&itensPorPagina=${itensPorPagina}&filtro=${encodeURIComponent(filtro)}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data && data.dados && typeof data.total_paginas === 'number') {
        preencherTabela(data.dados);
        criarPaginacao(data.total_paginas);
      } else {
        console.error("Estrutura de dados inesperada:", data);
        preencherTabela([]); // Exibir mensagem de "nenhum produto encontrado"
      }
    })
    .catch((error) => {
      console.error("Erro ao carregar dados:", error);
      preencherTabela([]); // Exibir mensagem de erro
    });
}

/* Função para preencher a tabela com os dados */
function preencherTabela(dados) {
  const tbody = document.getElementById("tabelaProdutos");
  tbody.innerHTML = ""; // Limpar a tabela

  if (dados.length === 0) {
    const row = document.createElement("tr");
    row.classList.add("no-data");
    row.innerHTML = `
      <td colspan="10" class="text-center">
        <i class="fas fa-exclamation-circle"></i>
        <span>Nenhum produto encontrado.</span>
      </td>
    `;
    tbody.appendChild(row);
    return;
  }

  dados.forEach((dado) => {
    const descricao = dado.descricao || "Descrição não encontrada";
    const row = document.createElement("tr");
    row.innerHTML = `
      <td><span class="id-badge">${dado.id}</span></td>
      <td class="font-semibold">${dado.produto}</td>
      <td><span class="tag">${dado.classificacao}</span></td>
      <td>${descricao}</td>
      <td><span class="nature-badge">${dado.natureza}</span></td>
      <td><span class="quantity-badge ${getStockClass(dado.quantidade)}">${dado.quantidade}</span></td>
      <td><span class="location-badge">${dado.localizacao}</span></td>
      <td><span class="currency">${dado.custo}</span></td>
      <td><span class="currency">${dado.preco_medio}</span></td>
      <td class="action-buttons">
        <button class="btn-details btn-action" onclick="abrirModalDetalhes('${dado.id}')">
          <i class="fas fa-info-circle"></i>
        </button>
        <button class="btn-edit btn-action" onclick="abrirModalAtualizar('${dado.id}')">
          <i class="fas fa-edit"></i>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

/* Função auxiliar para determinar a classe do badge de quantidade */
function getStockClass(quantidade) {
  const qtd = parseInt(quantidade, 10);
  if (isNaN(qtd)) return "low-stock";
  if (qtd > 50) return "good-stock";
  if (qtd > 10) return "medium-stock";
  return "low-stock";
}

/* Função para criar a paginação com botões "<<" e ">>" */
function criarPaginacao(totalPaginas) {
  const paginacaoContainer = document.querySelector(".pagination");
  paginacaoContainer.innerHTML = ""; // Limpar os botões de paginação

  const maxBotoes = 5;
  let inicio = Math.max(1, paginaAtual - Math.floor(maxBotoes / 2));
  let fim = Math.min(totalPaginas, inicio + maxBotoes - 1);

  if (fim - inicio + 1 < maxBotoes) {
    inicio = Math.max(1, fim - maxBotoes + 1);
  }

  // Botão "Primeira Página"
  if (inicio > 1) {
    const primeiro = document.createElement("button");
    primeiro.textContent = "<<";
    primeiro.classList.add("pagination-btn");
    primeiro.addEventListener("click", () => {
      paginaAtual = 1;
      carregarTabela(paginaAtual, document.getElementById("filtroProduto").value);
    });
    paginacaoContainer.appendChild(primeiro);
  }

  // Botões de página
  for (let i = inicio; i <= fim; i++) {
    const button = document.createElement("button");
    button.textContent = i;
    button.classList.add("pagination-btn");
    if (i === paginaAtual) button.classList.add("active");
    button.addEventListener("click", () => {
      paginaAtual = i;
      carregarTabela(paginaAtual, document.getElementById("filtroProduto").value);
    });
    paginacaoContainer.appendChild(button);
  }

  // Botão "Última Página"
  if (fim < totalPaginas) {
    const ultimo = document.createElement("button");
    ultimo.textContent = ">>";
    ultimo.classList.add("pagination-btn");
    ultimo.addEventListener("click", () => {
      paginaAtual = totalPaginas;
      carregarTabela(paginaAtual, document.getElementById("filtroProduto").value);
    });
    paginacaoContainer.appendChild(ultimo);
  }
}

/* Função para filtrar a tabela com base no input */
function aplicarFiltro() {
  const filtro = document.getElementById("filtroProduto").value.trim();
  paginaAtual = 1; // Resetar para a primeira página
  carregarTabela(paginaAtual, filtro);
}

/* Função para limpar o filtro */
function limparFiltro() {
  document.getElementById("filtroProduto").value = "";
  aplicarFiltro();
}

/* Inicialização */
function inicializar() {
  // Carregar tabela inicial
  carregarTabela(paginaAtual);

  // Adicionar eventos aos botões
  const btnFiltrar = document.getElementById("filtrar");
  const btnLimpar = document.getElementById("limpar");

  if (btnFiltrar) {
    btnFiltrar.addEventListener("click", aplicarFiltro);
  } else {
    console.warn("Botão 'filtrar' não encontrado.");
  }

  if (btnLimpar) {
    btnLimpar.addEventListener("click", limparFiltro);
  } else {
    console.warn("Botão 'limpar' não encontrado.");
  }
}

/* Executar inicialização após o carregamento da página */
window.addEventListener("load", inicializar);
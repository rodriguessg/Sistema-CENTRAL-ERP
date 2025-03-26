// Variáveis globais
let paginaAtual = 1;
const itensPorPagina = 7;

// Função para carregar os dados da tabela com filtro e paginação
function carregarTabela(pagina, filtro = "") {
  fetch(
    `paginasTabelaestoque.php?pagina=${pagina}&itensPorPagina=${itensPorPagina}&filtro=${filtro}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data && data.dados) {
        preencherTabela(data.dados);
        criarPaginacao(data.total_paginas);
      } else {
        console.error("Estrutura de dados inesperada:", data);
      }
    })
    .catch((error) => console.error("Erro ao carregar dados:", error));
}

// Função para preencher a tabela com os dados
function preencherTabela(dados) {
  const tbody = document.getElementById("tabelaProdutos");
  tbody.innerHTML = ""; // Limpar a tabela

  if (dados.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `<td colspan="8">Nenhum produto encontrado.</td>`;
    tbody.appendChild(row);
    return;
  }

  dados.forEach((dado) => {
    // Verificar se a descrição está disponível, caso contrário, substituir por "Descrição não encontrada"
    const descricao = dado.descricao || "Descrição não encontrada";

    const row = document.createElement("tr");

    // Adicionando quebra de linha após a descrição, ou outra coluna que desejar
    row.innerHTML = `
        <td>${dado.id}</td>
        <td>${dado.produto}</td>
        <td>${dado.classificacao}</td>
        <td>${descricao}<br></td> <!-- Adicionando quebra de linha aqui -->
        <td>${dado.natureza}</td>
        <td>${dado.quantidade}</td>
        <td>${dado.localizacao}</td>
        <td>${dado.custo}</td>
        <td class="actions">
            <button class="btn-estoque1" onclick="abrirModalDetalhes('${dado.id}')">
                <i class="fas fa-info-circle"></i> + Detalhes
            </button>
            <button class="btn-estoque" onclick="abrirModalAtualizar('${dado.id}')">
                <i class="fas fa-edit"></i> Atualizar
            </button>
        </td>
    `;
    tbody.appendChild(row);
  });
}


// Função para criar a paginação com botões "<<" e ">>"
function criarPaginacao(totalPaginas) {
  const paginacaoContainer = document.querySelector(".pagination");
  paginacaoContainer.innerHTML = ""; // Limpar os botões de paginação

  const maxBotoes = 5;
  let inicio = Math.max(1, paginaAtual - Math.floor(maxBotoes / 2));
  let fim = Math.min(totalPaginas, inicio + maxBotoes - 1);

  if (fim - inicio + 1 < maxBotoes) {
    inicio = Math.max(1, fim - maxBotoes + 1);
  }

  if (inicio > 1) {
    const primeiro = document.createElement("button");
    primeiro.textContent = "<<";
    primeiro.addEventListener("click", () => {
      paginaAtual = 1;
      carregarTabela(paginaAtual, document.getElementById("filtroProduto").value);
    });
    paginacaoContainer.appendChild(primeiro);
  }

  for (let i = inicio; i <= fim; i++) {
    const button = document.createElement("button");
    button.textContent = i;
    button.className = i === paginaAtual ? "active" : "";
    button.addEventListener("click", () => {
      paginaAtual = i;
      carregarTabela(paginaAtual, document.getElementById("filtroProduto").value);
    });
    paginacaoContainer.appendChild(button);
  }

  if (fim < totalPaginas) {
    const ultimo = document.createElement("button");
    ultimo.textContent = ">>";
    ultimo.addEventListener("click", () => {
      paginaAtual = fim + 1;
      carregarTabela(paginaAtual, document.getElementById("filtroProduto").value);
    });
    paginacaoContainer.appendChild(ultimo);
  }
}

// Eventos de filtro e limpeza
document.getElementById("filtrar").addEventListener("click", () => {
  carregarTabela(1, document.getElementById("filtroProduto").value);
});

document.getElementById("limpar").addEventListener("click", () => {
  document.getElementById("filtroProduto").value = "";
  carregarTabela(1);
});

// Carregar tabela na inicialização
window.addEventListener("load", () => carregarTabela(paginaAtual));

// Função para filtrar a tabela com base no valor do input
function filtrarTabela() {
  const filtro = document
    .getElementById("filtroProduto")
    .value.toLowerCase()
    .trim();
  const linhas = document.querySelectorAll("#tabelaProdutos tr");

  linhas.forEach((linha) => {
    const produto = linha.cells[1]?.textContent.toLowerCase().trim() || "";
    // Exibe a linha se o produto contém o filtro, ou oculta se não contém
    linha.style.display = produto.includes(filtro) ? "" : "none";
  });
}

// Função para limpar o campo de input
function limparFiltro() {
  document.getElementById("filtroProduto").value = ""; // Limpar o campo de texto
  filtrarTabela(); // Reaplicar o filtro
}

// Função para limitar a tabela a 7 linhas e adicionar scroll
window.onload = function () {
  const tabelaBody = document.getElementById("tabelaProdutos");
  const linhas = tabelaBody.getElementsByTagName("tr");

  // Limitar a 7 linhas
  const limite = 7;
  for (let i = 0; i < linhas.length; i++) {
    if (i >= limite) {
      linhas[i].style.display = "none";
    }
  }

  // Adicionar barra de rolagem
  tabelaBody.style.maxHeight = "300px";
  tabelaBody.style.overflowY = "auto";
};

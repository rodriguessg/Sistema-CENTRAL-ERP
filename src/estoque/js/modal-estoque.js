// Função para abrir o modal e carregar os detalhes
function abrirModalDetalhes(id) {
  const linha = [...document.querySelectorAll("#tabelaProdutos tr")].find(
    (tr) => tr.children[0].textContent == id
  );
  if (!linha) return;

  const dados = {
    id: linha.children[0].textContent,
    produto: linha.children[1].textContent,
    classificacao: linha.children[2].textContent,
    localizacao: linha.children[3].textContent,
    codigo: linha.children[4].textContent,
    natureza: linha.children[5].textContent,
    quantidade: linha.children[6].textContent,
  };

  const modalConteudo = document.getElementById("modal-informacoes");
  modalConteudo.innerHTML = `
            <h3>Detalhes do Produto</h3>
            <p><strong>ID:</strong> ${dados.id}</p>
            <p><strong>Produto:</strong> ${dados.produto}</p>
            <p><strong>Classificação:</strong> ${dados.classificacao}</p>
            <p><strong>Localização:</strong> ${dados.localizacao}</p>
            <p><strong>Código:</strong> ${dados.codigo}</p>
            <p><strong>Natureza:</strong> ${dados.natureza}</p>
            <p><strong>Quantidade:</strong> ${dados.quantidade}</p>
        `;

  document.getElementById("modal-detalhes").style.display = "block";
}

// Função para abrir o modal de atualização
function abrirModalAtualizar(id) {
  const linha = [...document.querySelectorAll("#tabelaProdutos tr")].find(
    (tr) => tr.children[0].textContent == id
  );
  if (!linha) return;

  const dados = {
    id: linha.children[0].textContent,
    produto: linha.children[1].textContent,
    classificacao: linha.children[2].textContent,
    localizacao: linha.children[3].textContent,
    codigo: linha.children[4].textContent,
    natureza: linha.children[5].textContent,
    quantidade: linha.children[6].textContent,
  };

  const modalConteudo = document.getElementById("modal-atualizacao");
  modalConteudo.innerHTML = `
            <h2><i class="fas fa-box-open"></i> Atualizar Produto</h2>
            <form id="formAtualizar">
                <input type="hidden" name="id" value="${dados.id}">
    
                <label><i class="fas fa-tag"></i> Produto:</label>
                <input type="text" name="produto" value="${dados.produto}" readonly>
    
                <label><i class="fas fa-layer-group"></i> Classificação:</label>
                <input type="text" name="classificacao" value="${dados.classificacao}" readonly>
    
                <label><i class="fas fa-map-marker-alt"></i> Localização:</label>
                <input type="text" name="localizacao" value="${dados.localizacao}" readonly>
    
                <label><i class="fas fa-barcode"></i> Código:</label>
                <input type="text" name="codigo" value="${dados.codigo}" readonly>
    
                <label><i class="fas fa-cube"></i> Natureza:</label>
                <input type="text" name="natureza" value="${dados.natureza}" readonly>
    
                <label><i class="fas fa-sort-numeric-up"></i> Quantidade:</label>
                <input type="number" name="quantidade" value="${dados.quantidade}">
    
                <button type="button" class="btn-salvar" onclick="salvarAlteracoes()">
                    <i class="fas fa-check-circle"></i> Salvar Alterações
                </button>
            </form>
        `;

  document.getElementById("modal-atualizar").style.display = "block";
}

// Função para salvar alterações
function salvarAlteracoes() {
  const form = document.getElementById("formAtualizar");
  const dadosAtualizados = new FormData(form);

  // Enviar os dados para o backend via fetch ou outra requisição AJAX
  fetch("atualizar_produto.php", {
    method: "POST",
    body: dadosAtualizados,
  })
    .then((response) => response.text())
    .then((result) => {
      alert("Produto atualizado com sucesso!");
      fecharModal("modal-atualizar");
      location.reload(); // Recarrega a página para atualizar a tabela
    })
    .catch((error) => alert("Erro ao atualizar produto."));
}

// Função para fechar o modal
function fecharModal(modalId) {
  document.getElementById(modalId).style.display = "none";
}

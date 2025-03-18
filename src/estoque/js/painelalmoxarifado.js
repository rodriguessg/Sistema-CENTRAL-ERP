document.addEventListener("DOMContentLoaded", () => {
    // Função para atualizar os cards com os dados do backend
    function atualizarCards(data) {
        const totalProdutosCard = document.getElementById('totalProdutos');
        const produtoAcabandoCard = document.getElementById('produtoAcabando');
        const cardProdutoAcabando = document.getElementById('cardProdutoAcabando');
        const listaProdutosAcabando = document.getElementById('listaProdutosAcabando');
        
        // Atualiza o total de produtos
        totalProdutosCard.textContent = data.totalProdutos;

        // Verifica se há produtos acabando
        const produtosAcabando = data.produtos.filter(produto => produto.quantidade < 10);
        
        if (produtosAcabando.length > 0) {
            // Exibe o card de produtos acabando
            cardProdutoAcabando.style.display = 'block';

            // Preenche a lista de produtos com pouca unidade
            listaProdutosAcabando.innerHTML = ''; // Limpa a lista antes de adicionar os novos itens
            produtosAcabando.forEach(produto => {
                const li = document.createElement('li');
                li.textContent = `${produto.nome} - ${produto.quantidade} unidades`;
                listaProdutosAcabando.appendChild(li);
            });

            // Atualiza o texto do produto que está acabando
            produtoAcabandoCard.textContent = `${produtosAcabando.length} produto(s) acabando`;
        } else {
            // Se não houver produtos acabando, oculta o card
            cardProdutoAcabando.style.display = 'none';
        }
    }

    // Função para buscar dados do backend
    function buscarDados() {
        fetch('dados_estoque.php')
            .then(response => response.json())
            .then(data => {
                atualizarCards(data);
            })
            .catch(error => console.error('Erro ao carregar dados:', error));
    }

    // Iniciar o carregamento dos dados
    buscarDados();
    });
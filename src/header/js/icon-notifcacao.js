(function() {
    // Função para marcar a notificação como lida
    function markAsRead(notificationId) {
        // Envia uma requisição GET para marcar a notificação como lida
        fetch(`/marcar_notificacao_lida.php?id=${notificationId}`, {
            method: 'GET'  // Pode ser POST se preferir
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualiza o contador de notificações
                getNotificationsCount();
            } else {
                alert('Erro ao marcar como lida!');
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
        });
    }
    

    // Função para atualizar o contador de notificações não lidas
    function getNotificationsCount() {
        // Fazer a requisição para pegar o número de notificações não lidas
        fetch('/getNotificationsCount.php')
        .then(response => response.json())
        .then(data => {
            // Atualiza o número de notificações não lidas na interface
            const notificationCountElement = document.getElementById('notificationCount');
            if (notificationCountElement) {
                notificationCountElement.textContent = data.unreadCount;
            }
        })
        .catch(error => {
            console.error('Erro ao pegar o contador de notificações:', error);
        });
    }

    // Aguardando o carregamento do DOM antes de executar
    document.addEventListener('DOMContentLoaded', function() {
        // Certifique-se de que o contador de notificações seja atualizado quando a página carregar
        getNotificationsCount();
    });
})();

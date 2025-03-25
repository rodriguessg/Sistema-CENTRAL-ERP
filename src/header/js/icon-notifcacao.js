(function() {
    // Função para marcar a notificação como lida
    function markAsRead(notificationId) {
        // Fazer a requisição para marcar a notificação como lida
        fetch(`/marcar_notificacao_lida.php?id=${notificationId}`, {
            method: 'GET', // Ou 'POST', dependendo de como seu servidor está configurado
        })
        .then(response => response.json())
        .then(data => {
            // Verifica se o status da resposta foi OK
            if (data.success) {
                // Atualiza o contador de notificações
                getNotificationsCount();
                // Aqui, você pode também remover ou marcar a notificação como lida na interface
                const notificationElement = document.getElementById(`notification-${notificationId}`);
                if (notificationElement) {
                    notificationElement.classList.add('read'); // Adiciona uma classe 'read' para marcar visualmente
                }
            } else {
                console.error("Erro ao marcar notificação como lida:", data.message);
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

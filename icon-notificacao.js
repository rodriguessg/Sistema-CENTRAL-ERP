

// Função para carregar as notificações ao clicar no ícone de sino
document.getElementById('notificacaoLink').addEventListener('click', function() {
    // Faz a requisição para o PHP (getNotifications.php)
    fetch('getNotificationCount.php')
        .then(response => response.json())
        .then(data => {
            // Atualiza o conteúdo do dropdown com as notificações
            const notificationList = document.getElementById('notificationList');
            notificationList.innerHTML = '';  // Limpa o conteúdo atual

            if (data.length > 0) {
                data.forEach(notification => {
                    const notificationItem = document.createElement('a');
                    notificationItem.classList.add('dropdown-item');
                    notificationItem.href = '#';
                    notificationItem.innerHTML = notification.mensagem;
                    notificationList.appendChild(notificationItem);
                });
            } else {
                const noNotificationItem = document.createElement('p');
                noNotificationItem.classList.add('dropdown-item');
                noNotificationItem.innerHTML = 'Sem novas notificações.';
                notificationList.appendChild(noNotificationItem);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar notificações:', error);
        });
});
function markAsRead(notificationStatus) {
    fetch('marca_notificacao_lida.php', {
        method: 'POST',
        body: JSON.stringify({ status: notificationStatus }),  // Envia o status em vez do ID
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Notificação marcada como lida!");
            document.location.reload();
        } else {
            alert("Erro ao marcar a notificação como lida.");
        }
    })
    .catch(error => console.error('Erro ao marcar notificação:', error));
}



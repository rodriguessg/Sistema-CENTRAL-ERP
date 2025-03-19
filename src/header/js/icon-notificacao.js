// Função para carregar as notificações ao clicar no ícone de sino
document.getElementById('notificacaoLink').addEventListener('click', function() {
    // Faz a requisição para o PHP (getNotifications.php)
    fetch('getNotificationsCount.php')
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
function markAsRead(notificationId) {
    fetch('marcar_notificacao_lida.php', {
        method: 'POST',
        body: JSON.stringify({ id: notificationId }),
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar a interface (exemplo: remover a notificação da lista ou marcar de alguma forma)
            alert("Notificação marcada como lida!");
            document.location.reload(); // Recarga a página para refletir as mudanças
        } else {
            alert("Erro ao marcar a notificação como lida.");
        }
    })
    .catch(error => console.error('Erro ao marcar notificação:', error));
}

$(document).ready(function() {
    const initialOptions = [
        'Gerar relatório do sistema',
        'Quantos usuários estão cadastrados?',
        'Quantos produtos estão cadastrados?',
        'Informações sobre o patrimônio',
        'Quantos setores estão ativos?',
        'Quantos funcionários estão cadastrados?',
        'O que você pode fazer?'
    ];

    function addOptions(options) {
        $('#chat-options').empty();
        options.forEach(option => {
            const button = $('<button>')
                .addClass('btn btn-outline-primary m-1')
                .text(option)
                .on('click', function() {
                    sendMessage(option);
                });
            $('#chat-options').append(button);
        });
    }

    function appendMessage(sender, message) {
        const messageDiv = $('<div>').html(`<strong>${sender}:</strong> ${message}`);
        $('#chat-messages').append(messageDiv);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }

    function sendMessage(message) {
        appendMessage('Você', message);
        $('#chat-input').val('');

        $.ajax({
            url: 'ia.php',
            type: 'POST',
            data: JSON.stringify({ message: message }),
            contentType: 'application/json',
            success: function(response) {
                appendMessage('IA', response.reply);
                if (response.options && response.options.length > 0) {
                    addOptions(response.options);
                }
            },
            error: function(xhr, status, error) {
                appendMessage('Erro', 'Não foi possível obter resposta da IA.');
            }
        });
    }

    $('#send-message').on('click', function() {
        const message = $('#chat-input').val();
        if (message.trim() !== '') {
            sendMessage(message);
        }
    });

    $('#iaModal').on('show.bs.modal', function() {
        addOptions(initialOptions);
    });
});
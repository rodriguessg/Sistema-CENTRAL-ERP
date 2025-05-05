$(document).ready(function() {
    // Toggle category form visibility
    $('#toggle-category-form').click(function() {
        $('#add-category-form').slideToggle('fast');
    });
    

    // Navigation buttons
    $('#prev-month, #next-month').click(function() {
        window.location.href = $(this).data('url');
    });

    // Select day to view events
    $('.day-cell').click(function() {
        const day = $(this).data('day');
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('day', day);
        window.location.search = urlParams.toString();
    });


 // Toggle dropdown menu
$('.menu-icon').click(function(e) {
    e.stopPropagation();  // Impede a propagação do clique para o document
    const $dropdown = $(this).siblings('.dropdown-menu');

    // Se o dropdown já estiver visível, fecha, senão abre
    if ($dropdown.is(':visible')) {
        $dropdown.hide();
    } else {
        // Esconde outros dropdowns e mostra o atual
        $('.dropdown-menu').not($dropdown).hide();
        $dropdown.show();  // Exibe o dropdown
    }
});

// Fechar o dropdown ao clicar fora dele
$(document).click(function(e) {
    // Se o clique não for dentro do menu ou no ícone
    if (!$(e.target).closest('.daily-event-menu').length) {
        $('.dropdown-menu').hide();  // Esconde todos os menus
    }
});

// Impedir que o clique dentro do dropdown feche o menu
$('.dropdown-menu').click(function(e) {
    e.stopPropagation();  // Impede o menu de fechar ao clicar dentro dele
});


    // Toggle email field visibility
    $('#enviar-email').change(function() {
        if ($(this).is(':checked')) {
            $('#email-field, #salvar-email-field').show();
        } else {
            $('#email-field, #salvar-email-field').hide();
            $('#email-destinatario').val('');
            $('#email-destinatario-input').val('');
            $('#salvar-email').prop('checked', false);
        }
    });

    // Toggle between select and input for email
    $('#email-destinatario').change(function() {
        if ($(this).val() === '') {
            $('#email-destinatario-input').show().focus();
        } else {
            $('#email-destinatario-input').hide();
        }
    });

    // Open edit form in sidebar
    $('.edit-link, .evento').click(function(e) {
        e.preventDefault();
        const eventId = $(this).data('id');
        $.ajax({
            url: 'fetch_event.php',
            method: 'POST',
            data: { event_id: eventId },
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    alert(data.error);
                } else {
                    // Update form for editing
                    $('#form-title').text('Editar Evento');
                    $('#form-action').val('edit_event');
                    $('#event-id').val(data.id);
                    $('#titulo').val(data.titulo);
                    $('#descricao').val(data.descricao);
                    $('#data').val(data.data.split(' ')[0]);
                    $('#hora').val(data.hora);
                    $('#categoria').val(data.categoria);
                    $('#cor').val(data.cor);
                    $('#enviar-email').prop('checked', false);
                    $('#email-field, #salvar-email-field').hide();
                    $('#email-destinatario').val('');
                    $('#email-destinatario-input').val('');
                    $('#salvar-email').prop('checked', false);
                    $('#submit-btn').text('Evento de atualização');
                    $('#cancel-btn').show();
                }
            },
            error: function() {
                alert('Erro ao carregar o evento.');
            }
        });
    });

    // Reset form to "Add Event" state
    $('#cancel-btn').click(function() {
        $('#form-title').text('Adicionar Evento');
        $('#form-action').val('add_event');
        $('#event-id').val('');
        $('#event-form')[0].reset();
        $('#data').val('<?= sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $selectedDay) ?>');
        $('#cor').val('#ff0000'); // Reset color to default
        $('#enviar-email').prop('checked', false);
        $('#email-field, #salvar-email-field').hide();
        $('#email-destinatario').val('');
        $('#email-destinatario-input').val('');
        $('#salvar-email').prop('checked', false);
        $('#submit-btn').text('Adicionar Evento');
        $('#cancel-btn').hide();
    });

    // Delete event from daily events preview
    $('.delete-link').click(function(e) {
        e.preventDefault();
        if (confirm('Tem certeza que deseja excluir este evento?')) {
            const eventId = $(this).data('id');
            $('<form>', {
                method: 'POST',
                action: window.location.href,
                html: `
                    <input type="hidden" name="action" value="delete_event">
                    <input type="hidden" name="event_id" value="${eventId}">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                `
            }).appendTo('body').submit();
        }
    });


    // Substituir o texto do evento por ícones no calendário
    $('.day-cell').each(function() {
        var eventContent = $(this).find('.evento');
        eventContent.each(function() {
            var icon = $('<i>').addClass('fas fa-calendar-day'); // Exemplo de ícone
            $(this).empty().append(icon); // Remove o texto e adiciona o ícone
        });
    });

    
});



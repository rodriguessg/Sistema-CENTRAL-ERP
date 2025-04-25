
$(document).ready(function() {
    // Toggle category form visibility
    $('#toggle-category-form').click(function() {
        $('#add-category-form').toggle();
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
        e.stopPropagation();
        const $dropdown = $(this).siblings('.dropdown-menu');
        $('.dropdown-menu').not($dropdown).hide();
        $dropdown.toggle();
    });

    // Hide dropdown when clicking outside
    $(document).click(function() {
        $('.dropdown-menu').hide();
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
});
$(document).ready(function() {
    // AJAX for adding events
    $('#add-event-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#messages').html($(response).find('#messages').html());
                reloadCalendar();
            },
            error: function() {
                $('#messages').html('<div class="message message-error">Erro ao adicionar evento.</div>');
            }
        });
    });

    // AJAX for editing events
    $('#edit-event-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#edit-event-modal').hide();
                $('#messages').html($(response).find('#messages').html());
                reloadCalendar();
            },
            error: function() {
                $('#messages').html('<div class="message message-error">Erro ao editar evento.</div>');
            }
        });
    });

    // Delete event
    $('#delete-event-btn').on('click', function() {
        if (confirm('Deseja excluir este evento?')) {
            $.ajax({
                url: window.location.href,
                method: 'POST',
                data: {
                    action: 'delete_event',
                    event_id: $('#edit-event-id').val(),
                    csrf_token: $('input[name="csrf_token"]').val()
                },
                success: function(response) {
                    $('#edit-event-modal').hide();
                    $('#messages').html($(response).find('#messages').html());
                    reloadCalendar();
                },
                error: function() {
                    $('#messages').html('<div class="message message-error">Erro ao excluir evento.</div>');
                }
            });
        }
    });

    // Month navigation
    $('#prev-month, #next-month').on('click', function() {
        const url = $(this).data('url');
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                const $response = $(response);
                $('.calendar').html($response.find('.calendar').html());
                $('#messages').html($response.find('#messages').html());
                history.pushState({}, '', url);
            },
            error: function() {
                $('#messages').html('<div class="message message-error">Erro ao carregar o calendário.</div>');
            }
        });
    });

    // Open edit modal on event click
    $(document).on('click', '.evento', function() {
        const eventId = $(this).data('id');
        $.ajax({
            url: window.location.href,
            method: 'GET',
            data: { event_id: eventId },
            success: function(response) {
                const $response = $(response);
                const event = $response.find(`.evento[data-id="${eventId}"]`).data('event');
                // Note: For simplicity, we'll fetch event details server-side in a real app
                // Here, we simulate by filling the form (in a real app, add an API endpoint)
                $('#edit-event-id').val(eventId);
                $('#edit-titulo').val($(response).find(`.evento[data-id="${eventId}"] strong`).text());
                $('#edit-categoria').val('geral'); // Replace with actual data
                $('#edit-cor').val($(response).find(`.evento[data-id="${eventId}"]`).css('background-color'));
                $('#edit-event-modal').show();
            }
        });
    });

    // Close modal
    $('#close-modal-btn').on('click', function() {
        $('#edit-event-modal').hide();
    });

    // Keyboard navigation for events
    $(document).on('keydown', '.evento', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            $(this).trigger('click');
        }
    });

    // Reload calendar after changes
    function reloadCalendar() {
        $.ajax({
            url: window.location.href,
            method: 'GET',
            success: function(response) {
                $('.calendar').html($(response).find('.calendar').html());
            }
        });
    }
});
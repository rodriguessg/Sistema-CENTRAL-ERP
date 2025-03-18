//Este código realiza a atualização das informações sobre o perfil
$(document).ready(function() {
    $('#perfilModal').on('show.bs.modal', function () {
        $.ajax({
            url: 'perfil.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                $('#modal-email').text(data.email);
                $('#modal-setor').text(data.setor);
                $('#modal-tempo-registro').text(data.tempo_registro);
                $('#modal-movimentacoes').text(data.movimentacoes);
            },
            error: function(xhr, status, error) {
                alert('Erro ao carregar dados: ' + error);
            }
        });
    });
});

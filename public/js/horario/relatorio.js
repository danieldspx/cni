$(document).ready(function(){
    $('#relatorioLink').addClass('active');
});

$('#saveRelatorio').click(function(){
    var _token = $('#token').val();
    var conteudo = $("#descricao").val();
    if(conteudo != ""){
        $.ajax({
            method: "POST",
            url: "relatorio/salvar",
            data: {_token: _token, conteudo: conteudo},
            success: function(response){
                pushMessage(response);
            }
        });
    } else {
        toastr.warning('Digite o conteúdo da aula.');
    }
});

$('#updateRelatorio').click(function(){
    var _token = $('#token').val();
    $.ajax({
        method: "POST",
        url: "relatorio/update",
        data: {_token: _token},
        success: function(response){
            console.log(response);
            switch (parseInt(response)) {
                case 406:
                    toastr.error('Erro ao atualizar o relatório.');
                    break;
                case 403:
                    toastr.error('Erro ao criar diretório.');
                    break;
                default:
                    toastr.success('Relatório salvo com sucesso.');
            }
        }
    });
});

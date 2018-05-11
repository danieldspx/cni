$(document).ready(function(){
    $('#relatorioLink').addClass('active');
    toastr.options.progressBar = true;
    toastr.options.closeButton = true
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
                console.log(response);
                switch (parseInt(response)) {
                    case 406:
                        toastr.error('Erro ao salvar o relatório.');
                        break;
                    case 403:
                        toastr.error('Erro ao criar diretório.');
                        break;
                    default:
                        toastr.success('Relatório salvo com sucesso.');
                }
            }
        });
    } else {
        toastr.warning('Digite o conteúdo da aula.');
    }
});

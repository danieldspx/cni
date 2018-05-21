$(document).ready(function(){
    $('#ocorrenciaLink').addClass('active');
    toastr.options.progressBar = true;
    toastr.options.closeButton = true
});

$('.setOcorrencia').click(function(){
    var idComplete = $(this).parent().data('id');
    var id = idComplete.slice(9);
    var nome = $('#'+idComplete+">.nomeAluno").text();
    $('#nome').val(nome);
    $('#idAluno').val(id);
    openPanel();
    updateMaterial();
});

$('#addOcorrencia').click(function(){
    var idAluno = $('#idAluno').val();
    var descricao = $('#descricao').val();
    var _token = $("#token").val();
    if(descricao.length != 0){
        $.ajax({
            method: "POST",
            url: "ocorrencia/adicionar",
            data: {"alunos_id":idAluno,"descricao": descricao,"_token":_token},
            success: function(response){
                console.log(response);
                switch (parseInt(response)) {
                    case 406:
                        toastr.error('Erro ao salvar a ocorrência.');
                        break;
                    default:
                        toastr.success('Ocorrência salva com sucesso.');
                }
            }
        });
    } else {
        toastr.warning('Digite a descrição.', 'Atenção');
    }
});

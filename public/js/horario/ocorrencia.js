$(document).ready(function(){
    $('#ocorrenciaLink').addClass('active');
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
    this.disabled = true;
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
                pushMessage(response,function(){
                    $('#descricao').val("");
                    $('#idAluno').val("");
                    closePanel();
                    this.disabled = false;
                });
            }
        });
    } else {
        toastr.warning('Digite a descrição.', 'Atenção');
    }
});

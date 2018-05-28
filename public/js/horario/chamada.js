$(document).ready(function(){
    $('#chamadaLink').addClass('active');
	$("#matricula").keypress(function(e) {
        if(e.which == 13 && e.shiftKey) {
            e.preventDefault();
            $('#addAlunoHorario').trigger('click');
        }
    });
});

function fixBugButton(id){
    if($(id+" .options").css("display") == "none"){ //Should be plus
        if($(id+" .showOptions").hasClass('mdi-minus-circle')){//Its minus
            $(id+" .showOptions").toggleClass("mdi-plus-circle"); //Change to plus
            $(id+" .showOptions").toggleClass("mdi-minus-circle");//
        }
    } else { //Should be minus
        if($(id+" .showOptions").hasClass('mdi-plus-circle')){//Its plus
            $(id+" .showOptions").toggleClass("mdi-plus-circle"); //Change to minus
            $(id+" .showOptions").toggleClass("mdi-minus-circle");//
        }
    }
}

$(".showOptions").click(function(element){
    var id = "#"+$(element.target).parent().data('id');

    $(id+" .showOptions").animateCss('flipInY');
    if($(id+" .showOptions").hasClass('mdi-plus-circle')){
       $(id+" .options").css("display","inline-block");
       $(id+" .options").animateCss('zoomIn',function(){
           fixBugButton(id);
       });
    } else{
       $(id+" .options").animateCss('zoomOut',function(){
          $(id+" .options").css("display","none");
          fixBugButton(id);
       });
    }
      $(id+" .showOptions").toggleClass("mdi-plus-circle");
      $(id+" .showOptions").toggleClass("mdi-minus-circle");
      $(id+" .showOptions").animateCss('flipInY');
});

function horarioId(){
    let urlString = $(location).attr('href');
    let i = urlString.indexOf("/horario/"); //Position of
    let newString = urlString.substr(i+9)
    let id = newString.split("/")[0]
    return id;
}

$("#addAlunoHorario").click(function(){
    var idHorario = horarioId();
    var _token = $("#token").val();
    var matricula = $("#matricula").val();
    var url = "chamada/adicionar";
    if(matricula.length != 0){
        $.ajax({
            url: url,
            method: 'POST',
            data: {"matricula": matricula, "horario": idHorario, "_token": _token},
            success: function(response){
                pushMessage(response,function(type){
                    if(type = 'success'){
                        $('#nome').val('');
                        $('#matricula').val('');
                    }
                });
            }
        });
    } else {
        toastr.warning('Digite a matrícula do aluno.', 'Atenção');
    }
});

$("#removeAlunoHorario").click(function(){
    var idHorario = horarioId();
    var _token = $("#token").val();
    var matricula = $("#matricula").val();
    var url = "chamada/remover";
    if(matricula.length != 0){
        $.ajax({
            url: url,
            method: 'POST',
            data: {"matricula": matricula, "horario": idHorario, "_token": _token},
            success: function(response){
                pushMessage(response,function(code){
                    if(code = 'success'){
                        $('#nome').val('');
                        $('#matricula').val('');
                        $('div.alunoContainer[data-matricula='+matricula+"]").remove();
                    }
                });
            }
        });
    } else {
        toastr.warning('Digite a matrícula do aluno.', 'Atenção');
    }
});

function setSituacao(element,situacao){
    var id = "#"+$(element.target).parent().data('id');
    if (typeof($(element.target).parent().data('id')) == 'undefined') {
        id = "#"+$(element.target).parent().parent().data('id');
    }
    $(id).data('situacao',situacao);
}

function prependHTML(element,htmlClass){
    var id = "#"+$(element.target).parent().data('id');
    if (typeof($(element.target).parent().data('id')) == 'undefined') {
        id = "#"+$(element.target).parent().parent().data('id');
    }
    var htmlPiece = "<i class='mdi mdi-"+htmlClass+"'></i>";
    $(id+" .mdi-close-octagon").remove();
    $(id+" .mdi-alert-decagram").remove();
    $(id+" .mdi-approval").remove();
    $(id+" .mdi-alert").remove();
    $(id+" .mdi-alert-octagon").remove();
    $(id).prepend(htmlPiece);
}
$(".setFalta").click(function(element){
    prependHTML(element,"close-octagon");
    setSituacao(element,0);
});
$(".setPresenca").click(function(element){
    prependHTML(element,"approval");
    setSituacao(element,1);
});
$(".setChange").click(function(element){
    prependHTML(element,"alert-decagram");
    setSituacao(element,2);
});
$(".setGraduated").click(function(element){
    prependHTML(element,"alert-octagon");
    setSituacao(element,3);
});
$(".setUnknown").click(function(element){
    prependHTML(element,"alert");
    setSituacao(element,4);
});

$('#saveChamada').click(function(){
    var idHorario = horarioId();
    var _token = $("#token").val();
    var dataAlunos = [];
    $('.alunoContainer').each(function(){
        let aluno = {};
        aluno.alunos_id = parseInt((this.id).replace("alunoItem",""));
        if(typeof($(this).data('situacao')) == 'undefined'){//Nao foi feita a chamada
            toastr.error('Faça a chamada para todos os alunos.', 'Erro');
            dataAlunos = []; //Clean Array
            return false;
        }
        aluno.situacoes_id = $(this).data('situacao');
        dataAlunos.push(aluno);//Add aluno to the list
    });
    if(dataAlunos.length != 0){
        $.ajax({
            url: "chamada/salvar",
            method: "POST",
            data: {"dataAlunos":JSON.stringify(dataAlunos),"horario": idHorario,"_token":_token},
            success: function(response){
                pushMessage(response);
            }
        });
    }
});

function populateForm(aluno) {
    $("#matricula").val(aluno.matricula);
    $("#nome").val(aluno.nome);
    updateMaterial(); //Update Labels
}

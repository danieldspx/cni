$.fn.extend({
  animateCss: function(animationName, callback) {
    var animationEnd = (function(el) {
      var animations = {
        animation: 'animationend',
        OAnimation: 'oAnimationEnd',
        MozAnimation: 'mozAnimationEnd',
        WebkitAnimation: 'webkitAnimationEnd',
      };

      for (var t in animations) {
        if (el.style[t] !== undefined) {
          return animations[t];
        }
      }
    })(document.createElement('div'));

    this.addClass('animated ' + animationName).one(animationEnd, function() {
      $(this).removeClass('animated ' + animationName);

      if (typeof callback === 'function') callback();
    });

    return this;
  },
});

$(document).ready(function(){
    $('#chamadaLink').addClass('active');
    toastr.options.progressBar = true;
    toastr.options.closeButton = true;
    toastr.options.preventDuplicates = true;
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
    var url = "/horario/"+idHorario+"/adicionar";
    $.ajax({
        url: url,
        method: 'POST',
        data: {"matricula": matricula, "horario": idHorario, "_token": _token},
        success: function(response){
            switch (parseInt(response)) {
                case 409:
                    toastr.error('Conflito de informações. Tente novamente.', 'Erro');
                    break;
                case 406:
                    toastr.error('Não foi possível adicionar o aluno.', 'Erro');
                    break;
                case 404:
                    toastr.error('Aluno não encontrado.', 'Erro');
                    break;
                default:
                    toastr.success('Aluno adicionado com sucesso!', 'Sucesso');
            }
        }
    });
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
                console.log(response);
                switch (parseInt(response)) {
                    case 409:
                        toastr.error('Conflito de informações. Tente novamente.', 'Erro');
                        break;
                    case 406:
                        toastr.error('Não foi possível criar a chamada.', 'Erro');
                        break;
                    case 417:
                        toastr.error('Dados dos Alunos não foram salvos', 'Erro');
                        break;
                    default:
                        toastr.success('Chamada salva com sucesso!', 'Sucesso');
                }
            }
        });
    }
});

$("#buscarAlunoHorario").click(function(){
    var idHorario = horarioId();
    var _token = $("#token").val();
    var nomeAluno = $("#nome").val();
    if(nomeAluno.length != 0){
        $.ajax({
            url: "chamada/buscaAluno",
            method: "POST",
            data: {"dataAlunos":JSON.stringify(dataAlunos),"horario": idHorario,"_token":_token},
            success: function(response){
                console.log(response);
                switch (parseInt(response)) {
                    case 409:
                        toastr.error('Conflito de informações. Tente novamente.', 'Erro');
                        break;
                    case 406:
                        toastr.error('Não foi possível criar a chamada.', 'Erro');
                        break;
                    case 417:
                        toastr.error('Dados dos Alunos não foram salvos', 'Erro');
                        break;
                    default:
                        toastr.success('Chamada salva com sucesso!', 'Sucesso');
                }
            }
        });
    } else {
        toastr.warning('Digite o nome do aluno.', 'Atenção');
    }
});

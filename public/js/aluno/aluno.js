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
    $('#alunoLink').addClass('active');
    toastr.options.progressBar = true;
    toastr.options.closeButton = true;
});

$(".showOptions").click(function(element){
    var id = "#"+$(element.target).parent().data('id');
    $(id+" .showOptions").animateCss('flipInY');
    if($(id+" .showOptions").hasClass('mdi-plus-circle')){
        $(id+" .options").css("display","inline-block");
        $(id+" .options").animateCss('zoomIn');
    } else{
        $(id+" .options").animateCss('zoomOut',function(){
            $(id+" .options").css("display","none");
        });
    }
        $(id+" .showOptions").toggleClass("mdi-plus-circle");
        $(id+" .showOptions").toggleClass("mdi-minus-circle");
        $(id+" .showOptions").animateCss('flipInY');
});

$("#addAlunoList").click(function(){
    var data = {};
    data.matricula = $("#matricula").val();
    data.nome = $("#nome").val();
    data.nascimento = $("#nascimento").val();
    data.situacao = $("input[name=situacao]:checked").val();
    data.telefone = $("#telefone").val();
    var _token = $("#token").val();
    var someEmpty = false;
    for(var key in data){
        if(data[key] == ""){
            someEmpty = true;
            break;
        }
    }
    if(someEmpty){
        toastr.warning('Digite todos os dados!', 'Erro');
    } else { //Good to go
        $.ajax({
            url: "aluno/adicionar",
            method: "POST",
            data: {"data":JSON.stringify(data),"_token":_token},
            success: function(response){
                console.log(response);
                switch (parseInt(response)) {
                    case 400:
                        toastr.error('Erro ao incluir no Banco de dados!', 'Erro');
                        break;
                    case 401:
                        toastr.warning('Digite todos os dados!', 'Erro');
                        break;
                    default:
                        toastr.success('Aluno salvo!', 'Sucesso');
                        break;
                }
            }
        });
        clearForm();
        $('#ativo').prop('checked', true); //Check 'Ativo' option
    }
});

$("#searchAluno").click(function(){
    var matricula = $("#matricula").val();
    var nome = $("#nome").val();
    var _token = $("#token").val();
    if(matricula == "" && nome == ""){
        toastr.warning('Digite a matricula ou o nome para buscar', 'Atenção');
    } else {
        $.ajax({
            url: "aluno/buscar",
            method: "POST",
            data: {"matricula":matricula,"nome":nome,"_token":_token},
            success: function(response){
                if(parseInt(response) == 400){
                    toastr.error('Matrícula não encontrada!', 'Erro');
                } else if(parseInt(response) == 404){
                    toastr.error('Nome não encontrado!', 'Erro');
                } else {
                    // var alunos = JSON.parse(response);
                    console.log(response);
                    // $("#matricula").val(aluno.matricula);
                    // $("#nome").val(aluno.nome);
                    // $("#nascimento").val(aluno.nascimento);
                    // $("#telefone").val(aluno.telefone);
                    // if(parseInt(aluno.situacao) == 1){//Ativo
                    //     $("#ativo").prop("checked",true);
                    // } else {
                    //     $("#inativo").prop("checked",true);
                    // }
                    // updateMaterial(); //Update Labels
                }
            }
        });
    }
});

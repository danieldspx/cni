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

function populateForm(aluno) {
    $("#matricula").val(aluno.matricula);
    $("#nome").val(aluno.nome);
    $("#nascimento").val(aluno.nascimento);
    $("#telefone").val(aluno.telefone);
    $("#nome_responsavel").val(aluno.nome_responsavel);
    $("#telefone_responsavel").val(aluno.telefone_responsavel);
    $("#celular_responsavel").val(aluno.celular_responsavel);
    if(parseInt(aluno.situacao) == 1){//Ativo
        $("#ativo").prop("checked",true);
    } else {
        $("#inativo").prop("checked",true);
    }
    $('#cursoWrapper').empty();
    for(curso of aluno.cursos){
        var htmlPiece = '<div class="cursoContainer col-sm-8" id="container'+curso.horarios_id+'">\
                            <div class="cursoTitle">\
                                '+curso.materia+'\
                            </div>\
                            <div class="descricao">\
                                <i class="mdi mdi-clock"></i> '+curso.dia+' '+curso.start+' às '+curso.end+'\
                                <div class="iconsOptions">\
                                    <a href="#animatedModal" class="linkHorario openChangeModal">\
                                        <i class="mdi mdi-swap-horizontal mdi-36px changeTurma" title="Troca aluno de turma" data-aluno="'+aluno.matricula+'" data-curso="'+curso.horarios_id+'"></i>\
                                    </a>\
                                    <a href="horario/'+curso.horarios_id+'/chamada" class="linkHorario">\
                                        <i class="mdi mdi-eye mdi-36px seeLink" title="Acesse a turma"></i>\
                                    </a>\
                                </div>\
                            </div>\
                        </div>';
        $('#cursoWrapper').append(htmlPiece);
    }
    updateMaterial(); //Update Labels
    $(".openChangeModal").animatedModal();
}

$(document).ready(function(){
    $('#nascimento').mask('00-00-0000');
    $('#telefone').mask('(00) 00000-0000');
    $('#telefone_responsavel').mask('(00) 0000-0000');
    $('#celular_responsavel').mask('(00) 00000-0000');

    $('#alunoLink').addClass('active');

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

    $("#matricula").keypress(function(e) {
        if(e.which == 13) {
            e.preventDefault();
            searchAluno();
        }
    });

    $("#addAlunoList").click(function(){
		this.disabled = true;
        var data = {};
        data.matricula = $("#matricula").val();
        data.nome = $("#nome").val().toUpperCase();
        data.nascimento = $("#nascimento").val();
        data.situacao = $("input[name=situacao]:checked").val();
        data.telefone = $("#telefone").val();
        data.nome_responsavel = $("#nome_responsavel").val().toUpperCase();
        data.telefone_responsavel = $("#telefone_responsavel").val();
        data.celular_responsavel = $("#celular_responsavel").val();
        var _token = $("#token").val();
        var someEmpty = false;
        for(var key in data){
            if(data[key] == ""){
				if(key.search('telefone') != -1 || key.search('responsavel') != -1){
					delete data[key];
				} else {
					someEmpty = true;
					break;
				}
            }
        }
        if(someEmpty){
            toastr.warning('Digite os dados essenciais', 'Erro');
        } else { //Good to go
            $.ajax({
                url: "aluno/adicionar",
                method: "POST",
                data: {"data":JSON.stringify(data),"_token":_token},
                success: function(response){
                    pushMessage(response);
                }
            });
            clearForm();
            $('#ativo').prop('checked', true); //Check 'Ativo' option
        }
		this.disabled = false;
    });

    $(".openChangeModal").animatedModal();

    $('.diaSelect>.select>.select-options>li').click(function(){
        changeHorario(1);
    });

    $('#changeAluno').click(function(){
        changeHorario(3);
    });

    $('#cursoWrapper').on('click','.openChangeModal',function(elem){
        elem = elem.target;
        $('#alunoChange').val($(elem).data('aluno'));
        $('#fromHorario').val($(elem).data('curso'));
    });

    $("#closebt-container").click(function(){
        $('#changeAluno').prop("disabled", true);
        $('#changeAluno').css("cursor","not-allowed");
    });
});

function setupCursos(response) {
    if(typeof $('.cursoSelect') != "undefined"){
        $('.cursoSelect').empty();
    }
    if(typeof $('.horarioSelect') != "undefined"){
        $('.horarioSelect').empty();
    }
    var htmlPiece = '<div class="row cursoSelect">\
                        <select id="cursoChange" class="col-sm-8">\
                            <option value="" selected>Selecione o curso</option>';
    var cursos = JSON.parse(response);
    cursos.forEach(function(curso){
        htmlPiece += '<option value="'+curso.id+'">'+curso.nome+'</option>';
    });
    htmlPiece += '</select>\
                </div>';
    $('#insideModal').append(htmlPiece);
    $('.cursoSelect>.select>.select-options>li').ready(function(){
        $('.cursoSelect>.select>.select-options>li').click(function(){
            changeHorario(2);
        });
    });
}

function setupHorarios(response) {
    if(typeof $('.horarioSelect') != "undefined"){
        $('.horarioSelect').empty();
    }
    var htmlPiece = '<div class="row horarioSelect">\
                        <select id="horarioChange" class="col-sm-8">\
                            <option value="" selected>Selecione o horário</option>';
    var horarios = JSON.parse(response);
    horarios.forEach(function(horario){
        htmlPiece += '<option value="'+horario.id+'">'+horario.start+' - '+horario.end+'</option>';
    });
    htmlPiece += '</select>\
                </div>';
    $('#insideModal').append(htmlPiece);
    $('.horarioSelect>.select>.select-options>li').ready(function(){
        $('.horarioSelect>.select>.select-options>li').click(function(){
            $('#changeAluno').prop( "disabled", false );
            $('#changeAluno').css( "display","block");
            $('#changeAluno').css( "cursor","pointer");
        });
    });
}

function changeHorario(type){
    $('#changeAluno').prop("disabled", true);
    $('#changeAluno').css("cursor","not-allowed");

    var dia = $('#diaChange>option:selected').val();
    var _token = $("#token").val();
    var dataSend = {type:type, dia:dia, _token:_token};
    if(type == 2){
        dataSend.materia = $('#cursoChange>option:selected').val();
    } else if (type == 3){//Make change
        dataSend.toHorario = $('#horarioChange>option:selected').val();
        dataSend.aluno = $("#alunoChange").val();
        dataSend.fromHorario = $("#fromHorario").val();
    }
    $.ajax({
        url: "aluno/mudar",
        method: "POST",
        data: dataSend,
        success: function(response){
            console.log(response);
            pushMessage(response,function(code){
                if(code == 'success'){
                    //Remove Curso containers
                    $("#cursoWrapper").empty();
                    $('#closebt-container').trigger('click');
                    clearForm();
                }
                if(typeof code != "undefined"){
                    $('#changeAluno').prop("disabled", false);
                    $('#changeAluno').css("cursor","pointer");
                }
            });
            if(type==1){
                setupCursos(response);
            } else if (type==2){
                setupHorarios(response);
            }
            updateSelects();
        }
    });
}

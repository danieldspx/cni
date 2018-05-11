$(document).ready(function(){
    $('#horarioLink').addClass('active');
});

$("#addHorarioList").click(function(){
    var data = {};
    data.materias_id = $("#materiaNH").val();
    var nomeMateria = $("#materiaNH>option:selected").text();
    data.dias_id = $("#diaNH").val();
    var nomeDia = $("#diaNH>option:selected").text();
    data.start = $("#startNH").val();
    data.end = $("#endNH").val();
    var _token = $("#token").val();

    toastr.options.progressBar = true;
    toastr.options.closeButton = true;
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
            url: "horario/adicionar",
            method: "POST",
            data: {"data":JSON.stringify(data),"_token":_token},
            success: function(response){
                var horario = JSON.parse(response);
                var htmlPiece = "<div class='row'>\
                                    <a href='horario/"+horario.id+"' class='linkHorario'>\
                                        <div class='horarioContainer shadow col-sm-10'>\
                                            <div class='horarioTitle'><i class='mdi mdi-checkbox-blank-circle labelHorario'></i>"+nomeMateria+"</div>\
                                            <div class='descricao'><i class='mdi mdi-clock'></i> "+nomeDia+" - "+data.start+" às "+data.end+"</div>\
                                        </div>\
                                    </a>\
                                </div>";
                $("#horarioPlace").append(htmlPiece); //Add new Horario
                if ($('#noHorario').length) {
                    $('#noHorario').remove();
                }
                switch (parseInt(horario.code)) {
                    case 400:
                        toastr.error('Erro ao incluir no Banco de dados!', 'Erro');
                        break;
                    case 401:
                        toastr.warning('Digite todos os dados!', 'Erro');
                        break;
                    default:
                        toastr.success('Horário adicionado!', 'Sucesso');
                        break;
                }
            }
        });
        closePanel();
        clearForm();
    }
});

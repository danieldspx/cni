//////////////////////////////////////
////                              ////
////       Global Variables       ////
////                              ////
//////////////////////////////////////

var searchAlunos = [];

$(document).ready(function(){
    $('#content-search').on("click",'.aluno-option',function(element){
        var key = $(element.target).data('key');
        populateForm(searchAlunos[key]);
        clearResults();
    });

    $("#searchAluno").click(searchAluno);

    $("#nome").keypress(function(e) {
        if(e.which == 13) {
            e.preventDefault();
            searchAluno();
        }
    });
});

function clearResults(){
    searchAlunos = [];
    $('#nome').sweetDropdown('disable');
    $('#content-search').empty();
}

function searchAluno(){
    var matricula = $("#matricula").val();
    var nome = $("#nome").val();
    var additional = $("#additional").val();
    var _token = $("#token").val();
    if(matricula == "" && nome == ""){
        toastr.warning('Digite a matricula ou o nome para buscar', 'Atenção');
    } else {
        if (matricula != "") {
            toastr.info('Você esta pesquisando pela matricula.');
        }
        if(typeof additional == "undefined"){//No additional
            var dataSend = {"matricula":matricula,"nome":nome,"_token":_token};
        } else {
            var dataSend = {"matricula":matricula,"nome":nome,additional: additional,"_token":_token};
        }
        $.ajax({
            url: "/aluno/buscar",
            method: "POST",
            data: dataSend,
            beforeSend: function(){
                clearResults();
            },
            success: function(response){
                pushMessage(response,function(type){
                    if(type == 'success'){
                        var alunos = JSON.parse(response).data;//Specifically this case has data inside the message JSON
                        if(!Array.isArray(alunos)){
                            populateForm(alunos);
                        } else {
                            searchAlunos = alunos; //Set to global scope
                            $('#content-search').empty(); //Clear dropdown data
                            for(var key in alunos){
                                if(alunos.hasOwnProperty(key)){
                                    var html = "<li class='aluno-option'>\
                                                    <a data-key='"+key+"'>"
                                                        +alunos[key]['nome']+
                                                    "</a>\
                                                </li>";
                                    $('#content-search').append(html); //Add dropdown data
                                }
                            }
                            $('#nome').sweetDropdown('attach', '#dropdown-alunos');
                            $('#nome').sweetDropdown('enable');
                            setTimeout(function(){
                                $('#nome').sweetDropdown('show');
                            },200);
                        }
                        updateMaterial(); //Update Labels
                    }
                });
            }
        });
    }
}

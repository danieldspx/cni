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
    var _token = $("#token").val();
    if(matricula == "" && nome == ""){
        toastr.warning('Digite a matricula ou o nome para buscar', 'Atenção');
    } else {
        if (matricula != "") {
            toastr.info('Você esta pesquisando pela matricula.');
        }
        $.ajax({
            url: "/aluno/buscar",
            method: "POST",
            data: {"matricula":matricula,"nome":nome,"_token":_token},
            beforeSend: function(){
                clearResults();
            },
            success: function(response){
                if(parseInt(response) == 400){
                    toastr.error('Matrícula não encontrada!', 'Erro');
                } else if(parseInt(response) == 404){
                    toastr.error('Nome não encontrado!', 'Erro');
                } else {
                    var alunos = JSON.parse(response);
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
            }
        });
    }
}

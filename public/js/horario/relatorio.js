$(document).ready(function(){
    $('#relatorioLink').addClass('active');
});

$('.wrapConteudo').on('click','#saveRelatorio',function(){
    var _token = $('#token').val();
    var conteudo = $("#descricao").val();
    if(conteudo != ""){
        $.ajax({
            method: "POST",
            url: "relatorio/salvar",
            data: {_token: _token, conteudo: conteudo},
            success: function(response){
                console.log(response);
                pushMessage(response,function(type){
                    if(type == 'success'){
                        $('.wrapConteudo').empty();//Wipe Wrapper out
                        var id = JSON.parse(response).id;
                        var htmlPiece = '<div class="table-responsive">\
                                            <table class="table table-bordered table-hover">\
                                                <thead class="thead-dark">\
                                                    <tr style="text-align: center">\
                                                        <th colspan="5">Conteúdo</th>\
                                                    </tr>\
                                                </thead>\
                                                <tbody>\
                                                    <tr>\
                                                        <td>'+conteudo+' <i class="mdi mdi-delete-forever mdi-36px deleteConteudo" title="Delete o conteúdo da aula" id="'+id+'"></i></td>\
                                                    </tr>\
                                                </tbody>\
                                            </table>\
                                        </div>';
                        $('.wrapConteudo').append(htmlPiece);
                    }
                });
            }
        });
    } else {
        toastr.warning('Digite o conteúdo da aula.');
    }
});

$('.wrapConteudo').on('click','.deleteConteudo',function(){
    var _token = $('#token').val();
    $.ajax({
        method: "POST",
        url: "conteudo/remover",
        data: {_token: _token, id_conteudo: this.id},
        success: function(response){
            pushMessage(response,function(type){
                if(type == 'success'){
                    $('.wrapConteudo').empty();//Wipe Wrapper out
                    var htmlPiece = '<div class="titleConteudo">\
                                        <i class="mdi mdi-chair-school"></i> Conteúdo da Aula (Máx.: 500)\
                                    </div>\
                                    <div class="row">\
                                        <div class="input-field col-10 col-sm-7 col-md-5">\
                                            <input id="descricao" type="text" maxlength="500" autocomplete="off">\
                                            <label for="descricao" data-to="descricao">Conteúdo</label>\
                                        </div>\
                                    </div>\
                                    <div class="row">\
                                        <button type="button" class="btn col-sm-6 col-md-4 col-10" id="saveRelatorio">Salvar Relatório <i class="mdi mdi-content-save"></i></button>\
                                    </div>';
                    $('.wrapConteudo').append(htmlPiece);
                }
            });
        }
    });
});

$('.deleteOcorrencia').click(function(){
    var _token = $('#token').val();
    var matricula = $(this).data('matricula');
    $.ajax({
        method: "POST",
        url: "ocorrencia/remover",
        data: {_token: _token, matricula: matricula},
        success: function(response){
            console.log(response);
            pushMessage(response,function(type){
                if(type == 'success'){
                    $('i.deleteOcorrencia[data-matricula='+matricula+']').parent().parent().remove();//Delete row
                    if($('#bodyOcorrencia').children().length == 0){//Delete table
                        $('#bodyOcorrencia').parent().remove();
                    }
                }
            });
        }
    });
});

$('#updateRelatorio').click(function(){
    var _token = $('#token').val();
    $.ajax({
        method: "POST",
        url: "relatorio/update",
        data: {_token: _token},
        success: function(response){
            pushMessage(response);
        }
    });
});

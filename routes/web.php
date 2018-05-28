<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',function(){
    return redirect('/login');
});

Route::get('/horario', 'HorarioController@getAll')->name('horario');

Route::get('/teste', 'HorarioController@teste');

Route::post('/horario/adicionar', 'HorarioController@addHorario');

Route::get('/horario/{id}','HorarioController@getHorario')->where('id','[0-9]+');

Route::get('/horario/{id}/chamada','HorarioController@chamada')->where('id','[0-9]+')->name('chamada');

Route::get('/horario/{id}/relatorio','HorarioController@relatorio')->where('id','[0-9]+');

Route::get('/horario/{id}/ocorrencia','HorarioController@ocorrencia')->where('id','[0-9]+');

Route::post('/horario/{id}/ocorrencia/adicionar','HorarioController@addOcorrencia')->where('id','[0-9]+');

Route::post('/horario/{id}/ocorrencia/remover','HorarioController@removeOcorrencia')->where('id','[0-9]+');

Route::post('/horario/{id}/relatorio/salvar','HorarioController@salvaRelatorio')->where('id','[0-9]+');

Route::post('/horario/{id}/relatorio/update','HorarioController@updateRelatorio')->where('id','[0-9]+');

Route::match(['get', 'post'],'/horario/{id}/chamada/adicionar','HorarioController@incluirChamada')->where('id','[0-9]+');

Route::match(['get', 'post'],'/horario/{id}/chamada/remover','HorarioController@removerChamada')->where('id','[0-9]+');

Route::get('/aluno','AlunoController@getHome');

Route::post('/aluno/adicionar','AlunoController@addAluno');

Route::post('/aluno/mudar','AlunoController@mudarAluno');

Route::post('/aluno/buscar','AlunoController@buscarAluno');

Route::post('/horario/{id}/chamada/salvar','HorarioController@newChamada')->where('id','[0-9]+');

Route::get('/horario/{id}/conteudo','HorarioController@getConteudo')->where('id','[0-9]+');

Route::post('/horario/{id}/conteudo/remover','HorarioController@removeConteudo')->where('id','[0-9]+');

Route::get('home','HomeController@index');

Auth::routes();

Route::match(['get','post'], '/logout', 'Auth\LoginController@logout')->name('logout');

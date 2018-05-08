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

Route::get('/horario', 'HorarioController@getAll')->name('horario');

Route::post('/horario/adicionar', 'HorarioController@addHorario');

Route::get('/horario/{id}','HorarioController@getHorario')->where('id','[0-9]+');

Route::get('/horario/{id}/chamada','HorarioController@chamada')->where('id','[0-9]+')->name('chamada');

Route::get('/horario/{id}/relatorio','HorarioController@relatorio')->where('id','[0-9]+');

Route::get('/horario/{id}/relatorio/download','HorarioController@relatorioPDF')->where('id','[0-9]+');

Route::post('/horario/{id}/adicionar','HorarioController@incluirChamada')->where('id','[0-9]+');

Route::get('/aluno','AlunoController@getHome');

Route::post('/aluno/adicionar','AlunoController@addAluno');

Route::post('/aluno/buscar','AlunoController@buscarAluno');

Route::post('/horario/{id}/chamada/salvar','HorarioController@newChamada')->where('id','[0-9]+');

Route::get('home','HomeController@index');

Auth::routes();

Route::match(['get','post'], '/logout', 'Auth\LoginController@logout')->name('logout');

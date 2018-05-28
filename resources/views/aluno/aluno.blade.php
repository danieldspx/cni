@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ URL::asset('node_modules/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('node_modules/toastr/build/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/shared/material.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('node_modules/sweet-dropdown/dist/min/jquery.sweet-dropdown.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/aluno/aluno.css') }}">
@stop

@section('core')
    <div class="container-fluid">
        <div class="title">
            Dados Aluno
        </div>
        <div class="insidePanel">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="additional" id="additional" value="getCursos">
            <div class="row">
                <div class="input-field col-sm-2">
                    <input id="matricula" type="number" maxlength="20" autocomplete="off">
                    <label for="matricula" data-to="matricula">Matrícula</label>
                </div>
                <div class="input-field col-sm-2">
                    <input id="nascimento" type="text" maxlength="15" autocomplete="off">
                    <label for="nascimento" data-to="nascimento">Nascimento</label>
                </div>
                <div class="input-field col-sm-4">
                    <div class="md-radio md-radio-inline">
                        <input id="ativo" type="radio" name="situacao" value="1" checked>
                        <label for="ativo">Ativo</label>
                    </div>
                    <div class="md-radio md-radio-inline">
                        <input id="inativo" type="radio" value="0" name="situacao">
                        <label for="inativo">Inativo</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col-sm-4">
                    <input id="nome" type="text" maxlength="60" autocomplete="off">
                    <label for="nome" data-to="nome">Nome</label>
                    <div class="dropdown-menu dropdown-anchor-top-left dropdown-has-anchor dark" id="dropdown-alunos">
                    	<ul id="content-search">
                    	</ul>
                    </div>
                </div>
                <div class="input-field col-sm-4 col-md-2">
                    <input id="telefone" type="text" maxlength="20" autocomplete="nope">
                    <label for="telefone" data-to="telefone">Telefone</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col-sm-4 col-md-4">
                    <input id="nome_responsavel" type="text" maxlength="60" autocomplete="nope">
                    <label for="nome_responsavel" data-to="nome_responsavel">Nome Responsável</label>
                </div>
                <div class="input-field col-12 col-sm-4 col-md-2">
                    <input id="telefone_responsavel" type="text" maxlength="20" autocomplete="nope">
                    <label for="telefone_responsavel" data-to="telefone_responsavel">Telefone Responsável</label>
                </div>
                <div class="input-field col-12 col-sm-4 col-md-2">
                    <input id="celular_responsavel" type="text" maxlength="20" autocomplete="nope">
                    <label for="celular_responsavel" data-to="celular_responsavel">Celular Responsável</label>
                </div>
            </div>
            <div class="row" id="cursoWrapper">
            </div>
            <div class="row">
                <div class="input-field col-sm-11">
                    <button class="btn col-12 col-sm-3" id="addAlunoList">Salvar <i class="mdi mdi-account-plus"></i></button>
                    <button class="btn col-12 col-sm-3" id="searchAluno" style="background-color: #f39c12;">Buscar  <i class="mdi mdi-account-search"></i></button>
                    <button class="btn col-12 col-sm-3 red-accent-2" id="clearForm">Limpar <i class="mdi mdi-close"></i></button>
                </div>
            </div>
         </div>
        <div id="animatedModal">
            <div class="close-animatedModal" id="closebt-container">
                <img class="closebt" src="{{ URL::asset('img/closebt.svg') }}">
            </div>
            <div class="modal-container">
                <div class="wrapChange col-sm-8 col-md-8 col-lg-8 col-lg-offset-2">
                    <div class="insidePanel" id="insideModal">
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                        <input type="hidden" name="alunoChange" id="alunoChange" value="">
                        <input type="hidden" name="fromHorario" id="fromHorario" value="">
                        <div class="row diaSelect">
                            <select id="diaChange" class="col-sm-8">
                                <option value="" selected>Selecione o dia</option>
                                @foreach($dias as $dia)
                                    <option value="{{$dia->id}}">{{$dia->nome}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-field col-sm-11">
                        <button class="btn col-12 col-sm-5" id="changeAluno" disabled="true">Alterar <i class="mdi mdi-account-convert"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="{{ URL::asset('node_modules/toastr/build/toastr.min.js') }}"></script>
    <script src="{{ URL::asset('js/shared/material.js') }}"></script>
    <script src="{{ URL::asset('node_modules/sweet-dropdown/dist/min/jquery.sweet-dropdown.min.js') }}"></script>
    <script src="{{ URL::asset('node_modules/jquery-mask-plugin/dist/jquery.mask.min.js') }}"></script>
    <script src="{{ URL::asset('node_modules/animatedmodal/animatedModal.min.js') }}"></script>
    <script src="{{ URL::asset('js/aluno/aluno.js') }}"></script>
    <script src="{{ URL::asset('js/shared/searchAluno.js') }}"></script>
@stop

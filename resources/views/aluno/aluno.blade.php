@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ URL::asset('node_modules/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('node_modules/toastr/build/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/material.css') }}">
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
            <div class="row">
                <div class="input-field col-sm-2">
                    <input id="matricula" type="number" maxlength="20" autocomplete="off">
                    <label for="matricula" data-to="matricula">Matrícula</label>
                </div>
                <div class="input-field col-sm-2">
                    <input id="nascimento" type="text" maxlength="15" autocomplete="off">
                    <label for="nascimento" data-to="nascimento">Nascimento</label>
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
            </div>
            <div class="row">
                <div class="input-field col-sm-4">
                    <input id="telefone" type="text" maxlength="20" autocomplete="nope">
                    <label for="telefone" data-to="telefone">Telefone</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col-sm-4">
                    <input id="telefone_responsavel" type="text" maxlength="20" autocomplete="nope">
                    <label for="telefone_responsavel" data-to="telefone_responsavel">Telefone Responsável</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col-sm-4">
                    <input id="celular_responsavel" type="text" maxlength="20" autocomplete="nope">
                    <label for="celular_responsavel" data-to="celular_responsavel">Celular Responsável</label>
                </div>
            </div>
             <div class="row">
                 <div class="input-field col-sm-8">
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
                <div class="input-field col-sm-11">
                    <div class="btn col-sm-2" id="addAlunoList" style="margin-right: 10px">Salvar <i class="mdi mdi-account-plus"></i></div>
                    <div class="btn col-sm-2 red-accent-2" id="clearForm">Limpar <i class="mdi mdi-close"></i></div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col-sm-11">
                    <div class="btn col-sm-4" id="searchAluno" style="background-color: #f39c12;">Buscar  <i class="mdi mdi-account-search"></i></div>
                </div>
            </div>
         </div>
    </div>
@stop

@section('script')
    <script src="{{ URL::asset('node_modules/toastr/build/toastr.min.js') }}"></script>
    <script src="{{ URL::asset('js/material.js') }}"></script>
    <script src="{{ URL::asset('node_modules/sweet-dropdown/dist/min/jquery.sweet-dropdown.min.js') }}"></script>
    <script src="{{ URL::asset('node_modules/jquery-mask-plugin/dist/jquery.mask.min.js') }}"></script>
    <script src="{{ URL::asset('js/aluno/aluno.js') }}"></script>
    <script src="{{ URL::asset('js/searchAluno.js') }}"></script>
@stop

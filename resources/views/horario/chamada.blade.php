@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ URL::asset('css/material.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/horario/chamada.css') }}">
@stop

@section('navOptions')
    <a class="nav-item nav-link" id="chamadaLink" href="chamada"><i class="mdi mdi-school"></i> Chamada</a>
    <a class="nav-item nav-link" id="relatorioLink" href="relatorio"><i class="mdi mdi-clipboard-text"></i> Relatório</a>
    <a class="nav-item nav-link" id="curriculoLink" href="curriculo"><i class="mdi mdi-book-open-variant"></i> Currículo</a>
@stop

@section('core')

    <div class="container-fluid" id="core">
        <div class="row">
            <div class="newElement"><i class="mdi mdi-account"></i></div>
        </div>
        @if(count($alunos) == 0)
            <div class="row">
                <div class="notFound shadow col-sm-12" role="alert">
                    <i class="mdi mdi-emoticon-sad" style="color: #ff1744"></i> Nenhum aluno cadastrado.
                </div>
            </div>
        @else
            @foreach($alunos as $aluno)
                <div class="row">
                    @if(property_exists($aluno,'situacao'))
                    <div class="alunoContainer col-sm-12 shadow" data-situacao="{{$aluno->situacao}}" id="alunoItem{{$aluno->id}}">
                    @else
                    <div class="alunoContainer col-sm-12 shadow" id="alunoItem{{$aluno->id}}">
                    @endif
                        @if(property_exists($aluno,'situacao'))
                            @switch($aluno->situacao)
                                @case(0)
                                    <i class="mdi mdi-close-octagon"></i>
                                    @break;
                                @case(1)
                                    <i class="mdi mdi-approval"></i>
                                    @break;
                                @case(2)
                                    <i class="mdi mdi-alert-decagram"></i>
                                    @break;
                                @case(3)
                                    <i class="mdi mdi-alert-octagon"></i>
                                    @break;
                                @case(4)
                                    <i class="mdi mdi-alert"></i>
                                    @break;
                            @endswitch
                        @endif
                        @if($aluno->nascimento == 1)
                            <i class="mdi mdi-cake-variant" id="birthdayIcon"></i>
                        @endif
                        <div class="nomeAluno">{{str_limit($aluno->nome, $limit = 30, $end = '...')}}</div>
                        <div class="buttonsAluno" data-id="alunoItem{{$aluno->id}}">
                            <i class="mdi mdi-checkbox-marked-circle setPresenca"></i>
                            <i class="mdi mdi-close-circle setFalta"></i>
                            <i class="mdi mdi-plus-circle showOptions"></i>
                            <div class="options">
                                <i class="mdi mdi-account-convert setChange"></i>
                                <i class="mdi mdi-school setGraduated"></i>
                                <i class="mdi mdi-help-circle setUnknown"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        <div class="row">
            <button type="button" class="btn col-sm-3" id="saveChamada" style="margin-top: 30px;">Salvar Chamada <i class="mdi mdi-content-save"></i></button>
        </div>
    </div>
    <div class="addContainer">
       <div class="addPanel">
           <i class="mdi mdi-close-circle closePanel"></i>
           <div class="insidePanel">
               <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
               <div class="row">
                   <div class="input-field col-sm-10">
                       <input id="matricula" type="number" maxlength="20" autocomplete="off">
                       <label for="matricula" data-to="matricula">Matrícula</label>
                   </div>
               </div>
                <div class="row">
                    <div class="input-field col-sm-10">
                        <div class="btn col-sm-12" id="addAlunoHorario">Adicionar <i class="mdi mdi-account-plus"></i></div>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col-sm-10">
                        <div class="btn col-sm-12" id="removeAlunoHorario">Remover <i class="mdi mdi-account-plus"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="{{ URL::asset('js/material.js') }}"></script>
    <script src="{{ URL::asset('js/horario/chamada.js') }}"></script>
@stop

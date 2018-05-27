@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ URL::asset('node_modules/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('node_modules/toastr/build/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/shared/material.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/horario/ocorrencia.css') }}">
@stop

@section('navOptions')
    @include('horario.navOptions')
@stop

@section('core')

    <div class="container-fluid" id="core">
        @if(count($alunos) == 0)
            <div class="row">
                <div class="notFound shadow col-sm-12" role="alert">
                    <i class="mdi mdi-emoticon-sad" style="color: #ff1744"></i> Nenhum aluno cadastrado.
                </div>
            </div>
        @else
            @foreach($alunos as $aluno)
                <div class="row">
                    <div class="alunoContainer col-sm-12 shadow" id="alunoItem{{$aluno->id}}">
                        <div class="nomeAluno">{{str_limit($aluno->nome, $limit = 30, $end = '...')}}</div>
                        <div class="buttonsAluno" data-id="alunoItem{{$aluno->id}}">
                            <i class="mdi mdi-account-minus setOcorrencia"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="addContainer">
       <div class="addPanel">
           <i class="mdi mdi-close-circle closePanel"></i>
           <div class="insidePanel">
               <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
               <input type="hidden" id="idAluno" value="">
               <div class="row">
                   <div class="input-field col-sm-10">
                       <input id="nome" type="text" maxlength="60" autocomplete="off">
                       <label for="nome" data-to="nome">Nome</label>
                   </div>
                   <div class="input-field col-sm-10">
                       <input id="descricao" type="text" maxlength="500" autocomplete="off">
                       <label for="descricao" data-to="descricao">Descrição</label>
                   </div>
               </div>
                <div class="row">
                    <div class="input-field col-sm-10">
                        <div class="btn col-sm-12" id="addOcorrencia">Adicionar <i class="mdi mdi-plus-circle-outline"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="{{ URL::asset('node_modules/toastr/build/toastr.min.js') }}"></script>
    <script src="{{ URL::asset('js/shared/material.js') }}"></script>
    <script src="{{ URL::asset('js/horario/ocorrencia.js') }}"></script>
@stop

@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ URL::asset('css/material.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/horario/relatorio.css') }}">
@stop

@section('navOptions')
    <a class="nav-item nav-link" id="chamadaLink" href="chamada"><i class="mdi mdi-school"></i> Chamada</a>
    <a class="nav-item nav-link" id="relatorioLink" href="relatorio"><i class="mdi mdi-clipboard-text"></i> Relatório</a>
    <a class="nav-item nav-link" id="curriculoLink" href="curriculo"><i class="mdi mdi-book-open-variant"></i> Currículo</a>
@stop

@section('core')

    <div class="container-fluid" id="core">
        @if(!isset($alunos))
            <div class="row">
                <div class="notFound shadow col-sm-12" role="alert">
                    <i class="mdi mdi-emoticon-sad" style="color: #ff1744"></i> Nenhum relatório cadastrado.
                </div>
            </div>
        @else
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Matrícula</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Situação</th>
                    <th scope="col">Telefone</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alunos as $aluno)
                    @if($aluno->situacao != "Presente")
                        <tr class="table-danger">
                    @else
                        <tr>
                    @endif
                            <th scope="row">{{$aluno->matricula}}</th>
                            <td>{{$aluno->nome}}</td>
                            <td>{{$aluno->situacao}}</td>
                            <td>{{$aluno->telefone}}</td>
                        </tr>
                @endforeach
            </tbody>
        @endif
    </div>
@stop

@section('script')
    <script src="{{ URL::asset('js/horario/relatorio.js') }}"></script>
@stop

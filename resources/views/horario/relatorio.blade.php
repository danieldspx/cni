@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ URL::asset('css/material.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/horario/relatorio.css') }}">
@stop

@section('navOptions')
    @include('horario.navOptions')
@stop

@section('core')
    <div class="container-fluid" id="core">
        @if(!isset($conteudo))
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="wrapConteudo">
                <div class="titleConteudo">
                    <i class="mdi mdi-chair-school"></i> Conteúdo da Aula (Máx.: 500)
                </div>
                <div class="row">
                    <div class="input-field col-sm-5">
                        <input id="descricao" type="text" maxlength="500" autocomplete="off">
                        <label for="descricao" data-to="descricao">Conteúdo</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <button type="button" class="btn col-sm-3" id="saveRelatorio" style="margin-top: 30px; margin-left: 15px;">Salvar Relatório <i class="mdi mdi-content-save"></i></button>
            </div>
        @else
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr style="text-align: center">
                        <th colspan="5">Conteúdo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$conteudo->conteudo}}</td>
                    </tr>
                </tbody>
            </table>
        @endif
        @if(count($alunos) == 0)
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
                    <th scope="col">Celular</th>
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
                            <td>{{$aluno->celular}}</td>
                        </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        @if(count($alunosOcorrencia) == 0)
            <div class="row">
                <div class="notFound shadow col-sm-12" role="alert" style="margin-bottom: 50px;">
                    <i class="mdi mdi-emoticon-sad" style="color: #ff1744"></i> Nenhuma ocorrência cadastrada.
                </div>
            </div>
        @else
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr style="text-align: center">
                        <th colspan="5">Ocorrências</th>
                    </tr>
                    <tr>
                        <th scope="col">Matrícula</th>
                        <th scope="col">Nome</th>
                        <th scope="col">descricao</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Celular</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alunosOcorrencia as $aluno)
                            <tr>
                                <th scope="row">{{$aluno->matricula}}</th>
                                <td>{{$aluno->nome}}</td>
                                <td>{{$aluno->descricao}}</td>
                                <td>{{$aluno->telefone}}</td>
                                <td>{{$aluno->celular}}</td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if(isset($conteudo))
        <div class="row">
            <button type="button" class="btn col-sm-3" id="updateRelatorio">Atualizar Relatório <i class="mdi mdi-cloud-sync"></i></button>
        </div>
        @endif
    </div>
@stop

@section('script')
    <script src="{{ URL::asset('js/material.js') }}"></script>
    <script src="{{ URL::asset('js/horario/relatorio.js') }}"></script>
@stop

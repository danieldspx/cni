@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ URL::asset('css/shared/material.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/horario/relatorio.css') }}">
@stop

@section('navOptions')
    @include('horario.navOptions')
@stop

@section('core')
    <div class="container-fluid" id="core">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        @if(empty($alunos))
            <div class="row">
                <div class="notFound shadow col-sm-12" role="alert">
                    <i class="mdi mdi-emoticon-sad" style="color: #ff1744"></i> Nenhum relatório cadastrado.
                </div>
            </div>
        @else
            <div class="wrapConteudo">
            @if(empty($conteudo))
                <div class="titleConteudo">
                    <i class="mdi mdi-chair-school"></i> Conteúdo da Aula (Máx.: 500)
                </div>
                <div class="row">
                    <div class="input-field col-10 col-sm-7 col-md-5">
                        <input id="descricao" type="text" maxlength="500" autocomplete="off">
                        <label for="descricao" data-to="descricao">Conteúdo</label>
                    </div>
                </div>
                <div class="row">
                    <button type="button" class="btn col-sm-6 col-md-4 col-10" id="saveRelatorio">Salvar Relatório <i class="mdi mdi-content-save"></i></button>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr style="text-align: center">
                            <th colspan="5">Conteúdo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$conteudo->conteudo}} <i class="mdi mdi-delete-forever mdi-36px deleteConteudo" title="Delete o conteúdo da aula" id="{{$conteudo->id}}"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif
            </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        @if(!$isMobile)
                        <th scope="col">Matrícula</th>
                        @endif
                        <th scope="col">Nome</th>
                        <th scope="col">Situação</th>
                        @if(!$isMobile)
                        <th scope="col">Telefone</th>
                        <th scope="col">Celular</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($alunos as $aluno)
                        @if($aluno->situacao != "Presente")
                            <tr class="table-danger">
                        @else
                            <tr>
                        @endif
                                @if(!$isMobile)
                                <th scope="row">{{$aluno->matricula}}</th>
                                @endif
                                <td>{{$aluno->nome}}</td>
                                <td>{{$aluno->situacao}}</td>
                                @if(!$isMobile)
                                <td>{{$aluno->telefone}}</td>
                                <td>{{$aluno->celular}}</td>
                                @endif
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @if(count($alunosOcorrencia) == 0)
            <div class="row">
                <div class="notFound shadow col-sm-12" role="alert" style="margin-bottom: 50px;">
                    <i class="mdi mdi-emoticon-sad" style="color: #ff1744"></i> Nenhuma ocorrência cadastrada.
                </div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr style="text-align: center">
                        @if(!$isMobile)
                        <th colspan="6">Ocorrências</th>
                        @else
                        <th colspan="3">Ocorrências</th>
                        @endif
                    </tr>
                    <tr>
                        @if(!$isMobile)
                        <th scope="col">Matrícula</th>
                        @endif
                        <th scope="col">Nome</th>
                        <th scope="col">Descrição</th>
                        @if(!$isMobile)
                        <th scope="col">Telefone</th>
                        <th scope="col">Celular</th>
                        @endif
                        <th scope="col">Deletar</th>
                    </tr>
                </thead>
                <tbody id="bodyOcorrencia">
                    @foreach($alunosOcorrencia as $aluno)
                            <tr>
                                @if(!$isMobile)
                                <th scope="row">{{$aluno->matricula}}</th>
                                @endif
                                <td>{{$aluno->nome}}</td>
                                <td>{{$aluno->descricao}}</td>
                                @if(!$isMobile)
                                <td>{{$aluno->telefone}}</td>
                                <td>{{$aluno->celular}}</td>
                                @endif
                                <td><i class="mdi mdi-delete-forever mdi-36px deleteOcorrencia" data-matricula='{{$aluno->matricula}}'></i></td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @if(isset($conteudo))
        <div class="row">
            <button type="button" class="btn col-sm-6 col-md-4 col-10" id="updateRelatorio">Atualizar Relatório <i class="mdi mdi-cloud-sync"></i></button>
        </div>
        @endif
    </div>
@stop

@section('script')
    <script src="{{ URL::asset('js/shared/material.js') }}"></script>
    <script src="{{ URL::asset('js/horario/relatorio.js') }}"></script>
@stop

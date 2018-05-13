@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ URL::asset('css/material.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/horario/conteudo.css') }}">
@stop

@section('navOptions')
    @include('horario.navOptions')
@stop

@section('core')
    <div class="container-fluid" id="core">
        @if(count($conteudos) == 0)
            <div class="row">
                <div class="notFound shadow col-sm-12" role="alert">
                    <i class="mdi mdi-emoticon-sad" style="color: #ff1744"></i> Nenhum conteúdo matéria.
                </div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Conteúdo</th>
                        <th scope="col">Data</th>
                        <th scope="col">Professor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conteudos as $conteudo)
                            <tr>
                                <th scope="row">{{$conteudo->conteudo}}</th>
                                <td>{{$conteudo->data}}</td>
                                <td>{{$conteudo->name}}</td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
@stop

@section('script')
    <script src="{{ URL::asset('js/material.js') }}"></script>
    <script src="{{ URL::asset('js/horario/conteudo.js') }}"></script>
@stop

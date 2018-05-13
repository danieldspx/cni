@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ URL::asset('node_modules/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('node_modules/toastr/build/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/material.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/horario/horario.css') }}">
@stop

@section('core')
    <div class="container" id="horarioPlace">
        <div class="row">
            <div class="newElement"><i class="mdi mdi-plus"></i></div>
        </div>
        @if(count($horarios) == 0)
            <div class="notFound shadow" role="alert" id="noHorario">
               <i class="mdi mdi-emoticon-sad" style="color: #ff1744"></i> Nenhum horário cadastrado.
           </div>
        @else
            @foreach($horarios as $horario)
                <div class="row">
                    <a href="horario/{{$horario->id}}" class="linkHorario">
                        <div class="horarioContainer shadow col-sm-10">
                            <div class="horarioTitle"><i class="mdi mdi-checkbox-blank-circle labelHorario day{{$horario->dias_id}}"></i> {{$horario->materia}}</div>
                            <div class="descricao"><i class="mdi mdi-clock"></i> {{$horario->dia}} - {{$horario->start}} às {{$horario->end}}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
    <div class="addContainer">
        <div class="addPanel">
            <i class="mdi mdi-close-circle closePanel"></i>
            <div class="insidePanel">
                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                <div class="row" style="margin-bottom: 25px;">
                    <select id="materiaNH">
                        <option value="" selected>Selecione a matéria</option>
                        @foreach($materias as $materia)
                            <option value="{{$materia->id}}">{{$materia->nome}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <select id="diaNH" class="col-sm-8">
                        <option value="" selected>Selecione o dia</option>
                        @foreach($dias as $dia)
                            <option value="{{$dia->id}}">{{$dia->nome}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="input-field col-sm-3">
                        <input id="startNH" type="text" maxlength="5" autocomplete="off">
                        <label for="startNH" data-to="startNH">De</label>
                    </div>
                    <div class="input-field col-sm-3">
                        <input id="endNH" type="text" maxlength="5" autocomplete="off">
                        <label for="endNH" data-to="endNH">Até</label>
                    </div>
                </div>
                <div class="row">
                    <div class="btn col-sm-4 offset-sm-1 col-10 offset-1" id="addHorarioList" style="margin-bottom: 10px;">Adicionar <i class="mdi mdi-calendar-plus"></i></div>
                    <div class="btn col-sm-4 offset-sm-2 red-accent-2 col-10 offset-1" id="clearForm">Limpar <i class="mdi mdi-close"></i></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="{{ URL::asset('node_modules/toastr/build/toastr.min.js') }}"></script>
    <script src="{{ URL::asset('js/material.js') }}"></script>
    <script src="{{ URL::asset('js/horario/horario.js') }}"></script>
@stop

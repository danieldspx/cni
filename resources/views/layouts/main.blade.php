<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="sortcut icon" href="{{ URL::asset('img/favicon/icon.png') }}" type="image/png">
        <title>CNI</title>
        <!-- Normalize CSS -->
        <link rel="stylesheet" href="{{ URL::asset('css/normalize.css') }}">
        <!-- Material Design Icons -->
        <link rel="stylesheet" href="{{ URL::asset('node_modules/mdi/css/materialdesignicons.min.css') }}">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <!-- Animate CSS -->
        <link rel="stylesheet" href="{{ URL::asset('node_modules/animate.css/animate.min.css') }}">
        <!-- Toast -->
        <link rel="stylesheet" href="{{ URL::asset('node_modules/toastr/build/toastr.min.css') }}">
        <!-- Main CSS -->
        <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
        @yield('head')
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-info">
            <a class="navbar-brand" href="horario" style="font-weight: bold;">CNI</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    @yield('navOptions')
                    <a class="nav-item nav-link" href="/horario" id="horarioLink"><i class="mdi mdi-clock"></i> Horários</a>
                    <a class="nav-item nav-link" href="/aluno" id="alunoLink"><i class="mdi mdi-account-group"></i> Alunos</a>
                    <a class="nav-item nav-link" href="/logout"><i class="mdi mdi-logout-variant"></i> Sair</a>
                </div>
            </div>
        </nav>
        @yield('core')
        <!-- jQuery -->
        <script
  src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <!-- Popper for Bootstrap -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <!-- Bootstrap plugin -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
        <!-- Toast Notification -->
        <script src="{{ URL::asset('node_modules/toastr/build/toastr.min.js') }}"></script>
        @yield('script')
    </body>
</html>

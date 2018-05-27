<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="sortcut icon" href="{{ URL::asset('img/favicon/icon.png') }}" type="image/png">
        <title>CNI</title>
        <!-- Normalize CSS -->
        <link rel="stylesheet" href="{{ URL::asset('css/shared/normalize.css') }}">
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
        <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-info">
            <a class="navbar-brand" href="/horario" style="font-weight: bold;">CNI</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    @yield('navOptions')
                    <li class="nav-item"><a class="nav-link" href="/horario" id="horarioLink"><i class="mdi mdi-clock"></i> Horários</a></li>
                    <li class="nav-item"><a class="nav-link" href="/aluno" id="alunoLink"><i class="mdi mdi-account-group"></i> Alunos</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout"><i class="mdi mdi-logout-variant"></i> Sair</a></li>
                </ul>
            </div>
        </nav>
        @yield('core')
        <!-- jQuery -->
        <script src="{{ URL::asset('node_modules/jquery/dist/jquery.min.js') }}"></script>
        <!-- Popper for Bootstrap -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <!-- Bootstrap plugin -->
        <script src="{{ URL::asset('node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <!-- Toast Notification -->
        <script src="{{ URL::asset('node_modules/toastr/build/toastr.min.js') }}"></script>
        <!-- Toast Notification Handler-->
        <script src="{{ URL::asset('js//shared/toastNotifications.js') }}"></script>
        @yield('script')
    </body>
</html>

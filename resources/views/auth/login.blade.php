<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="sortcut icon" href="{{ URL::asset('img/favicon/icon.png') }}" type="image/png">
    <title>CNI</title>
    <!-- Normalize CSS -->
    <link rel="stylesheet" href="{{ URL::asset('css/shared/normalize.css') }}">
    <!-- Material Design Icons -->
    <link rel="stylesheet" href="{{ URL::asset('node_modules/mdi/css/materialdesignicons.min.css') }}">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="{{ URL::asset('node_modules/animate.css/animate.min.css') }}">
    <!-- Materialize -->
    <link rel="stylesheet"  href="{{ URL::asset('node_modules/materialize-css/dist/css/materialize.min.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ URL::asset('css/login/login.css') }}">
    <!-- Toast -->
    <link rel="stylesheet" href="{{ URL::asset('node_modules/toastr/build/toastr.min.css') }}">
</head>
<body>
    <div class="row center">
        <h1 class="titulo">CNI</h1>
        <h2 class="subtitulo">Centro de Formação Profissional</h2>
    </div>
    <div class="login">
        <div class="row">
            <form action="{{ route('login') }}" name="login_form" class="col s12 login-form" method="post" id="formLogin">
                @csrf
                <div class="input-field col s10 offset-s1">
                    <i class="material-icons prefix mdi mdi-email"></i>
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                    <label for="email">E-mail</label>
                </div>
                <div class="input-field col s10 offset-s1">
                    <i class="material-icons prefix mdi mdi-lock"></i>
                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                    <label for="password">Senha</label>
                </div>
            </form >
            <span class="center">
                <button class="waves-effect waves-light btn green accent-2" id="btnLogin">Entrar <i class="material-icons middle mdi mdi-logout-variant"></i></button>
            </span>
        </div>
    </div>
    <div class="row copyright">
        <i class="mdi mdi-copyright"></i> Daniel dos Santos Pereira - 2018
    </div>
    <!-- jQuery -->
    <script src="{{ URL::asset('node_modules/jquery/dist/jquery.min.js') }}"></script>
    <!-- Materialize -->
    <script src="{{ URL::asset('node_modules/materialize-css/dist/js/materialize.min.js') }}"></script>
    <!-- Toast Notification -->
    <script src="{{ URL::asset('node_modules/toastr/build/toastr.min.js') }}"></script>
    <!-- Login Script -->
    <script type="text/javascript">
        function login(){
            var email = $("#email").val();
            var password = $("#password").val();
            if(password != "" && email != "" && email.search('@') != -1){
                $("#formLogin").submit();
            } else {
                toastr.warning('Digite o e-mail e a senha válidos');
            }
        }
        $(document).ready(function(){
            toastr.options.progressBar = true;
            toastr.options.preventDuplicates = true;
            toastr.options.positionClass = "toast-top-right";
            $("#btnLogin").click(login);

            $("#password").keypress(function(e) {
                if(e.which == 13) {
                    e.preventDefault();
                    login();
                }
            });

            @if(count($errors) != 0)
                toastr.error("{{$errors->first('msg')}}");
            @endif
        });
    </script>
</body>
</html>

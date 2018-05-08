<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>CNI</title>
        <style>
            .notFound{
                padding: 15px 50px;
                text-align: left;
                background-color: white;
                border-radius: 8px;
                font-size: 40px;
                font-weight: bold;
                margin-top: 30px;
            }
            body{
                font-family: Arial, sans-serif;
                text-align: left;
            }
            .thead-dark{
                background-color: #212529;
                color: white;
            }
            th,td{
                padding: 15px 30px 15px 15px;
            }
            .table{
                width: 90%;
                position: relative;
                left: 5%;
                border-collapse: collapse;
            }
            .thead-dark th{
                border: 0.5px solid #32383e;
            }

            .table-bordered td{
                border: 1px solid #dee2e6;
            }
            table, th, td {
                border: 0.5px solid #32383e;
            }
            .table-stripped>tbody tr:nth-child(even){
                background-color: #efefef;
            }
            .table-danger{
                background-color: #f5c6cb !important;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid" style="margin-top: 50px">
            @if(!isset($alunos))
                <div class="row">
                    <div class="notFound shadow col-sm-12" role="alert">
                        Nenhum relatório cadastrado.
                    </div>
                </div>
            @else
            <table class="table table-bordered table-stripped">
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
                                <td>{{$aluno->matricula}}</th>
                                <td>{{$aluno->nome}}</td>
                                <td>{{$aluno->situacao}}</td>
                                <td>{{$aluno->telefone}}</td>
                            </tr>
                    @endforeach
                </tbody>
            @endif
        </div>
    </body>
</html>

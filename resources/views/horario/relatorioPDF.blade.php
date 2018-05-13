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
                width: 95%;
                position: relative;
                left: 2.5%;
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
            #ocorrencias{
                margin-top: 50px;
            }
            #conteudo{
                margin-top: 50px;
            }
            .cabecalho{
                font-family: Arial,sans-serif;
                font-weight: bold;
                font-size: 20px;
                padding-left: 2.5%;
                margin-bottom: 50px;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid" style="margin-top: 50px">
            <div class="cabecalho">
                <div class="row">Dia: {{$dia}} <br> Horário: {{$horario}}</div>
                <div class="row">Curso: {{$nomeMateria}} <br> Professor: {{$nomeProfessor}}</div>
            </div>
            @if(empty($alunos))
                <div class="row">
                    <div class="notFound shadow col-sm-12" role="alert">
                        Nenhum relatório cadastrado.
                    </div>
                </div>
            @else
            <table class="table table-bordered table-stripped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">CTR</th>
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
                                <td>{{$aluno->matricula}}</th>
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
                    <div class="notFound shadow col-sm-12" role="alert">
                        <i class="mdi mdi-emoticon-sad" style="color: #ff1744"></i> Nenhuma ocorrência cadastrada.
                    </div>
                </div>
            @else
                <table class="table table-bordered table-hover" id="ocorrencias">
                    <thead class="thead-dark">
                        <tr style="text-align: center">
                            <th colspan="5">Ocorrências</th>
                        </tr>
                        <tr>
                            <th scope="col">CTR</th>
                            <th scope="col">Nome</th>
                            <th scope="col">descricao</th>
                            <th scope="col">Telefone</th>
                            <th scope="col">Celular</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alunosOcorrencia as $aluno)
                                <tr>
                                    <td>{{$aluno->matricula}}</td>
                                    <td>{{$aluno->nome}}</td>
                                    <td>{{$aluno->descricao}}</td>
                                    <td>{{$aluno->telefone}}</td>
                                    <td>{{$aluno->celular}}</td>
                                </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            <table class="table table-bordered table-hover" id="conteudo">
                <thead class="thead-dark">
                    <tr style="text-align: center">
                        <th>Conteúdo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$conteudo}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>

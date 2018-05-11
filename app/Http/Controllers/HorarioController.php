<?php

namespace cni\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Request;
use cni\Horario;
use cni\Materia;
use cni\Dia;
use cni\Aluno;
use cni\Relatorio;
use Carbon\Carbon;
use PDF;
use Auth;
/**
 *
 */
class HorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('autorizacao');
    }

    public function getAll()
    {
        try {
            $horarios = DB::table('horarios')
                ->join('dias', 'dias.id', '=', 'dias_id')
                ->join('materias', 'materias.id', '=', 'materias_id')
                ->select('horarios.id AS id', 'materias.nome AS materia', 'dias.nome AS dia', DB::raw("DATE_FORMAT(end,'%H:%i') end"), DB::raw("DATE_FORMAT(start,'%H:%i') start"))
                ->orderBy('dias.id')
                ->orderBy('start')
                ->get(); //Gets the classes info.
        } catch (\Exception $e) {
            $horarios = array();
        }

        $materias = Materia::all();
        $dias = Dia::all();
        return view('horario.horario')->with(compact('horarios','materias','dias'));
    }

    public function addHorario()
    {
        try {
            $params = json_decode(Request::input('data'),true); //JSON to ARRAY
            $horario = new Horario($params);
            $horario->save();
            $data['code'] = "200";
            $data['id'] = $horario->id;
        } catch (\Exception $e) {
            $data['code'] = "200";
        }
        return json_encode($data);
    }

    public function getHorario($id)
    {
        // Esta função redireciona para /aluno para executarmos algumas função
        return redirect()->route('chamada',['id' => $id]);
    }

    public function chamada($id)//View chamada
    {
        $todayComplete = Carbon::now()->format('d/m/Y');

        $alunos = DB::select("SELECT alunos.id, alunos.nome, alunos.nascimento FROM alunos INNER JOIN horarios_has_alunos ON horarios_id = :id WHERE alunos.id = horarios_has_alunos.alunos_id AND alunos.situacao = 1 ORDER BY alunos.nome ASC",['id' => $id]); //Todos os alunos

        try {
            $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();
            $alunosRelatorio = DB::table('relatorios_has_alunos') //Alunos que estao no relatorio
            ->where('relatorios_id',$todaysRelatorio->id)
            ->select('situacoes_id AS situacao','alunos_id AS id')->get();

            $isRelatorio = true;
        } catch (\Exception $e) {
            $isRelatorio = false;
        }

        $today['day'] = Carbon::now()->format('d');
        $today['month'] = Carbon::now()->format('m');
        $today['year'] = Carbon::now()->format('Y');

        $contador = 0;
        foreach ($alunos as $key => $aluno){
            if ($isRelatorio == true) {
                for ($i=0; $i < count($alunosRelatorio); $i++) {
                    if ($alunosRelatorio[$i]->id == $aluno->id) {
                        $dadoAluno['id'] = $aluno->id;
                        $dadoAluno['nome'] = $aluno->nome;
                        $dadoAluno['nascimento'] = $aluno->nascimento;
                        $dadoAluno['situacao'] = $alunosRelatorio[$i]->situacao;
                        $alunos[$key] = (object)$dadoAluno; //Replace with Situacao
                    }
                }
            }

            $dayBirth = explode("-",$aluno->nascimento)[2];
            $monthBirth = explode("-",$aluno->nascimento)[1];

            $aluno->nascimento = 0;//Não faz aniversário

            if ($dayBirth == $today['day'] && $monthBirth == $today['month']) {
                $aluno->nascimento = 1;//Faz aniversário
            } else if($dayBirth==29 && $monthBirth==2){ //Nasceu em 29 de Fevereiro
                if(($today['year']%400)!=0){//Não Bissexto
                    if(($today['year']%4)!=0 || ($today['year']%100)==0){ //Não Bissexto
                        if($today['day']==28 && $today['month']==2){ //28 é o ultimo dia de Fevereiro nesse ano
                            $aluno->nascimento = 1;//Faz aniversário
                        }
                    }
                }
            }
        }
        return view('horario.chamada')->with(compact('alunos','id'));
    }

    public function incluirChamada($id) //Adiciona aluno na chamada
    {
        $dados = Request::only(['matricula','horario']); //Get only matricula and horario
        if($id != $dados['horario']){
            return 409; //Conflito de informações
        } else { //Good to go
            try { //Try to find Aluno
                $aluno = Aluno::where('matricula', $dados['matricula'])->firstOrFail();
                try { //Try to insert Aluno into the Class
                    DB::table('horarios_has_alunos')->insert(
                        ['horarios_id' => $id, 'alunos_id' => $aluno->id]
                    );
                    return 200; //Adicionado com sucesso
                } catch (\Exception $e) { //Not possible to insert
                    return 406; //Erro ao adicionar o aluno.
                }

            } catch (\Exception $e) { //Aluno not found
                return 404; //Aluno nao encontrado
            }
        }
    }

    public function newChamada($id)//Cria um novo relatorio
    {
        $dataHoje = Carbon::now()->format('Y-m-d');

        $dados = Request::only(['dataAlunos','horario']); //Get only matricula and horario
        if($id == $dados['horario']){
            $alunos = json_decode($dados['dataAlunos'],true);
        } else {
            return 409; //Conflito de informações
        }
        try {//Testa se existe relatorio
            $relatorio = Relatorio::where([
                ['data',$dataHoje],
                ['horarios_id',$id]
            ])->firstOrFail();
        } catch (\Exception $e) { //Nenhum relatorio
            try {
                $params['data'] = $dataHoje;
                $params['horarios_id'] = $id;
                $relatorio = new Relatorio($params);
                $relatorio->save();
            } catch (\Exception $e) {
                return 406; //Relatorio não criado
            }
        }

        foreach ($alunos as $aluno) {//Save alunos into relatorios_has_alunos
            try { //Try update
                $existsInDB = DB::table('relatorios_has_alunos')->where('alunos_id',$aluno['alunos_id'])->where('relatorios_id',$relatorio->id)->first();//Test if aluno is in DB
                if($existsInDB==null){
                    throw new \Exception("Nenhum aluno para atualizar", 1);
                } else {
                    DB::table('relatorios_has_alunos')->where('alunos_id',$aluno['alunos_id'])->where('relatorios_id',$relatorio->id)->update($aluno);//Update
                }
            } catch (\Exception $e) {
                try {//Try insert
                    $aluno['relatorios_id'] = $relatorio->id;
                    DB::table('relatorios_has_alunos')->insert($aluno);
                } catch (\Exception $e) {
                    return 417; //Error INSERT and UPDATE
                }
            }
        }
        return 200;
    }

    public function relatorio($id)
    {
        $todayComplete = Carbon::now()->format('Y-m-d');
        try {
            $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();
            $alunos = DB::select("SELECT alunos.matricula, alunos.telefone_responsavel AS telefone, alunos.celular_responsavel AS celular, alunos.nome, alunos.nascimento, situacoes.nome AS situacao FROM alunos INNER JOIN horarios_has_alunos ON horarios_id = :idHorario INNER JOIN relatorios_has_alunos ON relatorios_has_alunos.relatorios_id = :idRelatorio INNER JOIN situacoes ON situacoes.id = relatorios_has_alunos.situacoes_id WHERE alunos.id = horarios_has_alunos.alunos_id AND alunos.id = relatorios_has_alunos.alunos_id AND alunos.situacao = 1 ORDER By alunos.nome ASC",['idHorario' => $id,'idRelatorio'=>$todaysRelatorio->id]);
        } catch (\Exception $e) {
            $alunos = null;
        }
        try {
            $data = Carbon::now()->format('Y-m-d');
            $alunosOcorrencia = DB::table('ocorrencias')
            ->select('alunos.nome','alunos.matricula','ocorrencias.descricao','alunos.telefone_responsavel AS telefone', 'alunos.celular_responsavel AS celular')
            ->join('alunos','alunos.id','=','ocorrencias.alunos_id')
            ->where('ocorrencias.horarios_id',$id)
            ->where('ocorrencias.data',$data)
            ->where('alunos.situacao',1)
            ->orderBy('alunos.nome','ASC')->get();
        } catch (\Exception $e) {
            $alunosOcorrencia = null;
        }

        return view('horario.relatorio',compact('alunos','alunosOcorrencia'));
    }

    public function relatorioPDF($id)
    {
        $todayComplete = Carbon::now()->format('Y-m-d');
        $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();

        $alunos = DB::select("SELECT alunos.matricula,  alunos.telefone_responsavel AS telefone, alunos.celular_responsavel AS celular, alunos.nome, alunos.nascimento, situacoes.nome AS situacao FROM alunos INNER JOIN horarios_has_alunos ON horarios_id = :idHorario INNER JOIN relatorios_has_alunos ON relatorios_has_alunos.relatorios_id = :idRelatorio INNER JOIN situacoes ON situacoes.id = relatorios_has_alunos.situacoes_id WHERE alunos.id = horarios_has_alunos.alunos_id AND alunos.id = relatorios_has_alunos.alunos_id AND alunos.situacao = 1 ORDER By alunos.nome ASC",['idHorario' => $id,'idRelatorio'=>$todaysRelatorio->id]);

        try {
            $data = Carbon::now()->format('Y-m-d');
            $alunosOcorrencia = DB::table('ocorrencias')
            ->select('alunos.nome','alunos.matricula','ocorrencias.descricao','alunos.telefone_responsavel AS telefone', 'alunos.celular_responsavel AS celular')
            ->join('alunos','alunos.id','=','ocorrencias.alunos_id')
            ->where('ocorrencias.horarios_id',$id)
            ->where('ocorrencias.data',$data)
            ->where('alunos.situacao',1)
            ->orderBy('alunos.nome','ASC')->get();
        } catch (\Exception $e) {
            $alunosOcorrencia = null;
        }
        $dia = Carbon::now()->dayOfWeek;
        switch ($dia){
            case 1:
                $dia = 'Segunda-feira';
                break;
            case 2:
                $dia = 'Terca-feira';
                break;
            case 3:
                $dia = 'Quarta-feira';
                break;
            case 4:
                $dia = 'Quinta-feira';
                break;
            case 5:
                $dia = 'Sexta-feira';
                break;
            case 6:
                $dia = 'Sabado';
                break;
            default:
                $dia = 'Domingo';
                break;
        }
        $materia = 'Manut. de Micro';
        $horario = "13:00 às 14:30";
        $nomeProfessor = Auth::user()->name;
        $pdf = PDF::loadView('horario.relatorioPDF',compact('alunos','alunosOcorrencia','nomeProfessor','dia','materia','horario'));
        $today = Carbon::now()->format('d_m_Y');
        $namePDF = "relatorio-$today.pdf";
        return $pdf->stream($namePDF);
    }

    public function ocorrencia($id)
    {
        $alunos = DB::select("SELECT alunos.id, alunos.nome, alunos.nascimento FROM alunos INNER JOIN horarios_has_alunos ON horarios_id = :id WHERE alunos.id = horarios_has_alunos.alunos_id AND alunos.situacao = 1 ORDER BY alunos.nome ASC",['id' => $id]); //Todos os alunos

        return view('horario.ocorrencia')->with(compact('alunos'));
    }

    public function addOcorrencia($id)
    {
        try {
            $data = Request::only(['alunos_id','descricao']);
            $data['horarios_id'] = $id;
            $data['professores_id'] = Auth::user()->id;//Professor
            $data['data'] = Carbon::now()->format('Y-m-d');
            DB::table('ocorrencias')->insert($data);
            return 200;
        } catch (\Exception $e) {
            return 406;
        }
    }

    public function salvaRelatorio($id)
    {
        $dia = Carbon::now()->dayOfWeek;
        switch ($dia){
            case 1:
                $dia = 'Segunda-feira';
                break;
            case 2:
                $dia = 'Terca-feira';
                break;
            case 3:
                $dia = 'Quarta-feira';
                break;
            case 4:
                $dia = 'Quinta-feira';
                break;
            case 5:
                $dia = 'Sexta-feira';
                break;
            case 6:
                $dia = 'Sabado';
                break;
            default:
                $dia = 'Domingo';
                break;
        }
        $data = Carbon::now()->format('d-m-Y');
        $nomeRelatorio = "relatorio_".$data;
        try {
            $dados = Request::only('conteudo');
            $dados['data'] = Carbon::now()->format('Y-m-d');
            $dados['professores_id'] = Auth::user()->id;
            $dados['horarios_id'] = $id;
            DB::table('conteudo')->insert($data);

            try { //Try to get some information
                $info = DB::table('horarios')
                ->select('materias.nome', DB::raw("DATE_FORMAT(start,'%H:%i') start"), DB::raw("DATE_FORMAT(end,'%H:%i') end"))
                ->join('materias','materias.id','=','horarios.materias_id')
                ->whereRaw("horarios.id = :id",['id'=>$id])
                ->first();
            } catch (\Exception $e) {
                return 406;
            }

            $nomeMateria = $info->nome;
            $de = $info->start;
            $ate = $info->end;
            $path = "/var/www/html/cni/relatorios/$dia/$nomeMateria/$de - $ate/";

            try { //Try to create the directory
                $result = File::makeDirectory($path,0777,true);
            } catch (\Exception $e) {
                return 403;
            }

            try { //Try to save PDF
                $todayComplete = Carbon::now()->format('Y-m-d');
                $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();

                $alunos = DB::select("SELECT alunos.matricula,  alunos.telefone_responsavel AS telefone, alunos.celular_responsavel AS celular, alunos.nome, alunos.nascimento, situacoes.nome AS situacao FROM alunos INNER JOIN horarios_has_alunos ON horarios_id = :idHorario INNER JOIN relatorios_has_alunos ON relatorios_has_alunos.relatorios_id = :idRelatorio INNER JOIN situacoes ON situacoes.id = relatorios_has_alunos.situacoes_id WHERE alunos.id = horarios_has_alunos.alunos_id AND alunos.id = relatorios_has_alunos.alunos_id AND alunos.situacao = 1 ORDER By alunos.nome ASC",['idHorario' => $id,'idRelatorio'=>$todaysRelatorio->id]);

                try {
                    $data = Carbon::now()->format('Y-m-d');
                    $alunosOcorrencia = DB::table('ocorrencias')
                    ->select('alunos.nome','alunos.matricula','ocorrencias.descricao','alunos.telefone_responsavel AS telefone', 'alunos.celular_responsavel AS celular')
                    ->join('alunos','alunos.id','=','ocorrencias.alunos_id')
                    ->where('ocorrencias.horarios_id',$id)
                    ->where('ocorrencias.data',$data)
                    ->where('alunos.situacao',1)
                    ->orderBy('alunos.nome','ASC')->get();
                } catch (\Exception $e) {
                    $alunosOcorrencia = null;
                }
                $conteudo = $dados['conteudo'];
                $nomeProfessor = Auth::user()->name;
                $horario = $de." às ".ate;
                $pdf = PDF::loadView('horario.relatorioPDF',compact('alunos','alunosOcorrencia','nomeProfessor','dia','nomeMateria','horario','conteudo'));
                $pdf->save($path.$nomeRelatorio);
                return 200;
            } catch (\Exception $e) {
                return 406;
            }


        } catch (\Exception $e) {
            return 206;
        }
    }
}

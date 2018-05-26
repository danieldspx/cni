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
use cni\Professor;
use Carbon\Carbon;
use PDF;
use Auth;
use Jenssegers\Agent\Agent;//Detect Mobile
/**
 *
 */
class HorarioController extends Controller
{
    private $agent, $today;
    public function __construct()
    {
        $this->agent = new Agent();
        $this->today = Carbon::now()->setTimezone('America/Sao_Paulo');
	      $this->middleware('autorizacao');
    }

    public function getAll(){
		    $dia = $this->today->dayOfWeek;
	      $hora = $this->today->format('H:i');
        if($dia==0){
            $dia = 1;
        }
        try {
            $search = DB::table('horarios')
                      ->join('dias', 'dias.id', '=', 'dias_id')
                      ->join('materias', 'materias.id', '=', 'materias_id')
                      ->select('horarios.id AS id', 'materias.nome AS materia', 'dias.nome AS dia','horarios.dias_id', DB::raw("DATE_FORMAT(end,'%H:%i') end"), DB::raw("DATE_FORMAT(start,'%H:%i') start"))
                      ->orderBy('dias.id')
                      ->orderBy('start')
                      ->orderBy('materias.nome');
    				if(Request::input('filtro')!='all'){
                $search->where('dias_id',$dia)
                       ->where('horarios.end','>=',$hora);
      					if(Auth::user()->access == 3){//Informatica
      							$search->whereBetween('materias.id', [1, 7]); //Gets the classes info.
      					} else if(Auth::user()->access == 4){ //Ingles
      							$search->where('materias.id',8);
      					} else if(Auth::user()->access == 5){//Gestao
                    $search->where('materias.id',9);
                }
    				}
            $horarios = $search->get();
        } catch (\Exception $e) {
            $horarios = array();
        }
        $materias = Materia::all();
        $dias = Dia::all();
        return view('horario.horario')->with(compact('horarios','materias','dias'));
    }

    public function checkConflict($matricula,$id){
        try {
            $horario = Horario::where('materias_id',$id)->firstOrFail();
            try {
                $aluno = Aluno::where('matricula',$matricula)->firstOrFail();
                $horariosCadastrado = DB::table('horarios_has_alunos')
                ->select(DB::raw("DATE_FORMAT(horarios.end,'%H:%i') end"), DB::raw("DATE_FORMAT(horarios.start,'%H:%i') start"))
                ->join('horarios','horarios.id','=','horarios_id')
                ->where('alunos_id',$aluno->id)->get();
                $gtg = true;
            if(count($horariosCadastrado) != 0){//Horarios Cadastrados
                foreach ($horariosCadastrado as $horarioCadastrado) {
                    $inicioC = $horarioCadastrado->start;
                    $fimC = $horarioCadastrado->end;
                    $inicio = $horario->start;
                    $fim = $horario->end;
                    if(($inicio > $inicioC && $inicio < $fimC) || ($fim > $inicioC && $fim < $fimC)){//Conflict
                        return false;
                    }
                }
                if($gtg){//Good to go
                    return true;
                }
            } else {//Good to go
                return true;
            }

            } catch (\Exception $e) {//Aluno not found
                return false;
            }
        } catch (\Exception $e) {//Horario not found
            return false;
        }
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
        $todayComplete = $this->today->format('Y-m-d');
        $info = DB::table('horarios')
        ->select('materias.nome', DB::raw("DATE_FORMAT(start,'%H:%i') start"), DB::raw("DATE_FORMAT(end,'%H:%i') end"))
        ->join('materias','materias.id','=','horarios.materias_id')
        ->whereRaw("horarios.id = :id",['id'=>$id])
        ->first();
        $alunos = DB::select("SELECT alunos.id, alunos.nome, alunos.matricula, alunos.nascimento FROM alunos INNER JOIN horarios_has_alunos ON horarios_id = :id WHERE alunos.id = horarios_has_alunos.alunos_id AND alunos.situacao = 1 ORDER BY alunos.nome ASC",['id' => $id]); //Todos os alunos
        try {
            $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();
            $alunosRelatorio = DB::table('relatorios_has_alunos') //Alunos que estao no relatorio
            ->where('relatorios_id',$todaysRelatorio->id)
            ->select('situacoes_id AS situacao','alunos_id AS id')->get();
            $isRelatorio = true;
        } catch (\Exception $e) {
            $isRelatorio = false;
        }
        $today['day'] = $this->today->format('d');
        $today['month'] = $this->today->format('m');
        $today['year'] = $this->today->format('Y');

        $contador = 0;
        foreach ($alunos as $key => $aluno){
            if ($isRelatorio == true) {
                for ($i=0; $i < count($alunosRelatorio); $i++) {
                    if ($alunosRelatorio[$i]->id == $aluno->id) {
                        $dadoAluno['id'] = $aluno->id;
                        $dadoAluno['nome'] = $aluno->nome;
                        $dadoAluno['nascimento'] = $aluno->nascimento;
                        $dadoAluno['situacao'] = $alunosRelatorio[$i]->situacao;
                        $dadoAluno['matricula'] = $aluno->matricula;
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
        return view('horario.chamada')->with(compact('alunos','id','info'));
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

    public function removerChamada($id) //Remove aluno da chamada
    {
        $dados = Request::only(['matricula','horario']); //Get only matricula and horario
        if($id != $dados['horario']){
            return 409; //Conflito de informações
        } else { //Good to go
            try { //Try to remove Aluno from chamada
                $aluno = Aluno::where('matricula', $dados['matricula'])->firstOrFail();
                try { //Try to remove Aluno
                    DB::table('horarios_has_alunos')
                    ->where('horarios_id',$id)
                    ->where('alunos_id',$aluno->id)
                    ->delete();
                    return 200; //Deletado com sucesso
                } catch (\Exception $e) { //Not possible to insert
                    return 406; //Erro ao remover o aluno.
                }

            } catch (\Exception $e) { //Aluno not found
                return 404; //Aluno nao encontrado
            }
        }
    }

    public function newChamada($id)//Cria um novo relatorio
    {
        $dataHoje = $this->today->format('Y-m-d');

        $dados = Request::only(['dataAlunos','horario']); //Get only matricula and horario
        if($id == $dados['horario']){
            $alunos = json_decode($dados['dataAlunos'],true);
        } else {
            return 409; //Conflito de informações
        }
        try {//Testa se existe relatorio
            $relatorioExist = true;
            $relatorio = Relatorio::where([
                ['data',$dataHoje],
                ['horarios_id',$id]
            ])->firstOrFail();
        } catch (\Exception $e) { //Nenhum relatorio
            try {
                $params['data'] = $dataHoje;
                $params['horarios_id'] = $id;
				$params['professores_id'] = Auth::user()->id;//Professor
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
        //if($relatorioExist){//Se já havia relatorio então atualiza o PDF
        //    updateRelatorio($id);
        //}
        return 200;
    }

    public function relatorio($id)
    {
        $todayComplete = $this->today->format('Y-m-d');
        try {
            $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();
            $alunos = DB::select("SELECT alunos.matricula, alunos.telefone_responsavel AS telefone, alunos.celular_responsavel AS celular, alunos.nome, alunos.nascimento, situacoes.nome AS situacao FROM alunos INNER JOIN horarios_has_alunos ON horarios_id = :idHorario INNER JOIN relatorios_has_alunos ON relatorios_has_alunos.relatorios_id = :idRelatorio INNER JOIN situacoes ON situacoes.id = relatorios_has_alunos.situacoes_id WHERE alunos.id = horarios_has_alunos.alunos_id AND alunos.id = relatorios_has_alunos.alunos_id AND alunos.situacao = 1 ORDER By alunos.nome ASC",['idHorario' => $id,'idRelatorio'=>$todaysRelatorio->id]);
        } catch (\Exception $e) {
            $alunos = null;
        }
        try {
            $alunosOcorrencia = DB::table('ocorrencias')
            ->select('alunos.nome','alunos.matricula','ocorrencias.descricao','alunos.telefone_responsavel AS telefone', 'alunos.celular_responsavel AS celular')
            ->join('alunos','alunos.id','=','ocorrencias.alunos_id')
            ->where('ocorrencias.horarios_id',$id)
            ->where('ocorrencias.data',$todayComplete)
            ->where('alunos.situacao',1)
            ->orderBy('alunos.nome','ASC')->get();
        } catch (\Exception $e) {
            $alunosOcorrencia = null;
        }
        $conteudo = DB::table('conteudo')
          ->select('conteudo')
          ->whereRaw('data = ?',[$todayComplete])
          ->whereRaw('horarios_id = ?',[$id])
          ->get()->first();
        $isMobile = $this->agent->isMobile();
        return view('horario.relatorio',compact('alunos','alunosOcorrencia','conteudo','isMobile'));
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
            $data['data'] = $this->today->format('Y-m-d');
            DB::table('ocorrencias')->insert($data);
            return 200;
        } catch (\Exception $e) {
            return 406;
        }
    }

    public function salvaRelatorio($id)
    {
        $dia = $this->today->dayOfWeek;
        switch ($dia){
            case 1:
                $dia = '1 - Segunda-feira';
                break;
            case 2:
                $dia = '2 - Terca-feira';
                break;
            case 3:
                $dia = '3 - Quarta-feira';
                break;
            case 4:
                $dia = '4 - Quinta-feira';
                break;
            case 5:
                $dia = '5 - Sexta-feira';
                break;
            case 6:
                $dia = '6 - Sabado';
                break;
            default:
                $dia = '7 - Domingo';
                break;
        }
        $data = $this->today->format('d-m-Y');
        $nomeRelatorio = "relatorio_".$data;
        try {
            $dados[0] = Request::only('conteudo');
            $dados[0]['data'] = $this->today->format('Y-m-d');
            $dados[0]['professores_id'] = Auth::user()->id;
            $dados[0]['horarios_id'] = $id;
            $verifica = DB::table('conteudo')->where('horarios_id',$id)->where('data', $dados[0]['data'])->get()->first();
            if(!isset($verifica)){
                DB::table('conteudo')->insert($dados); //Prevent duplicate
            }

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
            $de = str_replace(":","-",$info->start);
            $ate = str_replace(":","-",$info->end);
            $path = "D:/Aulas/Relatorios/$dia/$nomeMateria/$de - $ate/";
            try { //Try to create the directory
                $result = File::makeDirectory($path,0777,true,true);
            } catch (\Exception $e) {
                return 403;
            }
            try { //Try to save PDF
                $todayComplete = $this->today->format('Y-m-d');
                $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();

                $alunos = DB::select("SELECT alunos.matricula,  alunos.telefone_responsavel AS telefone, alunos.celular_responsavel AS celular, alunos.nome, alunos.nascimento, situacoes.nome AS situacao FROM alunos INNER JOIN horarios_has_alunos ON horarios_id = :idHorario INNER JOIN relatorios_has_alunos ON relatorios_has_alunos.relatorios_id = :idRelatorio INNER JOIN situacoes ON situacoes.id = relatorios_has_alunos.situacoes_id WHERE alunos.id = horarios_has_alunos.alunos_id AND alunos.id = relatorios_has_alunos.alunos_id AND alunos.situacao = 1 ORDER By alunos.nome ASC",['idHorario' => $id,'idRelatorio'=>$todaysRelatorio->id]);
                try {
                    $alunosOcorrencia = DB::table('ocorrencias')->select('alunos.nome','alunos.matricula','ocorrencias.descricao','alunos.telefone_responsavel AS telefone', 'alunos.celular_responsavel AS celular')
                    ->join('alunos','alunos.id','=','ocorrencias.alunos_id')
                    ->whereRaw('ocorrencias.horarios_id = :id AND ocorrencias.data = :data AND alunos.situacao = 1',["id"=>$id,"data"=>$todayComplete])
                    ->orderBy('alunos.nome','ASC')->get();
                } catch (\Exception $e) {
                    $alunosOcorrencia = null;
                }
                $conteudo = $dados[0]['conteudo'];
                $nomeProfessor = Auth::user()->name; //Professor
                $horario = $de." às ".$ate;
                $pdf = PDF::loadView('horario.relatorioPDF',compact('alunos','alunosOcorrencia','nomeProfessor','dia','nomeMateria','horario','conteudo'));
                $pdf->save($path.$nomeRelatorio.'.pdf');
                return 200;
            } catch (\Exception $e) {
                return 406;
            }

        } catch (\Exception $e) {
            return 406;
        }
    }

    public function updateRelatorio($id)
    {
        $dia = $this->today->dayOfWeek;
        switch ($dia){
            case 1:
                $dia = '1 - Segunda-feira';
                break;
            case 2:
                $dia = '2 - Terca-feira';
                break;
            case 3:
                $dia = '3 - Quarta-feira';
                break;
            case 4:
                $dia = '4 - Quinta-feira';
                break;
            case 5:
                $dia = '5 - Sexta-feira';
                break;
            case 6:
                $dia = '6 - Sabado';
                break;
            default:
                $dia = '7 - Domingo';
                break;
        }
        $data = $this->today->format('d-m-Y');
        $todayComplete = $this->today->format('Y-m-d');
        $nomeRelatorio = "relatorio_".$data;
        try {
            $conteudoDB = DB::table('conteudo')->select('conteudo','professores_id')->where('horarios_id',$id)->where('data', $todayComplete)->get()->first();
            if(!isset($conteudoDB)){
                return 406; //Nenhum conteudo de aula encontrado.
            }
            try { //Try to get some information
                $info = DB::table('horarios')
                ->select('materias.nome', DB::raw("DATE_FORMAT(start,'%H:%i') start"), DB::raw("DATE_FORMAT(end,'%H:%i') end"))
                ->join('materias','materias.id','=','horarios.materias_id')
                ->where("horarios.id",$id)
                ->first();
            } catch (\Exception $e) {
                return 406;
            }
            $nomeMateria = $info->nome;
            $de = str_replace(":","-",$info->start);
            $ate = str_replace(":","-",$info->end);
            $path = "D:/Aulas/Relatorios/$dia/$nomeMateria/$de - $ate/";
            try { //Try to create the directory
                $result = File::makeDirectory($path,0777,true,true);
            } catch (\Exception $e) {
                return 403;
            }
            try { //Try to save PDF
                $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();

                $alunos = DB::select("SELECT alunos.matricula,  alunos.telefone_responsavel AS telefone, alunos.celular_responsavel AS celular, alunos.nome, alunos.nascimento, situacoes.nome AS situacao FROM alunos INNER JOIN horarios_has_alunos ON horarios_id = :idHorario INNER JOIN relatorios_has_alunos ON relatorios_has_alunos.relatorios_id = :idRelatorio INNER JOIN situacoes ON situacoes.id = relatorios_has_alunos.situacoes_id WHERE alunos.id = horarios_has_alunos.alunos_id AND alunos.id = relatorios_has_alunos.alunos_id AND alunos.situacao = 1 ORDER By alunos.nome ASC",['idHorario' => $id,'idRelatorio'=>$todaysRelatorio->id]);
                try {
                    $alunosOcorrencia = DB::table('ocorrencias')->select('alunos.nome','alunos.matricula','ocorrencias.descricao','alunos.telefone_responsavel AS telefone', 'alunos.celular_responsavel AS celular')
                    ->join('alunos','alunos.id','=','ocorrencias.alunos_id')
                    ->whereRaw('ocorrencias.horarios_id = :id AND ocorrencias.data = :data AND alunos.situacao = 1',["id"=>$id,"data"=>$todayComplete])
                    ->orderBy('alunos.nome','ASC')->get();
                } catch (\Exception $e) {
                    $alunosOcorrencia = null;
                }
                $conteudo = $conteudoDB->conteudo;
	              $professor = Professor::find($conteudoDB->professores_id);
		            $nomeProfessor = $professor->name;
                $horario = $de." às ".$ate;
                $pdf = PDF::loadView('horario.relatorioPDF',compact('alunos','alunosOcorrencia','nomeProfessor','dia','nomeMateria','horario','conteudo'));
                $pdf->save($path.$nomeRelatorio.'.pdf');
                return 200;
            } catch (\Exception $e) {
                return 406;
            }

        } catch (\Exception $e) {
            return $e->getMessage();
            return 406;
        }
    }

    public function getConteudo($id)
    {
        $conteudos = DB::table('conteudo')
        ->select( DB::raw('DATE_FORMAT(data,\'%d/%m/%Y\') data'),'conteudo','professores.name')
        ->join('professores','professores.id','=','conteudo.professores_id')
        ->where('horarios_id',$id)
        ->get();
        return view('horario.conteudo',compact('conteudos'));
    }
}

<?php

namespace cni\Http\Controllers;
use Illuminate\Support\Facades\DB;
use cni\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Request;
use cni\Horario;
use cni\Materia;
use cni\Dia;
use cni\Aluno;
use cni\Relatorio;
use cni\Professor;
use cni\Ocorrencia;
use Carbon\Carbon;
use DateTime;
use PDF;
use Auth;
use Jenssegers\Agent\Agent;//Detect Mobile
/**
 *
 */
class HorarioController extends Controller
{
    private $agent, $today, $msg;
    public function __construct()
    {
        $this->agent = new Agent();
        $this->today = Carbon::now()->setTimezone('America/Sao_Paulo');
        $this->message['type'] = 'error';
        $this->middleware('autorizacao');
    }

    public function getAll()//Pega todos os horarios
    {
		    $dia = $this->today->dayOfWeek;
	      $hora = $this->today->format('H:i');
        if($dia==0){
            $dia = 1;
        }
        try {
            $horarios = DB::table('horarios')
            ->join('dias', 'dias.id', '=', 'dias_id')
            ->join('materias', 'materias.id', '=', 'materias_id')
            ->select('horarios.id AS id', 'materias.nome AS materia', 'dias.nome AS dia','horarios.dias_id', DB::raw("DATE_FORMAT(end,'%H:%i') end"), DB::raw("DATE_FORMAT(start,'%H:%i') start"))
            ->orderBy('dias.id')
            ->orderBy('start')
            ->orderBy('materias.nome');
            if(Request::input('filtro')!='all'){
                $horarios->where('dias_id',$dia)
                ->where('horarios.end','>=',$hora);
                if(Auth::user()->access == 3){//Informatica
                    $horarios->whereBetween('materias.id', [1, 7]); //Gets the classes info.
                } else if(Auth::user()->access == 4){ //Ingles
                    $horarios->where('materias.id',8);
                } else if(Auth::user()->access == 5){//Gestao
                    $horarios->where('materias.id',9);
                }
            }
            $horarios = $horarios->get();
        } catch (\Exception $e) {
            $horarios = array();
        }
        $materias = Materia::all();
        $dias = Dia::all();
        return view('horario.horario')->with(compact('horarios','materias','dias'));
    }

    public function checkConflict($idAluno,$id)//Procura por conflito de horarios
    {
        try {
            $horario = Horario::findOrFail($id);
            try {
                $aluno = Aluno::findOrFail($idAluno);
                $horariosCadastrado = DB::table('horarios_has_alunos')
                ->select('materias.nome AS materia','materias.id AS materias_id', 'horarios_id', 'dias.nome AS dia','dias.id AS dia_id', DB::raw("DATE_FORMAT(horarios.end,'%H:%i') end"), DB::raw("DATE_FORMAT(horarios.start,'%H:%i') start"))
                ->join('horarios','horarios.id','=','horarios_id')
                ->join('materias','materias.id','=','horarios.materias_id')
                ->join('dias','dias.id','=','horarios.dias_id')
                ->where('alunos_id',$aluno->id)->get();
                $gtg = true;
            if(count($horariosCadastrado) != 0){//Horarios Cadastrados
                $inicio = new DateTime($horario->start);
                $fim = new DateTime($horario->end);
                foreach ($horariosCadastrado as $horarioCadastrado) {
                    $inicioC = new DateTime($horarioCadastrado->start);
                    $fimC = new DateTime($horarioCadastrado->end);
                    if($horarioCadastrado->horarios_id == $id){ //If aluno is already registered on this horario
                        $this->message['text'] = 'Aluno já está nesse horário.';
                        return false;
                    } else if(((($inicio > $inicioC && $inicio < $fimC) || ($fim > $inicioC && $fim < $fimC)) && $horario->dias_id == $horarioCadastrado->dias_id) || $horario->materias_id == $horarioCadastrado->materias_id){//Conflict
                        $this->message['time'] = 10000;
                        $this->message['text'] = 'Conflito de horários. O aluno já entá no horário de <strong>'.$horarioCadastrado->materia.' de '.$horarioCadastrado->start.' às '.$horarioCadastrado->end.' na '.$horarioCadastrado->dia.'</strong>';
                        return  false;//False because Aluno is registered whether in the same Materia or there's a conflict of classes
                    }
                }
                if($gtg){//Good to go
                    return true;
                }
            } else {//Good to go
                return true;
            }

            } catch (\Exception $e) {//Aluno not found
                $this->message['text'] = 'Aluno não encontrado.';
                return false;//false
            }
        } catch (\Exception $e) {//Horario not found
            $this->message['text'] = 'Horário não encontrado.';
            return false;
        }
    }

    public function addHorario()//Adiciona novos horario
    {
        try {
            $params = json_decode(Request::input('data'),true); //JSON to ARRAY
            return var_dump($params);
            $horario = new Horario($params);
            $horario->save();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Horário adicionado!';
            return json_encode($data);
            $data['id'] = $horario->id;
        } catch (\Exception $e) {
            $this->message['text'] = 'Erro ao incluir no banco de dados!';
            return json_encode($this->message);
        }
    }

    public function getHorario($id)
    {
        return redirect()->route('chamada',['id' => $id]);
    }

    public function chamada($id)//View chamada
    {
        $todayComplete = $this->today->format('Y-m-d');
        $info = DB::table('horarios')
        ->select('materias.nome', DB::raw("DATE_FORMAT(start,'%H:%i') start"), DB::raw("DATE_FORMAT(end,'%H:%i') end"))
        ->join('materias','materias.id','=','horarios.materias_id')
        ->where("horarios.id",$id)
        ->first();
        $alunos = DB::table('horarios')
        ->select('alunos.id','alunos.nome','alunos.matricula','alunos.nascimento')
        ->join('horarios_has_alunos','horarios_has_alunos.horarios_id','=','horarios.id')
        ->join('alunos','alunos.id','=','horarios_has_alunos.alunos_id')
        ->where('horarios_has_alunos.horarios_id',$id)
        ->where('alunos.situacao',1)
        ->orderBy('alunos.nome', 'ASC')
        ->get();
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
            $this->message['text'] = 'Conflito de informações. Tente novamente.';
            return json_encode($this->message);
        } else { //Good to go
            try { //Try to find Aluno
                $aluno = Aluno::where('matricula', $dados['matricula'])->firstOrFail();
                if($this->checkConflict($aluno->id,$id)){//Add Aluno if there isn't any conflict
                    try { //Try to insert Aluno into the Class
                        DB::table('horarios_has_alunos')->insert(
                            ['horarios_id' => $id, 'alunos_id' => $aluno->id]
                        );
                        $this->message['type'] = 'success';
                        $this->message['text'] = 'Aluno adicionado com sucesso!';
                        return json_encode($this->message);
                    } catch (\Exception $e) { //Not possible to insert
                        $this->message['text'] = 'Não foi possível adicionar o aluno.';
                        return json_encode($this->message);
                    }
                } else {//Conflict between Horarios
                    //Message is comming from checkConflict
                    return json_encode($this->message);
                }
            } catch (\Exception $e) { //Aluno not found
                $this->message['text'] = 'Aluno não encontrado.';
                return json_encode($this->message);
            }
        }
    }

    public function removerChamada($id) //Remove aluno da chamada
    {
        $dados = Request::only(['matricula','horario']); //Get only matricula and horario
        if($id != $dados['horario']){
            $this->message['text'] = 'Conflito de informações. Tente novamente.';
            return json_encode($this->message);
        } else { //Good to go
            try { //Try to remove Aluno from chamada
                $aluno = Aluno::where('matricula', $dados['matricula'])->firstOrFail();
                try { //Try to remove Aluno
                    DB::table('horarios_has_alunos')
                    ->where('horarios_id',$id)
                    ->where('alunos_id',$aluno->id)
                    ->delete();
                    $this->message['type'] = 'success';
                    $this->message['text'] = 'Aluno removido com sucesso!';
                    return json_encode($this->message);
                } catch (\Exception $e) { //Not possible to delete
                    $this->message['text'] = 'Não foi possível remover o aluno.';
                    return json_encode($this->message);
                }
            } catch (\Exception $e) { //Aluno not found
                $this->message['text'] = 'Aluno não encontrado.';
                return json_encode($this->message);
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
            $this->message['text'] = 'Conflito de informações. Tente novamente.';
            return json_encode($this->message);
        }
        try {//Testa se existe relatorio
            $relatorio = Relatorio::where([
                ['data',$dataHoje],
                ['horarios_id',$id]
            ])->firstOrFail();
            try {
                $conteudo = DB::table('conteudo')
                ->where('horarios_id',$id)
                ->where('data',$dataHoje)->get()->first();
                $relatorioCompleteExist = true;
            } catch (\Exception $e) {
                $relatorioCompleteExist = false;
            }
        } catch (\Exception $e) { //Nenhum relatorio
            $relatorioCompleteExist = false;
            try {
                $params['data'] = $dataHoje;
                $params['horarios_id'] = $id;
				$params['professores_id'] = Auth::user()->id;//Professor
                $relatorio = new Relatorio($params);
                $relatorio->save();
            } catch (\Exception $e) {
                $this->message['title'] = 'Erro';
                $this->message['text'] = 'Chamada não salva.';//Relatorio não criado
                return json_encode($this->message);
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
                    $this->message['text'] = 'Dados dos Alunos não foram salvos. Tente novamente.';
                    return json_encode($this->message);
                }
            }
        }
        if($relatorioCompleteExist){//Se já havia relatorio então atualiza o PDF
           return $this->updateRelatorio($id);
        }
        $this->message['type'] = 'success';
        $this->message['text'] = 'Chamada salva com sucesso!';
        return json_encode($this->message);
    }

    public function relatorio($id)//View do Relatorio
    {
        $todayComplete = $this->today->format('Y-m-d');
        try {
            $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();
            $alunos = DB::table('relatorios')
            ->select('alunos.matricula','alunos.telefone_responsavel AS telefone','alunos.celular_responsavel AS celular','alunos.nome','alunos.nascimento','situacoes.nome AS situacao')
            ->join('relatorios_has_alunos','relatorios_has_alunos.relatorios_id','=','relatorios.id')
            ->join('alunos','alunos.id','=','relatorios_has_alunos.alunos_id')
            ->join('situacoes','situacoes.id','=','relatorios_has_alunos.situacoes_id')
            ->where('relatorios.id',$todaysRelatorio->id)
            ->where('alunos.situacao',1)
            ->orderBy('alunos.nome', 'ASC')
            ->get();
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
          ->select('conteudo','id')
          ->whereRaw('data = ?',[$todayComplete])
          ->whereRaw('horarios_id = ?',[$id])
          ->get()->first();
        $isMobile = $this->agent->isMobile();
        return view('horario.relatorio',compact('alunos','alunosOcorrencia','conteudo','isMobile'));
    }

    public function ocorrencia($id)//View para Ocorrencias
    {
        $alunos = DB::table('horarios')
        ->select('alunos.id','alunos.nome','alunos.matricula','alunos.nascimento')
        ->join('horarios_has_alunos','horarios_has_alunos.horarios_id','=','horarios.id')
        ->join('alunos','alunos.id','=','horarios_has_alunos.alunos_id')
        ->where('horarios_has_alunos.horarios_id',$id)
        ->where('alunos.situacao',1)
        ->orderBy('alunos.nome', 'ASC')
        ->get();

        return view('horario.ocorrencia')->with(compact('alunos'));
    }

    public function addOcorrencia($id)//Adiciona Ocorrencia
    {
        try {
            $data = Request::only(['alunos_id','descricao']);
            $data['horarios_id'] = $id;
            $data['professores_id'] = Auth::user()->id;//Professor
            $data['data'] = $this->today->format('Y-m-d');
            try {//Try to find any ocorrencia with the same params
                $ocorrencia = Ocorrencia::where('alunos_id',$data['alunos_id'])
                ->where('horarios_id',$id)
                ->where('data',$data['data'])
                ->firstOrFail();
                try {
                    $ocorrencia->descricao = $data['descricao'];
                    $ocorrencia->professores_id = $data['professores_id'];
                    $ocorrencia->save();
                    $this->message['type'] = 'success';
                    $this->message['text'] = 'Ocorrência atualizada com sucesso.';
                    return json_encode($this->message);
                } catch (\Exception $e) {
                    $this->message['text'] = 'Erro ao atualizar a ocorrência.';
                    return json_encode($this->message);
                }

            } catch (\Exception $e) {//Ocorrencia not found
                try {//Insert new ocorrencia
                    DB::table('ocorrencias')->insert($data);
                    $this->message['type'] = 'success';
                    $this->message['text'] = 'Ocorrência salva com sucesso.';
                    return json_encode($this->message);
                } catch (\Exception $e) {//Error
                    $this->message['text'] = 'Erro ao salvar a ocorrência.';
                    return json_encode($this->message);
                }

            }
        } catch (\Exception $e) {
            $this->message['text'] = 'Erro ao salvar a ocorrência.';
            return json_encode($this->message);
        }
    }

    public function removeOcorrencia($id)//Deleta Ocorrencia
    {
        try {//Try to find ocorrencia
            $data = $this->today->format('Y-m-d');
            $matricula = Request::input('matricula');
            $aluno = Aluno::where('matricula',$matricula)->firstOrFail();
            $ocorrencia = Ocorrencia::where('alunos_id', $aluno->id)
            ->where('data',$data)->firstOrFail();
            try {
                $ocorrencia->delete();
                $this->message['type'] = 'success';
                $this->message['text'] = 'Ocorrência excluida com sucesso.';
                return json_encode($this->message);
            } catch (\Exception $e) {
                $this->message['text'] = 'Ocorrência não pode ser excluida.';
                return json_encode($this->message);
            }
        } catch (\Exception $e) {//Ocorrencia not found
            $this->message['text'] = 'Ocorrência não encontrada para deletar.';
            $this->message['error'] = $e->getMessage();
            return json_encode($this->message);
        }

    }

    public function salvaRelatorio($id)//Cria Relatorio PDF
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
            $dados = Request::only('conteudo');
            $dados['data'] = $this->today->format('Y-m-d');
            $dados['professores_id'] = Auth::user()->id;
            $dados['horarios_id'] = $id;
            $verifica = DB::table('conteudo')->where('horarios_id',$id)->where('data', $dados['data'])->get()->first();
            if(!isset($verifica)){
                $conteudoId = DB::table('conteudo')->insertGetId($dados); //Prevent duplicate
            }

            try { //Try to get some information
                $info = DB::table('horarios')
                ->select('materias.nome', DB::raw("DATE_FORMAT(start,'%H:%i') start"), DB::raw("DATE_FORMAT(end,'%H:%i') end"))
                ->join('materias','materias.id','=','horarios.materias_id')
                ->where("horarios.id",$id)
                ->first();
            } catch (\Exception $e) {
                $this->message['text'] = 'Erro ao buscar no banco de dados.';
                return json_encode($this->message);
            }

            $nomeMateria = $info->nome;
            $de = str_replace(":","-",$info->start);
            $ate = str_replace(":","-",$info->end);
            $path = "D:/Aulas/Relatorios/$dia/$nomeMateria/$de - $ate/";
            // $path = "/var/www/html/cni/relatorios/$dia/$nomeMateria/$de - $ate/";

            try { //Try to create the directory
                $result = File::makeDirectory($path,0777,true,true);
            } catch (\Exception $e) {
                $this->message['text'] = 'Erro ao criar diretório.';
                return json_encode($this->message);
            }
            try { //Try to save PDF
                $todayComplete = $this->today->format('Y-m-d');
                $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();
                $alunos = DB::table('relatorios')
                ->select('alunos.matricula','alunos.telefone_responsavel AS telefone','alunos.celular_responsavel AS celular','alunos.nome','alunos.nascimento','situacoes.nome AS situacao')
                ->join('relatorios_has_alunos','relatorios_has_alunos.relatorios_id','=','relatorios.id')
                ->join('alunos','alunos.id','=','relatorios_has_alunos.alunos_id')
                ->join('situacoes','situacoes.id','=','relatorios_has_alunos.situacoes_id')
                ->where('relatorios.id',$todaysRelatorio->id)
                ->where('alunos.situacao',1)
                ->orderBy('alunos.nome', 'ASC')
                ->get();
                try {
                    $alunosOcorrencia = DB::table('ocorrencias')->select('alunos.nome','alunos.matricula','ocorrencias.descricao','alunos.telefone_responsavel AS telefone', 'alunos.celular_responsavel AS celular')
                    ->join('alunos','alunos.id','=','ocorrencias.alunos_id')
                    ->whereRaw('ocorrencias.horarios_id = :id AND ocorrencias.data = :data AND alunos.situacao = 1',["id"=>$id,"data"=>$todayComplete])
                    ->orderBy('alunos.nome','ASC')->get();
                } catch (\Exception $e) {
                    $alunosOcorrencia = null;
                }
                $conteudo = $dados['conteudo'];
                $nomeProfessor = Auth::user()->name; //Professor
                $horario = $de." às ".$ate;
                $pdf = PDF::loadView('horario.relatorioPDF',compact('alunos','alunosOcorrencia','nomeProfessor','dia','nomeMateria','horario','conteudo'));
                $pdf->save($path.$nomeRelatorio.'.pdf');
                $this->message['id'] = $conteudoId;
                $this->message['type'] = 'success';
                $this->message['text'] = 'Relatório salvo com sucesso.';
                return json_encode($this->message);
            } catch (\Exception $e) {
                $this->message['text'] = 'Erro ao salvar o relatório.';
                $this->message['error'] = $e->getMessage();
                return json_encode($this->message);
            }

        } catch (\Exception $e) {
            $this->message['text'] = 'Erro ao salvar o relatório.';
            return json_encode($this->message);
        }
    }

    public function updateRelatorio($id)//Atualiza Relatorio PDF
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
                $this->message['text'] = 'Nenhum conteúdo de aula encontrado.';
                return json_encode($this->message);
            }
            try { //Try to get some information
                $info = DB::table('horarios')
                ->select('materias.nome', DB::raw("DATE_FORMAT(start,'%H:%i') start"), DB::raw("DATE_FORMAT(end,'%H:%i') end"))
                ->join('materias','materias.id','=','horarios.materias_id')
                ->where("horarios.id",$id)
                ->first();
            } catch (\Exception $e) {
                $this->message['text'] = 'Erro ao buscar no banco de dados.';
                return json_encode($this->message);
            }
            $nomeMateria = $info->nome;
            $de = str_replace(":","-",$info->start);
            $ate = str_replace(":","-",$info->end);
            $path = "D:/Aulas/Relatorios/$dia/$nomeMateria/$de - $ate/";
            // $path = "/var/www/html/cni/relatorios/$dia/$nomeMateria/$de - $ate/";
            try { //Try to create the directory
                $result = File::makeDirectory($path,0777,true,true);
            } catch (\Exception $e) {
                $this->message['text'] = 'Erro ao criar diretório.';
                return json_encode($this->message);
            }
            try { //Try to save PDF
                $todaysRelatorio = Relatorio::where('data',$todayComplete)->where('horarios_id',$id)->firstOrFail();

                $alunos = DB::table('relatorios')
                ->select('alunos.matricula','alunos.telefone_responsavel AS telefone','alunos.celular_responsavel AS celular','alunos.nome','alunos.nascimento','situacoes.nome AS situacao')
                ->join('relatorios_has_alunos','relatorios_has_alunos.relatorios_id','=','relatorios.id')
                ->join('alunos','alunos.id','=','relatorios_has_alunos.alunos_id')
                ->join('situacoes','situacoes.id','=','relatorios_has_alunos.situacoes_id')
                ->where('relatorios.id',$todaysRelatorio->id)
                ->where('alunos.situacao',1)
                ->orderBy('alunos.nome', 'ASC')
                ->get();
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
                $this->message['type'] = 'success';
                $this->message['text'] = 'Relatório atualizado com sucesso.';
                return json_encode($this->message);
            } catch (\Exception $e) {
                $this->message['text'] = 'Erro ao atualizar o relatório. Verifique se ele está sendo usado pela Recepção.';
                return json_encode($this->message);
            }

        } catch (\Exception $e) {
            $this->message['text'] = 'Erro ao atualizar o relatório.';
            return json_encode($this->message);
        }
    }

    public function getConteudo($id)//View do Conteudo das AUlas
    {
        $conteudos = DB::table('conteudo')
        ->select( DB::raw('DATE_FORMAT(data,\'%d/%m/%Y\') data'),'conteudo','professores.name')
        ->join('professores','professores.id','=','conteudo.professores_id')
        ->where('horarios_id',$id)
        ->orderBy('data','DESC')
        ->get();
        return view('horario.conteudo',compact('conteudos'));
    }

    public function removeConteudo($id)//Remover o conteudo
    {
        $id_conteudo = Request::input('id_conteudo');
        try {
            DB::table('conteudo')->where('id',$id_conteudo)->delete();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Conteúdo deletado com sucesso.';
            return json_encode($this->message);
        } catch (\Exception $e) {
            $this->message['text'] = 'Conteúdo não foi deletado.';
            return json_encode($this->message);
        }

    }
}

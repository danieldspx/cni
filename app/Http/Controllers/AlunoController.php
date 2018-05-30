<?php

namespace cni\Http\Controllers;
use Illuminate\Support\Facades\DB;
use cni\Http\Controllers\Controller;
use Request;
use cni\Aluno;
use cni\Dia;

class AlunoController extends Controller
{
    private $msg;
    public function __construct()
    {
        $this->message['type'] = 'error';
        $this->middleware('autorizacao');
    }

    public function dateConverter($value)
    {
        $date = explode('-',$value);
        $newDate = implode('-',array_reverse($date));
        return $newDate;
    }

    public function getHome()
    {
        $dias = Dia::all();
        return view('aluno.aluno')->with(compact('dias'));
    }

    public function addAluno()
    {
        try {
            $params = json_decode(Request::input('data'),true); //JSON to Array
            try{
                $search = Aluno::where('matricula',$params['matricula'])->firstOrFail();
                $aluno = $search; //Aluno existente
                foreach($params as $key => $value){
                    $aluno[$key] = $value; //Updating aluno
                }
            } catch (\Exception $e){
                $aluno = new Aluno($params); //Novo aluno
            }
            $aluno->nascimento = $this->dateConverter($aluno->nascimento);
            $aluno->save();
            $this->message['type'] = 'success';
            $this->message['text'] = 'Aluno salvo!';
            return json_encode($this->message);
        } catch (\Exception $e) {
            $this->message['text'] = 'Erro ao incluir no Banco de dados!';
            return json_encode($this->message);
        }

    }

    public function buscarAluno()
    {
        if(Request::input('matricula')){
            try {//Try find by matricula
                $matricula = Request::input('matricula');
                $aluno = Aluno::where('matricula', $matricula)->firstOrFail();
                $aluno->nascimento = $this->dateConverter($aluno->nascimento);
                if(Request::input('additional') == 'getCursos'){//Get cursos to show in Alunos section
                    try {
                        $horariosCadastrado = DB::table('horarios_has_alunos')
                        ->select('materias.nome AS materia', 'horarios_id', 'dias.nome AS dia', DB::raw("DATE_FORMAT(horarios.end,'%H:%i') end"), DB::raw("DATE_FORMAT(horarios.start,'%H:%i') start"))
                        ->join('horarios','horarios.id','=','horarios_id')
                        ->join('materias','materias.id','=','horarios.materias_id')
                        ->join('dias','dias.id','=','horarios.dias_id')
                        ->where('alunos_id',$aluno->id)
                        ->get();
                        $aluno->cursos = (object)$horariosCadastrado;
                    } catch (\Exception $e) {
                        $this->message['text'] = 'Erro ao buscar cursos cadastrados para o aluno!';
                        return json_encode($this->message);
                    }
                }
                $this->message['type'] = 'success';
                $this->message['data'] = $aluno;
                return json_encode($this->message);
            } catch (\Exception $e) {
                $this->message['text'] = 'Matrícula não encontrada!';
                return json_encode($this->message);
            }
        } else {
            try {//Try find by nome
                $nome = strtoupper(Request::input('nome'));
                $alunos = DB::table('alunos')->where('nome','LIKE',$nome.'%')->get();
                if(Request::input('additional') == 'getCursos'){
                    $proceed = true;
                } else {
                    $proceed = false;
                }
                foreach ($alunos as $key => $aluno) { //Update nascimento
                    $aluno->nascimento = $this->dateConverter($aluno->nascimento);
                    if($proceed){//Get cursos to show in Alunos section
                        try {
                            $horariosCadastrado = DB::table('horarios_has_alunos')
                            ->select('materias.nome AS materia', 'horarios_id', 'dias.nome AS dia', DB::raw("DATE_FORMAT(horarios.end,'%H:%i') end"), DB::raw("DATE_FORMAT(horarios.start,'%H:%i') start"))
                            ->join('horarios','horarios.id','=','horarios_id')
                            ->join('materias','materias.id','=','horarios.materias_id')
                            ->join('dias','dias.id','=','horarios.dias_id')
                            ->where('alunos_id',$aluno->id)
                            ->get();
                            $aluno->cursos = (object)$horariosCadastrado;
                        } catch (\Exception $e) {
                            $this->message['text'] = 'Erro ao buscar cursos cadastrados para o aluno!';
                            return json_encode($this->message);
                        }
                    }
                    $alunos[$key] = $aluno;
                }
                $this->message['type'] = 'success';
                $this->message['data'] = $alunos;
                return json_encode($this->message);
            } catch (\Exception $e) {
                $this->message['text'] = 'Nome não encontrado!';
                return json_encode($this->message);
            }
        }
    }

    public function mudarAluno()
    {
        $type = Request::input('type');
        $dia = Request::input('dia');
        if($type == 1){//Get horarios
            try {//Not possible to search
                $horarios = DB::table('horarios')
                ->select('materias.nome','materias_id AS id')
                ->join('materias','materias.id','=','materias_id')
                ->where('dias_id',$dia)
                ->groupBy('materias_id')
                ->get();
                return json_encode($horarios);
            } catch (\Exception $e) {//Not possible to search
                $this->message['text'] = 'Não foi possível pesquisar pelo curso. Tente novamente!';
                return json_encode($this->message);
            }
        } else if ($type == 2){//Horarios naquele dia e horario
            $materia = Request::input('materia');
            try {
                $horarios = DB::table('horarios')
                ->select('horarios.id',DB::Raw("DATE_FORMAT(horarios.end,'%H:%i') end"),DB::Raw("DATE_FORMAT(horarios.start,'%H:%i') start"))
                ->join('materias','materias.id','=','materias_id')
                ->where('dias_id',$dia)
                ->where('materias_id',$materia)
                ->get();
                return json_encode($horarios);
            } catch (\Exception $e) {//Not possible to search
                $this->message['text'] = 'Não foi possível pesquisar pelo curso. Tente novamente!';
                return json_encode($this->message);
            }
        } else if($type == 3){
            $matricula = Request::input('aluno');
            $horarioNew = Request::input('toHorario');
            $horarioOld = Request::input('fromHorario');
            try { //Try to remove Aluno from chamada
                $aluno = Aluno::where('matricula', $matricula)->firstOrFail();
                try { //Try to remove Aluno
                    DB::table('horarios_has_alunos')
                    ->where('horarios_id',$horarioOld)
                    ->where('alunos_id',$aluno->id)
                    ->delete();
                } catch (\Exception $e) { //Not possible to delete
                    $this->message['text'] = 'Não foi possível excluir aluno do horário antigo.';
                    return json_encode($this->message);
                }
            } catch (\Exception $e) { //Aluno not found
                $this->message['text'] = 'Aluno não encontrado.';
                return json_encode($this->message);
            }

            return redirect()->action(//Redirect to the function that includes
                'HorarioController@incluirChamada',
                [
                    'id'=>$horarioNew,
                    'matricula'=>$matricula,
                    'horario'=>$horarioNew,
                ]
            );
        }
    }
}

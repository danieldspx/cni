<?php

namespace cni\Http\Controllers;
use Request;
use Illuminate\Support\Facades\DB;
use cni\Aluno;

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
        return view('aluno.aluno');
    }

    public function addAluno() //Add or Update
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
        } catch (\Exception $e) {
            $this->message['text'] = 'Erro ao incluir no Banco de dados!';
        }

    }

    public function buscarAluno()
    {
        if(Request::input('matricula')){
            try {//Try find by matricula
                $matricula = Request::input('matricula');
                $aluno = Aluno::where('matricula', $matricula)->firstOrFail();
                $aluno->nascimento = $this->dateConverter($aluno->nascimento);
                return json_encode($aluno);
            } catch (\Exception $e) {
                $this->message['text'] = 'Matrícula não encontrada!';
                return json_encode($this->message);
            }
        } else {
            try {//Try find by nome
                $nome = strtoupper(Request::input('nome'));
                $alunos = DB::table('alunos')->where('nome','LIKE',$nome.'%')->get();
                foreach ($alunos as $key => $aluno) { //Update nascimento
                    $aluno->nascimento = $this->dateConverter($aluno->nascimento);
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
}

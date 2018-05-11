<?php

namespace cni\Http\Controllers;
use Request;
use Illuminate\Support\Facades\DB;
use cni\Aluno;

class AlunoController extends Controller
{
    public function __construct()
    {
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
            return 200;
        } catch (\Exception $e) {
            return 400;
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
                return 400;
            }
        } else {
            try {//Try find by nome
                $nome = strtoupper(Request::input('nome'));
                $alunos = DB::table('alunos')->where('nome','LIKE',$nome.'%')->get();
                foreach ($alunos as $key => $aluno) { //Update nascimento
                    $aluno->nascimento = $this->dateConverter($aluno->nascimento);
                    $alunos[$key] = $aluno;
                }
                return json_encode($alunos);
            } catch (\Exception $e) {
                return 404;
            }
        }
    }
}

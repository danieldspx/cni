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
                    $aluno[$key] = $value; //Updating data
                }
            } catch (\Exception $e){
                $aluno = new Aluno($params); //Novo aluno
            }
            $aluno->save();
            return 200;
        } catch (\Exception $e) {
            return 400;
        }

    }

    public function buscarAluno()
    {
        try {
            $matricula = Request::input('matricula');
            $aluno = Aluno::where('matricula', $matricula)->get();
            return json_encode($aluno[0]);
        } catch (\Exception $e) {
            return 400;
        }

    }
}

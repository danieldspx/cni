<?php

namespace cni;

use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    protected $table = 'alunos';
    public $timestamps = false;

    protected $fillable = array('matricula','nome','nascimento', 'situacao', 'telefone','telefone_responsavel','celular_responsavel');

    protected $guarded = ['id'];
}

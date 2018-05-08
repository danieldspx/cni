<?php

namespace cni;

use Illuminate\Database\Eloquent\Model;

class Ocorrencia extends Model
{
    protected $table = 'ocorrencias';
    public $timestamps = false;

    protected $fillable = array('professores_id','alunos_id','descricao', 'horarios_id');

    protected $guarded = ['id'];
}

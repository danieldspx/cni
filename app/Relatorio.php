<?php

namespace cni;

use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    protected $table = 'relatorios';
    public $timestamps = false;

    protected $fillable = array('data','horarios_id','professores_id');

    protected $guarded = ['id'];
}

<?php

namespace cni;

use Illuminate\Database\Eloquent\Model;

class Situacao extends Model
{
    protected $table = 'situacoes';
    public $timestamps = false;

    protected $fillable = array('nome');

    protected $guarded = ['id'];
}

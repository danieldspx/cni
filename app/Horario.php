<?php

namespace cni;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    public $timestamps = false;

    protected $fillable = array('materias_id','start','end','dias_id');

    protected $guarded = ['id'];
}

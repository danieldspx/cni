<?php

namespace cni;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materias';
    public $timestamps = false;

    protected $fillable = ['nome'];

    protected $guarded = ['id'];
}

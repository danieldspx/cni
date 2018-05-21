<?php

namespace cni;

use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    protected $table = 'professores';
	public $timestamps = false;

    protected $guarded = ['id','access'];
}

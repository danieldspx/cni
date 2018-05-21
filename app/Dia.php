<?php

namespace cni;

use Illuminate\Database\Eloquent\Model;

class Dia extends Model
{
    protected $table = 'dias';
    public $timestamps = false;

    protected $fillable = ['nome'];

    protected $guarded = ['id'];
}

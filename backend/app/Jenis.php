<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    public $table = 'jnstrans';
    public $incrementing = false; 
    public $timestamps = false;
    protected $primaryKey = 'jns';
    protected $fillable = [
    'jns','uraian'
    ];
}

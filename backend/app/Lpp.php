<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lpp extends Model
{
    public $table = 'json_lpp';
    public $incrementing = true; 
    public $timestamps = 'true';
    protected $guarded = ['id'];
}

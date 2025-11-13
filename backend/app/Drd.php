<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drd extends Model
{
    public $table = 'json_drd';
    public $incrementing = true; 
    public $timestamps = 'true';
    protected $guarded = ['id'];
}

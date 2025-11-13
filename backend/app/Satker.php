<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Satker extends Model
{
    protected $table = 't_satker';
    protected $primaryKey = 'unit';
    public $incrementing = false;
    public $timestamps = false;
    //protected $guarded = ['id'];
    protected $fillable = ['unit','nm_unit','kota'];
}

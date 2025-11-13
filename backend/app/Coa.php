<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    public $table = 'rekening';
    public $incrementing = false; 
    public $timestamps = false;
    protected $primaryKey = 'kode_perk';
    protected $fillable = [
    'kode_perk','nama_perk'
    ];
}

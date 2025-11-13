<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invent extends Model
{
    protected $table = 't_invent';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

}

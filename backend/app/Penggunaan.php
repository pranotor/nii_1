<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penggunaan extends Model
{
    protected $table = 't_penggunaan';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

}

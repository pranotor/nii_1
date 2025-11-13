<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fp extends Model
{
    protected $table = 't_fakturpajak';
    protected $primaryKey = 'no_fp';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

}

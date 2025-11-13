<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dvud extends Model
{
    protected $table = 't_voucher';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function bayar(){
        return $this->hasMany('App\Voucherbayar','no_vcr','no_vcr');
    }
}

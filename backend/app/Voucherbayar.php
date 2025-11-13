<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucherbayar extends Model
{
    protected $table = 't_voucher_bayar';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function jbk(){
        return $this->hasMany('App\Jurnal','referensi','ref_jurnal');
    }
}

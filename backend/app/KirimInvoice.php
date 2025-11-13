<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KirimInvoice extends Model
{
    protected $table = 't_ttinvoice';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function bayar(){
        return $this->hasMany('App\PiutangBayar','bankin_id','id');
    }

}

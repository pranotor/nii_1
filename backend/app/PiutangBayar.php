<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PiutangBayar extends Model
{
    protected $table = 't_piutang_bayar';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function cust(){
        return $this->belongsTo('App\Customer','cust_id','id');
    }
}

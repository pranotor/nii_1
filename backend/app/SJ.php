<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SJ extends Model
{
    protected $table = 't_sj';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function sales(){
        return $this->belongsTo('App\Quotation','so_no','so_no');
    }

    public function status(){
        return $this->belongsTo('App\StatusQuotation','posting','status');
    }

    public function sjd(){
        return $this->hasMany('App\SJD','sj_id');
    }
    public function quod(){
        return $this->hasMany('App\Quod','qt_id');
    }

}

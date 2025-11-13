<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $table = 't_retur';
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

    public function quod(){
        return $this->hasMany('App\Quod','qt_id');
    }

}

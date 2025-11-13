<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 't_produksi';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];


    public function detail(){
        return $this->hasMany('App\ProduksiD','prod_id');
    }

    public function work(){
        return $this->belongsTo('App\Wo','wo_id');
    }
}

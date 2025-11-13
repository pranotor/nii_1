<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProduksiD extends Model
{
    protected $table = 't_produksi_item';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];
    protected $casts = [
        'use_sn' => 'boolean',
    ];

    public function prod_head(){
        return $this->belongsTo('App\Produksi','prod_id');
    }

    public function itembarang(){
        return $this->belongsTo('App\Item','item_id');
    }

    public function detailquot(){
        return $this->belongsTo('App\Quod','qd_id');
    }


}

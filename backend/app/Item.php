<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 't_item';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];
    protected $casts = [
        'use_sn' => 'boolean',
    ];

    public function stock(){
        return $this->hasMany('App\Invent','item_id','id');
    }

    public function itemperkiraan(){
        return $this->belongsTo('App\Perkiraan','kode_perk');
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cutting extends Model
{
    protected $table = 't_cutting';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];
    protected $casts = [
        'use_sn' => 'boolean',
    ];

    public function stock(){
        return $this->hasMany('App\Invent');
    }

    public function cuttd(){
        return $this->hasMany('App\CuttingD','ct_id');
    }

    public function perkiraan(){
        return $this->belongsTo('App\Perkiraan','kode_perk');
    }

}

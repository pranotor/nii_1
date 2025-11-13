<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perkiraan extends Model
{
    protected $table = 'rekening';
    protected $primaryKey = 'kode_perk';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    //protected $guarded = ['id'];
    protected $fillable = ['kode_perk','nama_perk','parent','level'];

    public function subperkiraan(){
        return $this->hasMany('App\Perkiraan','parent','kode_perk');
    }

    public function parentperkiraan(){
        return $this->belongsTo('App\Perkiraan','parent','kode_perk');
    }

    public function itembarang(){
        return $this->hasMany('App\Item','kode_perk','kode_perk');
    }

    public function scopeChildren($query)
    {
        return $this->subperkiraan()->with(['children','jurnaltrns']);
    }

    public function jurnal(){
        return $this->hasMany('App\Jurnal','kode','kode_perk');
    }

    public function scopeJurnaltrns($query){
        return $this->hasMany('App\Jurnal','kode','kode_perk');
    }
}



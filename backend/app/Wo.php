<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wo extends Model
{
    protected $table = 't_wo';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function supplier(){
        return $this->belongsTo('App\Rekanan','rekanan','kode');
    }

    public function quotation(){
        return $this->belongsTo('App\Quotation','qt_id');
    }

    public function bpbd(){
        return $this->hasMany('App\Bpbd');
    }

}

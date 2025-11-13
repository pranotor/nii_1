<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Po extends Model
{
    protected $table = 't_po';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function supplier(){
        return $this->belongsTo('App\Rekanan','rekanan','kode');
    }

    public function status(){
        return $this->hasMany('App\Bpb','po_no','po_no');
    }

    public function bpbd(){
        return $this->hasMany('App\Bpbd');
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bpb extends Model
{
    protected $table = 't_bpb';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function supplier(){
        return $this->belongsTo('App\Rekanan','rekanan','kode');
    }

    public function status(){
        return $this->belongsTo('App\StatusPosting','posting','status');
    }

    public function bpbd(){
        return $this->hasMany('App\Bpbd');
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bpp extends Model
{
    protected $table = 't_minta';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];


    public function status(){
        return $this->belongsTo('App\StatusPosting','posting','status');
    }

    public function guna(){
        return $this->belongsTo('App\Penggunaan','guna_id');
    }

    public function bppd(){
        return $this->hasMany('App\Bppd','bpp_id','id');
    }

    public function peminta(){
        return $this->belongsTo('App\Pegawai','pemohon','nik');
    }

    public function penyetuju(){
        return $this->belongsTo('App\Pegawai','disetujui','nik');
    }

    public function pengeluar(){
        return $this->belongsTo('App\Pegawai','mengeluarkan','nik');
    }

    public function penerimanya(){
        return $this->belongsTo('App\Pegawai','penerima','nik');
    }

}

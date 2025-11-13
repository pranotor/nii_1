<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table = 'jurnal';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function rekanan(){
        return $this->belongsTo('App\Rekanan','rekanan','kode');
    }

    public function seksi(){
        return $this->belongsTo('App\Satker','unit','unit');
    }

    public function getRekanan()
    {
        return $this->rekanan()->getResults();
    }

    public function coa(){
        return $this->belongsTo('App\Perkiraan','kode','kode_perk');
    }
    
    public function getCoa()
    {
        return $this->coa()->getResults();
    }

    public function jenisJurnal() {
        return $this->belongsTo('App\Jenis','jenis','jns');
    }
}

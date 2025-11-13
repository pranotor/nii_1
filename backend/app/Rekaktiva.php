<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rekaktiva extends Model
{
    protected $table = 'rekaset';
    protected $primaryKey = 'kodea';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = ['asset_id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function getTanggalAttribute($value){
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function kodeperk(){
        return $this->belongsTo('App\Perkiraan','kode','kode_perk');
    }
}

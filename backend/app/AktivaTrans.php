<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AktivaTrans extends Model
{
    protected $table = 't_asset_trans';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function kodeperk(){
        return $this->belongsTo('App\Perkiraan','kode','kode_perk');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SJD extends Model
{
    protected $table = 't_sjd';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];


    public function itembarang(){
        return $this->belongsTo('App\Item','item_id');
    }
    public function sj(){
        return $this->belongsTo('App\SJ','sj_id');
    }
}

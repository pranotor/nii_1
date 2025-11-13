<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bpbd extends Model
{
    protected $table = 't_bpbd';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function bpb_head(){
        return $this->belongsTo('App\Bpb','bpb_id');
    }

    public function itembarang(){
        return $this->belongsTo('App\Item','item_id');
    }
}

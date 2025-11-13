<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuttingD extends Model
{
    protected $table = 't_cutting_item';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];
    protected $casts = [
        'use_sn' => 'boolean',
    ];

    public function cut_head(){
        return $this->belongsTo('App\Cutting','ct_id');
    }

    public function itembarang(){
        return $this->belongsTo('App\Item','item_id');
    }

}

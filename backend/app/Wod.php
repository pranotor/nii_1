<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wod extends Model
{
    protected $table = 't_wo_item';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];


    public function itembarang(){
        return $this->belongsTo('App\Item','item_id');
    }
}

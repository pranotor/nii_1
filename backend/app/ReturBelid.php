<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturBelid extends Model
{
    protected $table = 't_retur_belid';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];

    public function itembarang(){
        return $this->belongsTo(Item::class,'item_id');
    }

    public function header(){
        return $this->belongsTo(ReturBeli::class,'ret_id');
    }
}

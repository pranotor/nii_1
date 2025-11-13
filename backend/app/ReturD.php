<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturD extends Model
{
    protected $table = 't_returd';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];


    public function itembarang(){
        return $this->belongsTo('App\Item','item_id');
    }
}

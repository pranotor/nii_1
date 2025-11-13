<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Tandatangan extends Model
{
    protected $table = 't_ttd';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    //protected $guarded = ['id'];
    protected $guarded = ['id'];

    public function getUpdatedAtAttribute($value){
        return Carbon::parse($value)->format('Y-m-d');
    } 

    public function pejabat(){
        return $this->hasMany('App\Ttdetail','ttd_id','id');
    }
}

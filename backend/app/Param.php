<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Param extends Model
{
    protected $table = 't_param';
    protected $primaryKey = 'param_kode';
    public $incrementing = true;
    public $timestamps = false;
    //protected $guarded = ['id'];
    protected $fillable = ['param_kode','param_key','param_value','input_type','input_default'];

    public function group(){
        return $this->belongsTo('App\Group','group_id');
    }
}

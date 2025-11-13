<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 't_role';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function menus()
    {
        return $this->belongsToMany('App\Menu');
    }

    public function wewenang(){
        return $this->hasMany('App\MenuRole','role_id');
    }
}

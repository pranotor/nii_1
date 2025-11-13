<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 't_menu';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    protected $casts = [
        'public' => 'boolean',
    ];

    public function submenu(){
        return $this->hasMany('App\Menu','parent','id');
    }

    public function child()
    {
        return $this->submenu()->with('child');
    }

    public function getChildAttribute()
    {
        if(empty($this->child())){
            return null;
        }
    }
}

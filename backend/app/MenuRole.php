<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuRole extends Model
{
    protected $table = 'menu_role';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    //protected $guarded = ['id'];
    protected $fillable = ['menu_id','role_id','create_auth','edit_auth','delete_auth'];

    protected $casts = [
        'create_auth' => 'boolean',
        'edit_auth' => 'boolean',
        'delete_auth' => 'boolean',
    ];

    public function menudetail(){
        return $this->belongsTo('App\Menu','menu_id');
    }

}

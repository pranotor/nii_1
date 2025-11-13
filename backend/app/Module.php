<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'module_controller';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = true;
    //protected $guarded = ['id'];
    protected $fillable = ['unit','nm_unit','kota'];
}

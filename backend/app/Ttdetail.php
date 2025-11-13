<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ttdetail extends Model
{
    protected $table = 't_ttd_detail';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    //protected $guarded = ['id'];
    protected $guarded = ['id'];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    public $table = 't_bank';
    protected $primaryKey = 'bank_id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['bank_id'];
}

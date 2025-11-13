<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusPosting extends Model
{
    public $table = 't_status_posting';
    public $incrementing = true; 
    public $timestamps = false;
    protected $primaryKey = 'status';
    protected $fillable = [
    'status_name'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusCustomer extends Model
{
    public $table = 't_status_customer';
    public $incrementing = true;
    public $timestamps = false;
    protected $primaryKey = 'status';
    protected $fillable = [
    'status_name'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $table = 't_customer';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];

    public function status(){
        return $this->belongsTo('App\StatusCustomer','block','status');
    }
}

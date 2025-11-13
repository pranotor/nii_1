<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusQuotation extends Model
{
    public $table = 't_status_quotation';
    public $incrementing = true; 
    public $timestamps = false;
    protected $primaryKey = 'status';
    protected $fillable = [
    'status_name'
    ];
}

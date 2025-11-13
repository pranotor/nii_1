<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    public $table = 't_sales';
    protected $primaryKey = 'sales_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
    'sales_id','nama','alamat','kota','propinsi','telepon','kontak_person','hutang','um','termin'
    ];
}

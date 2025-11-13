<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 't_quotation';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function getPAttribute($value)
    {
        if($value=='P')
            return true;
        else
            return false;
    }

    public function setPAttribute($value)
    {
        if($value)
            $this->attributes['p'] = 'P';
        else
            $this->attributes['p'] = 'N';
    }

    public function qcustomer(){
        return $this->belongsTo('App\Customer','cust_id','id');
    }

    public function status(){
        return $this->belongsTo('App\StatusQuotation','posting','status');
    }

    public function quod(){
        return $this->hasMany('App\Quod','qt_id');
    }

    public function sales(){
        return $this->belongsTo('App\Sales','sales_id');
    }

}

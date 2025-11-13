<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quod extends Model
{
    protected $table = 't_quotationd';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['no_vcr','tgl_vcr','uraian'];

    public function quo_head(){
        return $this->belongsTo('App\Quotation','qt_id');
    }

    public function workorder(){
        return $this->belongsTo('App\Wo','wo_id');
    }

    public function itembarang(){
        return $this->belongsTo('App\Item','item_id');
    }

    public function sales(){
        return $this->belongsTo('App\Sales','sales_id');
    }
}

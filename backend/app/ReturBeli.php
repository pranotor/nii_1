<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturBeli extends Model
{
    protected $table = 't_retur_beli';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(ReturBelid::class, 'ret_id');
    }
}

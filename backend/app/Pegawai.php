<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    public $table = 't_pegawai';
    protected $primaryKey = 'nik';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
    'nik','nama','jabatan'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    public $table = 't_tarifsusut';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
    'kode','nama','alamat','kota','kodepos','telepon','kontak_person','piutang','um','termin','lokasi'
    ];
}

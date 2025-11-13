<?php

namespace App\Http\Controllers;

use App\Pegawai;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StorePegawai;

class RefController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function kota()
    {
        $ret = DB::table('t_kelkota')->get();
        return Response::json($ret);
    }

    public function sales()
    {
        $ret = DB::table('t_sales')->orderBy('nama')->get();
        return Response::json($ret);
    }

    public function market()
    {
        $ret = DB::table('t_market')->orderBy('market')->get();
        return Response::json($ret);
    }


}

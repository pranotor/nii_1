<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Traits\Refno;

class ReferensiController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    use Refno;
    public function __invoke(Request $request)
    {
        $guna_id = $request['guna_id'];
        $jenis = $request['jenis'];
        if($jenis=='po')
            $date = Carbon::createFromFormat('Y-m-d', $request['tglJurnal']);
        elseif($jenis=='bpp')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='bpb')
            $date = Carbon::createFromFormat('Y-m-d', $request['tglJurnal']);
        elseif($jenis=='qt')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='ct')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='pd')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='wo')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='so')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='sr')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='sj')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='sjsr')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='rt')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='rb')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='bankin')
            $date = Carbon::createFromFormat('d-m-Y', $request['tglJurnal']);
        elseif($jenis=='susut'){
            $tgl = $request['tglJurnal']."-01-01";
            $date = Carbon::createFromFormat('Y-m-d', $tgl);
        }
        else
            $date = Carbon::createFromFormat('Y-m-d', $request['tglJurnal']);

        return $this->getnumber($jenis,$date,$guna_id);

    }
}

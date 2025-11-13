<?php

namespace App\Http\Controllers;

use App\Anggaran;

use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreAnggaran;

use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Arr;
use Carbon\Carbon;


class AnggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function anggaranlist(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        return Response::json(Anggaran::where('tahun',$data['THN'])->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnggaran $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $search  = array('.',',');
            $replace = array('','.');
            $data_inp = array();
            $data_inp['kode'] = $data['formdata']['kode'];
            $data_inp['tahun'] = $data['formdata']['tahun'];
            $data_inp['anggaran_tahunan'] = str_replace($search,$replace,$data['formdata']['anggaran_tahunan']);
            for($i=1;$i<=12;$i++){
                $var_index = "bln_".$i;
                $data_inp[$var_index] = str_replace($search,$replace,$data['formdata'][$var_index]);
            }
            
            //$data_inp['opr'] = $data['formdata']['opr'];
            if($data['formdata']['isEdit']) {
                Anggaran::where('kode',$data['formdata']['prevReferensi'])->update($data_inp);
            }
            else
                $aktiva = Anggaran::create($data_inp);
            
        }, 5);  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $aktiva = Aktiva::select(DB::raw("asset_id,tanggal,kode,kode_asset,uraian,nmr_bukti,posting,
                    gol,masa,tarif,satuan,format(jumlah,2,'de_DE') as jumlah,
                    format(harga_unit,2,'de_DE') as harga_unit,format(harga_beli,2,'de_DE') as harga_beli,format(nilai_buku,2,'de_DE') as nilai_buku"))
                    ->where('asset_id',$id)
                    ->first();
        return Response::json($aktiva);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    
    public function destroy(Dvud $dvud)
    {
        //
    }

    
}

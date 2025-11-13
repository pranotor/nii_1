<?php

namespace App\Http\Controllers;

use App\Dvud;
use App\Jurnal;
use App\ViewJurnal;
use App\Param;
use App\Drd;
use App\Voucherbayar;
use Illuminate\Http\Request;
use Response;
use DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Ui\Presets\React;
use PDF;
use Ixudra\Curl\Facades\Curl;

class DrdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $query_lembaga = Param::where('param_kode','10006')->first();
		$drdUrl = $query_lembaga['param_value'];
        $data = json_decode($request->getContent(), true);
        $arrBulan = explode("-",$data['formdata']['bulan']);

        $tgl_format = $arrBulan[0].$arrBulan[1];
        $url = sprintf($drdUrl,$arrBulan[0],$arrBulan[1]);
        $data['url'] = $url;
        $data['tgl_format'] = $tgl_format;
        
        $drd_cek = Drd::where('Tanggal',$tgl_format)->get();
        if (!count($drd_cek)){
            $response = Curl::to($url)
                    ->withTimeout(120)
                    ->get();
            $resp = json_decode($response);
            $data['resp'] = $resp;
            DB::transaction(function () use ($data) {
                foreach($data['resp'] as $rs){
                    foreach($rs as $r){
                        $det["D_K"] = $r->DebetKredit;
                        $det["Jumlah"] = str_replace(",", "", $r->Jumlah);
                        $det["Tanggal"] = $data['tgl_format'];
                        $det["Rekening"] = $r->Rekening;
                        Drd::create($det);
                    }
                }
                
                $lpp_q = Drd::select(DB::raw('Rekening,D_K,SUM(Jumlah) AS total'))
                        ->orderBy('D_K', 'asc')
                        ->groupBy('rekening')
                        ->where('Tanggal',$data['tgl_format'])
                        ->get();
                
                $arrBulan = explode("-",$data['formdata']['bulan']);
                $uraian = "Pendapatan Rekening Air  Periode ".$data['tgl_format'];
                $year = $arrBulan[0];
                $jucnt = 1;
                $ref = str_pad($jucnt, 4, "0", STR_PAD_LEFT);
                $ref .= ".2.".$arrBulan[1].".".$arrBulan[0];

                $tgl = date('Y-m-t',strtotime($arrBulan[0]."-".$arrBulan[1]."-01"));
                //$tgl = date('Y-m-d',strtotime("+1 days",strtotime($tgl)));

                foreach($lpp_q as $lpp_row){
                    $det = [];
                    if($lpp_row['D_K']=='D'){
                        $debet = trim($lpp_row['total']);
                        $kredit = 0;
                    }
                    else{
                        $kredit = trim($lpp_row['total']);
                        $debet = 0;
                    }
                    $det["tanggal"] = $tgl;
                    $det["kode"] = trim($lpp_row['Rekening']);
                    $det["debet"] = $debet;
                    $det["kredit"] = $kredit;
                    $det["jenis"] = 2;
                    $det["uraian"] = $uraian;
                    $det["referensi"] = $ref;
                    Jurnal::create($det);

                } 

                Drd::where('Tanggal',$data['tgl_format'])->update(['ref_jurnal'=>$ref]);
            
            }, 5);
        }
        // Create curl connection
        else{
            return response()->json([
                'error' => 'DRD tanggal '.$tgl_format.' Sudah pernah posting',
            ], 417);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function show(Dvud $dvud)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dvud $dvud)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dvud $dvud)
    {
        //
    }

    public function dvudlist(Request $request){
        $data = json_decode($request->getContent(), true);
        //print_r($data);
        return Response::json(Dvud::where('bayar',0)->withCount('bayar')->get());
    }

    public function bayarlist($no_vcr){
        return Response::json(Voucherbayar::where('no_vcr',$no_vcr)
                              ->with(['jbk' => function($query){
                                    $query->select('referensi','kode','debet')->where('debet', '<>', '0');
                              }])->get());
    }
}

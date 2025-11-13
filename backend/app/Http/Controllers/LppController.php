<?php

namespace App\Http\Controllers;

use App\Dvud;
use App\Jurnal;
use App\ViewJurnal;
use App\Param;
use App\Lpp;
use App\Voucherbayar;
use Illuminate\Http\Request;
use Response;
use DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Ui\Presets\React;
use PDF;
use Ixudra\Curl\Facades\Curl;
use Carbon\Carbon;

class LppController extends Controller
{

    public function lpplist(Request $request){
        $data = json_decode($request->getContent(), true);
        $q_list = DB::table('json_lpp')->select('Tanggal','ref_jurnal')->distinct()->whereYear('Tanggal',$data['THN'])->get();
        return Response::json($q_list);
    }

    public function lppdelete(Request $request){
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $referensi = $data['ref'];
            DB::table('json_lpp')->where('ref_jurnal',$referensi)->delete();
            Jurnal::where('document',$referensi)->delete();
        }, 5);
    }
    
    public function lppinit(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        $query_lembaga = Param::where('param_kode','10005')->first();
		$url = $query_lembaga['param_value'];
        $url = $url.$data['formdata']['tanggal'];
        /* $response = Curl::to($url)
                ->withTimeout(480)
                ->get(); */
        
        $lpp_cek = Lpp::where('Tanggal',$data['formdata']['tanggal'])->get();
        if (!count($lpp_cek)){
            $response = Curl::to($url)
                    ->withTimeout(480)
                    ->withHeader('Cache-Control: no-cache')
                    ->get();
            $resp = json_decode($response);
            return response()->json($resp);
        }
        // Create curl connection
        else{
            return response()->json([
                'error' => 'LPP tanggal '.$data['formdata']['tanggal'].' Sudah pernah posting',
            ], 417);
        }
    }


    public function store(Request $request)
    {
        $query_lembaga = Param::where('param_kode','10006')->first();
		$drdUrl = $query_lembaga['param_value'];
        $data = json_decode($request->getContent(), true);
        //return response()->json($data);
        DB::transaction(function () use ($data) {
            $tgl_lpp = Carbon::parse($data['formdata']['tanggal']);
            $ref_jurnal = "LPP_.".$tgl_lpp->format('Y').$tgl_lpp->format('m').$tgl_lpp->format('d');
            foreach($data['formdata']['datalpp'] as $r){
                //return response()->json($r);
                $det["JuruBayar"] = $r['JuruBayar'];
                $det["D_K"] = $r['DebetKredit'];
                $det["Jumlah"] = str_replace(",", "", $r['Jumlah']);
                $det["Tanggal"] = $data['formdata']['tanggal'];
                $det["Tgl_setor"] = $r['Tanggal'];
                $det["Rekening"] = $r['Rekening'];
                $det["ref_jurnal"] = $ref_jurnal;
                Lpp::create($det); 
            }
            
            $lpp_q = Lpp::select(DB::raw('JuruBayar,Rekening,D_K,SUM(Jumlah) AS total, Tgl_setor'))
                    ->orderBy('JuruBayar', 'asc')
                    ->orderBy('D_K', 'asc')
                    ->orderBy('Rekening', 'asc')
                    ->groupBy('rekening')
                    ->groupBy('JuruBayar')
                    ->where('Tanggal',$data['formdata']['tanggal'])
                    ->get();
            
            $juru_bayar = '';
            $prev_jid = 0;
            $jid = 0;
            
            foreach($lpp_q as $lpp_row){
                if ($juru_bayar != $lpp_row['JuruBayar']){
                    $uraian = "Penerimaan Kas Rekening Air dan Non Air Loket LOKET ".$lpp_row['JuruBayar']." Tanggal ".$data['formdata']['tanggal'];
                    $tgl_post = $lpp_row['Tgl_setor'];
                    $date = Carbon::createFromFormat('Y-m-d', $tgl_post);
                    $referensi = DB::select('SELECT MAX(CAST(SUBSTR(referensi,1,4) AS DECIMAL)) as number FROM jurnal WHERE YEAR(tanggal)='.$date->format('Y').' AND jenis=3 AND MONTH(tanggal)='.$date->format('n'));
                    //dd($referensi);	str_pad($input, 10, "-=", STR_PAD_LEFT)
                    $number = $referensi[0]->number + 1;
                    $str_ref = str_pad($number, 4, "0", STR_PAD_LEFT).".3.".$date->format('m').".".$date->format('y');
                }

                if($lpp_row['D_K']=='D'){
                    $debet = $lpp_row['total'];
                    $kredit = 0;
                }
                else{
                    $kredit = $lpp_row['total'];
                    $debet = 0;
                }
				if($debet==0 and $kredit==0)
					continue;
				
                $det = [];
                $det["tanggal"] = $tgl_post;
                $det["kode"] = $lpp_row['Rekening'];
                $det["debet"] = $debet;
                $det["kredit"] = $kredit;
                $det["jenis"] = 3;
                $det["uraian"] = $uraian;
                $det["referensi"] = $str_ref;
                $det["document"] = $ref_jurnal;;
                Jurnal::create($det);
                $juru_bayar = $lpp_row['JuruBayar'];
            } 
        
        }, 5);
    }

}

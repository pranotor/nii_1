<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Dvud;
use App\Param;
use Response;
use PDF;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use WkPdf;
use Illuminate\Support\Facades\Storage;

class VoucherRekananController extends Controller
{
    var $thnValuta = '';
    var $bulan = '';
    var $periode = '';
    var $tanggal = '';
    var $dataneraca = [];
    var $nama_lembaga = '';
    var $alamat_lembaga = '';
    var $logo_lembaga = '';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        //parent::__construct();
        DB::enableQueryLog();
        $this->thnValuta = session('thnValuta');
        $this->query_lembaga = \App\Param::where('param_kode','>=','90000')->orderBy('param_kode','ASC')->get();
        foreach($this->query_lembaga as $lbg){
            switch($lbg->param_key){
                case 'NAMA LEMBAGA':
                    $this->nama_lembaga = $lbg->param_value;
                    break;
                case 'ALAMAT':
                    $this->alamat_lembaga = $lbg->param_value;
                    break;
                case 'LOGO':
                    $this->logo_lembaga = $lbg->param_value;
                    break;
                case 'KOTKAB':
                    $this->kotkab = $lbg->param_value;
                    break;	
                case 'PEMKOT':
                    $this->pemkot = $lbg->param_value;
                    break;	    
            }
        }
    }

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
        $data = json_decode($request->getContent(), true);
        $this->thnValuta = $data['formdata']['thnvaluta'];
        $this->bulan = $data['formdata']['bulan'];
        $this->periode = $data['formdata']['periode']; 
        $statusvcr = $data['formdata']['statusvcr'];
        $rekanan = $data['formdata']['rekanan'];
        if(!empty($data['formdata']['tanggal'])){
            $tanggal = implode(",",$data['formdata']['tanggal']);
            $tanggal = "'".$tanggal."'";
            $str_tanggal = implode(" s/d ",$data['formdata']['tanggal']);
        }
        else{
            $tanggal = "";  
            $str_tanggal = "";
        }
                                                        
        $monthName = "";
        $periode = $this->periode;
        $year = $this->thnValuta;
        switch($this->periode){
            case 'bl':
                $arrBln = explode("-",$this->bulan);
                setlocale(LC_TIME, 'id_ID.UTF-8');
                $monthName = strftime('%B', mktime(0, 0, 0, $arrBln[1]));
                $bln = (int) $arrBln[1];
                $this->dataneraca = DB::select("CALL sp_voucher_rekanan(?,?,?,?,?,?)",array($year,$bln,'bl','',$statusvcr,$rekanan)); 
                break;
            case 'th' :
                $this->dataneraca = DB::select("CALL sp_voucher_rekanan(?,?,?,?,?,?)",array($year,0,'th','',$statusvcr,$rekanan)); 
                break;
            case 'range' :
                $this->dataneraca = DB::select("CALL sp_voucher_rekanan($year,0,'rg',$tanggal,'".$statusvcr."','".$rekanan."')"); 
                break;
        }
        //dd($this->dataneraca);
        $modname = get_class($this);
        $data_jurnal = $this->dataneraca;  
        $ALAMAT = $this->alamat_lembaga;
        $LEMBAGA = $this->nama_lembaga;
        $PEMKOT = $this->pemkot;
        $logo_lembaga = $request->root()."/".$this->logo_lembaga;

        $html =  view('laporan.voucherRekanan',  compact('data_jurnal','ALAMAT','LEMBAGA','PEMKOT','logo_lembaga','monthName','periode','year','modname'));
        Storage::disk('local')->put('table_vcr.html', $html);
        $nama_pdf = "report/vc_".time().".pdf";
        //return view('laporan.neracasaldo',  compact('data_jurnal','nama_lembaga','alamat_lembaga','logo_lembaga'));
          WkPdf::loadHTML($html)->setOrientation('Portrait')->setOption('margin-bottom', 20)
            ->setOption('toc', false)
            ->setOption('page-size', 'legal')
            ->save($nama_pdf);
        return response("/".$nama_pdf, 200);  
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

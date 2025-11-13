<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Perkiraan;
use App\Param;
use Response;
use PDF;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use WkPdf;

class AruskasController extends Controller
{
    var $thnValuta = '';
    var $bulan = '';
    var $periode = '';
    var $tanggal = '';
    var $dataneraca = [];
    var $labarugi = [];
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
        $monthName = "";
        $periode = $this->periode;
        $year = $this->thnValuta;
        switch($this->periode){
            case 'bl':
                $arrBln = explode("-",$this->bulan);
                setlocale(LC_TIME, 'id_ID.UTF-8');  
                $monthName = strftime('%B', mktime(0, 0, 0, $arrBln[1]));
                $bln = (int) $arrBln[1];
                $str_where = " MONTH(tanggal) <= $bln and YEAR(tanggal)='".$this->thnValuta."'";
                break;
            case 'th' :
                $str_where = " YEAR(tanggal)='".$this->thnValuta."'";
                break;
        }

        $query = "SELECT l.kodelak,l.`klplak`,l.`namalak`, SUM((SELECT IFNULL(SUM(debet),0)-IFNULL(SUM(kredit),0) FROM jurnal AS j WHERE SUBSTR(kode,1,5)= l.`kd_sub` AND MONTH(j.tanggal) <= $bln AND YEAR(j.tanggal)=".$this->thnValuta.")) AS total,
                SUM((SELECT IFNULL(SUM(debet),0)-IFNULL(SUM(kredit),0) FROM jurnal AS j0 WHERE SUBSTR(kode,1,5)= l.`kd_sub` AND MONTH(j0.tanggal) <= $bln-1 AND YEAR(j0.tanggal)=".$this->thnValuta.")) AS total0
                FROM lak_mapping AS l
                GROUP BY l.kodelak ORDER BY l.kodelak";

        $query_lr = "SELECT f_labarugi('2021','8') AS lr";
        
        $this->dataneraca = DB::select($query); 
        $this->labarugi = DB::select($query_lr); 

        $modname = get_class($this);
        $data_jurnal = $this->dataneraca; 
        $laba_rugi =  $this->labarugi;
        $ALAMAT = $this->alamat_lembaga;
        $LEMBAGA = $this->nama_lembaga;
        $PEMKOT = $this->pemkot;
        $logo_lembaga = $request->root()."/".$this->logo_lembaga;

        $html =  view('laporan.aruskas',  compact('data_jurnal','laba_rugi','ALAMAT','LEMBAGA','PEMKOT','logo_lembaga','monthName','periode','year','modname'));
        $nama_pdf = "report/ak_".time().".pdf";
        //return view('laporan.neracasaldo',  compact('data_jurnal','nama_lembaga','alamat_lembaga','logo_lembaga'));
          WkPdf::loadHTML($html)->setOrientation('Portrait')->setOption('margin-bottom', 20)
            ->setOption('toc', false)
            ->setOption('page-size', 'Legal')
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

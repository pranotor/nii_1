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
use Carbon\Carbon;

class DaftaraktivaController extends Controller
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
        $jenis = $data['formdata']['jenis'];
        
        if(!empty($data['formdata']['jenisAktiva'])){
            $perkiraan = implode('","',$data['formdata']['jenisAktiva']);
            $perkiraan = '"'.$perkiraan.'"';
        }
        else
            $perkiraan = "";                                            
        $monthName = "";
        $periode = $this->periode;
        $year = $this->thnValuta;
        switch($this->periode){
            case 'bl':
                $arrBln = explode("-",$this->bulan);
                setlocale(LC_TIME, 'id_ID.UTF-8');
                //$monthName = strftime('%B', mktime(0, 0, 0, $arrBln[1]));
                $monthName = Carbon::create($arrBln[0], $arrBln[1], 1, 0, 0, 0, 'Asia/Jakarta')->translatedFormat('F Y');
                $periode_awal = $monthName;
                //$periode_akhir = Carbon::create($arrBln[0], $arrBln[1], 1, 0, 0, 0, 'Asia/Jakarta')->translatedFormat('t F Y');
                //$periode_sebelum = Carbon::create($arrBln[0], $arrBln[1], 1, 0, 0, 0, 'Asia/Jakarta')->subDays(1)->translatedFormat('t F Y');
                $periode_akhir = Carbon::create($arrBln[0], $arrBln[1], 1, 0, 0, 0, 'Asia/Jakarta');
                $periode_sebelum = Carbon::create($arrBln[0], $arrBln[1], 1, 0, 0, 0, 'Asia/Jakarta')->subDays(1);
                $bln = (int) $arrBln[1];
                $str_tgl_awal = $this->bulan."-01";
                $tgl_akhir = Carbon::parse($str_tgl_awal)->format('Y-m-t');
                $query = "SELECT  a.asset_id,a.uraian,a.koreksi,a.kode,r.nama_perk AS nama,a.`kode_asset`,a.`tanggal`,a.jumlah,a.satuan,a.`gol`,a.`masa`,a.`tarif`,a.`harga_beli`,a.`akum_susut`,
                        a.turun,a.tgl_turun,a.naik,a.tgl_naik,IFNULL(t.`beban_susut`,0) AS susut_ini, IFNULL((t.`akum_susut`- t.beban_susut),0) AS susut_lalu,
                        a.akum_susut_lalu as akum_susut_blm_koreksi, a.nilai_buku_lalu as nilai_buku_blm_koreksi,
                        r.`parent`,r1.kode_perk as kp, r1.`nama_perk` as np FROM t_asset AS a
                        INNER JOIN rekening AS r ON a.`kode` = r.`kode_perk`
                        INNER JOIN rekening AS r1 ON r.`parent` = r1.`kode_perk`
                        LEFT JOIN t_asset_trans AS t ON t.`asset_id` = a.`asset_id` AND t.`tahun`=".$year." AND t.`bulan`=".$bln." AND t.kode_trans=300
                        WHERE a.tanggal <= '".$tgl_akhir."' ORDER BY kode,tanggal";
                        //WHERE a.tanggal <= '".$tgl_akhir."' AND (a.tgl_hapus >= '".$tgl_akhir."' OR a.tgl_hapus='1945-08-17') ORDER BY kode,tanggal";
                $this->dataneraca = DB::select($query); 
                break;
            case 'th' :
                $periode_awal = $year;
                $periode_akhir = Carbon::create($year, 1, 1, 0, 0, 0, 'Asia/Jakarta')->translatedFormat('Y');
                $periode_sebelum = Carbon::create($year-1, 12, 1, 0, 0, 0, 'Asia/Jakarta')->translatedFormat('t F Y');
                $this->dataneraca = DB::select("CALL sp_daftar_aktiva(?,?,?,?)",array($year,0,'th',$perkiraan)); 
                break;
        }
        //dd($this->dataneraca);
        $modname = get_class($this);
        $data_jurnal = $this->dataneraca;  
        //dd($data_jurnal);
        $ALAMAT = $this->alamat_lembaga;
        $LEMBAGA = $this->nama_lembaga;
        $PEMKOT = $this->pemkot;
        $logo_lembaga = $request->root()."/".$this->logo_lembaga;

        $html =  view('laporan.daftaraktiva',  compact('data_jurnal','ALAMAT','LEMBAGA','PEMKOT','logo_lembaga','monthName','periode','year','modname','periode_awal','periode_akhir','periode_sebelum'));
        Storage::disk('local')->put('table_vcr.html', $html);
        $nama_pdf = "report/vc_".time().".pdf";
        //return view('laporan.neracasaldo',  compact('data_jurnal','nama_lembaga','alamat_lembaga','logo_lembaga'));
          WkPdf::loadHTML($html)->setOrientation('Landscape')->setOption('margin-bottom', 20)
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

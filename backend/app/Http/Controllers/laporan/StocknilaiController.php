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
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

use WkPdf;

class StocknilaiController extends Controller
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
        $mode = $data['mode'];
        if(!empty($data['formdata']['nama'])){
            $nama = $data['formdata']['nama'];
        }
        else
            $nama = "";

        $monthName = "";
        $periode = $this->periode;
        $year = $this->thnValuta;
        switch($this->periode){
            case 'bl':
                $arrBln = explode("-",$this->bulan);
                setlocale(LC_TIME, 'id_ID.UTF-8');
                $bln = (int) $arrBln[1];
                $monthName = strftime('%B', mktime(0, 0, 0, $arrBln[1]));
                $this->dataneraca = DB::select("CALL sp_stock_nilai(?,?,?)",array($year,$arrBln[1],'bl'));
                break;
            case 'th' :
                $this->dataneraca = DB::select("CALL sp_stock_nilai(?,?,?)",array($year,0,'th'));
                break;
        }

        $data_jurnal = $this->dataneraca;
        $ALAMAT = $this->alamat_lembaga;
        $LEMBAGA = $this->nama_lembaga;
        $PEMKOT = $this->pemkot;
        $logo_lembaga = $request->root()."/".$this->logo_lembaga;
        $modname = get_class($this);
        $html =  view('laporan.stocknilai',  compact('data_jurnal','ALAMAT','LEMBAGA','PEMKOT','logo_lembaga','monthName','periode','year','modname'));
        Storage::disk('local')->put('table_ns.html', $html);
        $nama_pdf = "report/stock_".time().".pdf";
        //return view('laporan.neracasaldo',  compact('data_jurnal','nama_lembaga','alamat_lembaga','logo_lembaga'));
        if($mode==0){
            WkPdf::loadHTML($html)->setOrientation('Landscape')->setOption('margin-bottom', 20)
                ->setOption('toc', false)
                ->setOption('page-size', 'Legal')
                ->save($nama_pdf);
            return response("/".$nama_pdf, 200);
        }

        else{
            //dd($data_jurnal);
            $spreadsheet = IOFactory::load('template_stock_nilai.xlsx');
            $sheet = $spreadsheet->getActiveSheet();
            $baris = 3;
            foreach($data_jurnal as $item){
                if($item->kode ==''){
                    $satuan = "";
                    $saldo = "";
                    $harga = "";
                    $nilai = "";
                    $sheet->getStyle('A'.$baris.':'.'F'.$baris)
                        ->getFont()
                        ->setBold(true)->setUnderline(true);
                }
                else{
                    $satuan = $item->satuan;
                    $saldo = $item->saldo;
                    $harga = $item->harga;
                    $nilai = $item->nilai;
                    $sheet->getStyle('A'.$baris.':'.'F'.$baris)
                        ->getFont()
                        ->setBold(false)->setUnderline(false);
                }
                if($item->perkiraan == 'JUM'){
                    $kode = "";
                }
                else{
                    $kode = $item->kode;
                }
                $sheet->setCellValue('A'.$baris,$kode);
                $sheet->setCellValue('B'.$baris,$item->uraian);
                $sheet->setCellValue('C'.$baris,$satuan);
                $sheet->setCellValue('D'.$baris,$saldo);
                $sheet->setCellValue('E'.$baris,$harga);
                $sheet->getStyle('E'.$baris)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
                $sheet->setCellValue('F'.$baris,$nilai);
                $sheet->getStyle('F'.$baris)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
                if($item->perkiraan == 'JUM'){
                    $sheet->getStyle('A'.$baris.':'.'F'.$baris)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('dddddd');
                }

                $baris++;
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="rekap_stock.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            header('Access-Control-Allow-Origin: http://'.env('SANCTUM_STATEFUL_DOMAINS'));
            header('Access-Control-Allow-Credentials: true');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }

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


    public function test(Request $request)
    {
        $this->thnValuta = 2021;
        $this->bulan = '2021-01';
        $this->periode = 'bl';
        $bln = $this->bulan;
        $monthName = "";
        $periode = $this->periode;
        $year = $this->thnValuta;
        switch($this->periode){
            case 'bl':
                $arrBln = explode("-",$this->bulan);
                $bln = (int) $arrBln[1];
                $this->dataneraca = DB::select("CALL sp_neraca_saldo_bln(?,?,?)",array($arrBln[0],$arrBln[1],'bl'));
                break;
            case 'th' :
                $this->dataneraca = DB::select("
                                SELECT kode,SUM(debet) as tot_debet, SUM(kredit) as tot_kredit
                                FROM jurnal as j
                                WHERE YEAR(tanggal)='".$this->thnValuta."'
                                GROUP BY kode");
                break;
        }
        $data_jurnal = $this->dataneraca;
        $nama_lembaga = $this->nama_lembaga;
        $alamat_lembaga = $this->alamat_lembaga;
        $logo_lembaga = $request->root()."/".$this->logo_lembaga;

        $modname = get_class($this);
        $html =  view('laporan.neracasaldo',  compact('data_jurnal','nama_lembaga','alamat_lembaga','logo_lembaga','monthName','periode','year','modname'));
        //$nama_pdf = "report/ns_".time().".pdf";
        return $html;

    }

}

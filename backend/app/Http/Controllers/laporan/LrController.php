<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Perkiraan;
use App\Param;
use App\SJ;
use App\SJD;
use Response;
use PDF;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use WkPdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;


class LrController extends Controller
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

    private function nilaippn($tgl)
    {
        $ppn = DB::table('t_ppn')->whereDate('tgl_mulai','<=',$tgl)->orderBy('id','desc')->first();
        return $ppn->ppn_value;
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
        if(!empty($data['formdata']['tanggal'])){
            $tanggal = implode(",",$data['formdata']['tanggal']);
            $tanggal = "'".$tanggal."'";
            //$str_tanggal = implode(" s/d ",$data['formdata']['tanggal']);
            //$a = [1, 2, 3, 4, 5];
            //$b = array_map(fn($n) => Carbon::parse($n)->translatedFormat('j F Y'), $data['formdata']['tanggal']);
            $b = array_map(function ($item) {
                return Carbon::parse($item)->translatedFormat('j F Y');
            } , $data['formdata']['tanggal']);
            //$tanggal1 = Carbon::parse($data['formdata']['tanggal'])->translatedFormat('j F Y')
            $str_tanggal = implode(" s/d ",$b);

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
                $monthName = Carbon::create($arrBln[0], $arrBln[1], 1, 0, 0, 0, 'Asia/Jakarta')->translatedFormat('F Y');
                $bln = (int) $arrBln[1];
                $this->dataneraca = DB::table('v_penjualan_ret')
                                    ->whereMonth('sj_tgl',$bln)
                                    ->whereYear('sj_tgl',$this->thnValuta)
                                    ->orderBy('sj_tgl')
                                    ->orderBy('sj_id')
                                    ->get();
                break;
            case 'th' :
                $this->dataneraca = DB::table('v_penjualan_ret')
                                    ->whereYear('sj_tgl',$this->thnValuta)
                                    ->orderBy('sj_tgl')
                                    ->orderBy('sj_id')
                                    ->get();
                break;
            case 'range' :
                $this->dataneraca = DB::table('v_penjualan_ret')
                                    ->whereDate('sj_tgl','>=',$data['formdata']['tanggal'][0])
                                    ->whereDate('sj_tgl','<=',$data['formdata']['tanggal'][1])
                                    ->whereYear('sj_tgl',$this->thnValuta)
                                    ->orderBy('sj_tgl')
                                    ->orderBy('sj_id')
                                    ->get();
                break;
        }
        $data_jurnal = $this->dataneraca;

        $ALAMAT = $this->alamat_lembaga;
        $LEMBAGA = $this->nama_lembaga;
        $PEMKOT = $this->pemkot;
        $logo_lembaga = $request->root()."/".$this->logo_lembaga;
        $modname = get_class($this);
        $nama_pdf = "report/ledger_".time().".pdf";
        //dd($data_jurnal);
        $html =  view('laporan.penjualanlr',  compact('data_jurnal','ALAMAT','LEMBAGA','PEMKOT','logo_lembaga','monthName','periode','year','str_tanggal','modname'));
        Storage::disk('local')->put('table_lr_bln.html', $html);
        if($mode==0){
            WkPdf::loadHTML($html)->setOrientation('Landscape')->setOption('margin-bottom', 20)
            ->setOption('toc', false)
            ->setOption('page-size', 'Legal')
            ->save($nama_pdf);

            return response("/".$nama_pdf, 200);
        }

        else{
            //dd($data_jurnal);
            $spreadsheet = IOFactory::load('template_sales_lr.xlsx');
            $sheet = $spreadsheet->getActiveSheet();
            $baris = 2;
            $grand_total = 0;
            $grand_total_lr = 0;
            foreach($data_jurnal as $item){
                $jenis = $item->jenis;
                //dd($item);
                if($item->qty_kirim==0)
                    continue;

                $sheet->setCellValue('A'.$baris,$item->sj_tgl);
                //$sheet->setCellValue('B'.$baris,$item->inv_no);
                if($jenis=='jual')
                    $sheet->setCellValue('B'.$baris,$item->inv_no);
                else
                    $sheet->setCellValue('B'.$baris,$item->sj_no);
                $sheet->setCellValue('C'.$baris,$item->nick);
                $sheet->setCellValue('D'.$baris,$item->kode_barang);
                $sheet->setCellValue('E'.$baris,$item->nama_barang2);
                $sheet->setCellValue('F'.$baris,$item->harga);
                $sheet->getStyle('F'.$baris)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
                $sheet->setCellValue('G'.$baris,$item->qty_kirim);
                $sheet->setCellValue('H'.$baris,$item->satuan);
                $sheet->setCellValue('I'.$baris,$item->discount);
                $sheet->setCellValue('J'.$baris,'0');
                $subtotal = $item->qty_kirim*$item->harga*(1-($item->discount)/100);
                if($jenis=='retur')
                    $subtotal = -1*$subtotal;
                $sheet->setCellValue('K'.$baris,$subtotal);
                $sheet->getStyle('K'.$baris)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
                //hpp
                $tot_hpp = 0;
                $arr_hpp = explode("|",$item->hpp);
                foreach($arr_hpp as $d_hpp){
                    $arr_harga_qty = explode("*@",$d_hpp);
                    if(sizeof($arr_harga_qty)>0){
                        if(isset($arr_harga_qty[1])){
                            $harga_hpp = $arr_harga_qty[1];
                            // khusus retur: qty ambil dari qty_kirim, harga ambil dari setelah simbol @
                            if($jenis=='retur'){
                                $qty_hpp = $item->qty_kirim; // abaikan qty di string hpp
                            }
                            else{
                                $qty_hpp = $arr_harga_qty[0];
                            }
                            $tot_hpp = $tot_hpp + ($harga_hpp*$qty_hpp);
                        }
                    }
                }
                $sheet->setCellValue('L'.$baris,$tot_hpp);
                $sheet->getStyle('L'.$baris)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
                $sheet->setCellValue('M'.$baris,$item->hpp);
                $sheet->setCellValue('N'.$baris,$subtotal - $tot_hpp);
                $sheet->getStyle('N'.$baris)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
                $grand_total = $grand_total + $subtotal;
                $grand_total_lr = $grand_total_lr + ($subtotal - $tot_hpp);
                $baris++;
                /* $sheet->getStyle('H'.$baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('K'.$baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $baris++;
                $sheet->setCellValue('C'.$baris,$item->nama_barang2);
                $baris++;
                $sheet->setCellValue('C'.$baris,$item->size);
                $no++; */
            }

            $sheet->mergeCells('A'.$baris.':'.'J'.$baris);
            $sheet->setCellValue('A'.$baris,"J U M L A H");
            $sheet->setCellValue('K'.$baris,$grand_total);
            $sheet->getStyle('K'.$baris)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
            $sheet->setCellValue('N'.$baris,$grand_total_lr);
            $sheet->getStyle('N'.$baris)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
            $sheet->getStyle('A'.$baris.':'.'N'.$baris)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('bbbbbb');
            $sheet->getStyle('A'.$baris.':'.'N'.$baris)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color('CCCC0000'));
            $baris++;

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="01penjualan.xlsx"');
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

}

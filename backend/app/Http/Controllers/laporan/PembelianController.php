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


class PembelianController extends Controller
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
                $this->dataneraca = DB::table('v_pembelian')
                                    ->whereMonth('bpb_tgl',$bln)
                                    ->whereYear('bpb_tgl',$this->thnValuta)
                                    ->orderBy('bpb_tgl')
                                    ->orderBy('bpb_id')
                                    ->get();
                break;
            case 'th' :
                $this->dataneraca = DB::table('v_pembelian')
                                    ->whereYear('bpb_tgl',$this->thnValuta)
                                    ->orderBy('bpb_tgl')
                                    ->orderBy('bpb_id')
                                    ->get();
                break;
            case 'range' :
                $this->dataneraca = DB::table('v_pembelian')
                                    ->whereDate('bpb_tgl','>=',$data['formdata']['tanggal'][0])
                                    ->whereDate('bpb_tgl','<=',$data['formdata']['tanggal'][1])
                                    ->whereYear('bpb_tgl',$this->thnValuta)
                                    ->orderBy('bpb_tgl')
                                    ->orderBy('bpb_id')
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
        $html =  view('laporan.pembelian',  compact('data_jurnal','ALAMAT','LEMBAGA','PEMKOT','logo_lembaga','monthName','periode','year','str_tanggal','modname'));
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
            $spreadsheet = IOFactory::load('template_pembelian.xlsx');
            $sheet = $spreadsheet->getActiveSheet();
            $baris = 2;
            $grand_total_net = 0;
            foreach($data_jurnal as $item){
                //dd($item);
                if($item->qty_terima==0)
                    continue;

                $sheet->setCellValue('A'.$baris,$item->bpb_tgl);
                $sheet->setCellValue('B'.$baris,$item->po_no);
                $sheet->setCellValue('C'.$baris,$item->bpb_no);
                $sheet->setCellValue('D'.$baris,$item->nama);
                $sheet->setCellValue('E'.$baris,$item->kode_barang);
                $sheet->setCellValue('F'.$baris,$item->qty_pesan);
                $sheet->getStyle('F'.$baris)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                $sheet->setCellValue('G'.$baris,$item->qty_terima);
                $sheet->getStyle('G'.$baris)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                $sheet->setCellValue('H'.$baris,$item->harga);
                $sheet->getStyle('H'.$baris)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                $sheet->setCellValue('I'.$baris,$item->ppn);
                $sheet->getStyle('I'.$baris)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                $sheet->setCellValue('J'.$baris,$item->duty_cost);
                $sheet->getStyle('J'.$baris)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                $sheet->setCellValue('K'.$baris,$item->freight_cost);
                $sheet->getStyle('K'.$baris)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                $sheet->setCellValue('L'.$baris,$item->qty_terima*($item->harga + $item->ppn + $item->duty_cost + $item->freight_cost));
                $sheet->getStyle('L'.$baris)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');

                $grand_total_net = $grand_total_net + $item->qty_terima*($item->harga + $item->ppn + $item->duty_cost + $item->freight_cost);
                $baris++;
                /* $sheet->getStyle('H'.$baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('K'.$baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $baris++;
                $sheet->setCellValue('C'.$baris,$item->nama_barang2);
                $baris++;
                $sheet->setCellValue('C'.$baris,$item->size);
                $no++; */
            }

            $sheet->getStyle('A'.$baris.':'.'K'.$baris)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('dddddd');
            $sheet->getStyle('A'.$baris.':'.'K'.$baris)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color('FFFF0000'));

            $sheet->setCellValue('A'.$baris,'JUMLAH');
            $sheet->setCellValue('L'.$baris,$grand_total_net);

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

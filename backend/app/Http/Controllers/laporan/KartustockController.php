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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class KartustockController extends Controller
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

    public static function copyRows( Worksheet $sheet, $srcRange, $dstCell, Worksheet $destSheet = null) {

        if( !isset($destSheet)) {
            $destSheet = $sheet;
        }

        if( !preg_match('/^([A-Z]+)(\d+):([A-Z]+)(\d+)$/', $srcRange, $srcRangeMatch) ) {
            // Invalid src range
            return;
        }

        if( !preg_match('/^([A-Z]+)(\d+)$/', $dstCell, $destCellMatch) ) {
            // Invalid dest cell
            return;
        }

        $srcColumnStart = $srcRangeMatch[1];
        $srcRowStart = $srcRangeMatch[2];
        $srcColumnEnd = $srcRangeMatch[3];
        $srcRowEnd = $srcRangeMatch[4];

        $destColumnStart = $destCellMatch[1];
        $destRowStart = $destCellMatch[2];

        $srcColumnStart = Coordinate::columnIndexFromString($srcColumnStart);
        $srcColumnEnd = Coordinate::columnIndexFromString($srcColumnEnd);
        $destColumnStart = Coordinate::columnIndexFromString($destColumnStart);

        $rowCount = 0;
        for ($row = $srcRowStart; $row <= $srcRowEnd; $row++) {
            $colCount = 0;
            for ($col = $srcColumnStart; $col <= $srcColumnEnd; $col++) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $style = $sheet->getStyleByColumnAndRow($col, $row);
                $dstCell = Coordinate::stringFromColumnIndex($destColumnStart + $colCount) . (string)($destRowStart + $rowCount);
                $destSheet->setCellValue($dstCell, $cell->getValue());
                $destSheet->duplicateStyle($style, $dstCell);

                // Set width of column, but only once per column
                if ($rowCount === 0) {
                    $w = $sheet->getColumnDimensionByColumn($col)->getWidth();
                    $destSheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setAutoSize(false);
                    $destSheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setWidth($w);
                }

                $colCount++;
            }

            $h = $sheet->getRowDimension($row)->getRowHeight();
            $destSheet->getRowDimension($destRowStart + $rowCount)->setRowHeight($h);

            $rowCount++;
        }

        foreach ($sheet->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $mergeColSrcStart = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[0]));
            $mergeColSrcEnd = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[1]));
            $mergeRowSrcStart = ((int)preg_replace("/[A-Z]*/", "", $mc[0]));
            $mergeRowSrcEnd = ((int)preg_replace("/[A-Z]*/", "", $mc[1]));

            $relativeColStart = $mergeColSrcStart - $srcColumnStart;
            $relativeColEnd = $mergeColSrcEnd - $srcColumnStart;
            $relativeRowStart = $mergeRowSrcStart - $srcRowStart;
            $relativeRowEnd = $mergeRowSrcEnd - $srcRowStart;

            if (0 <= $mergeRowSrcStart && $mergeRowSrcStart >= $srcRowStart && $mergeRowSrcEnd <= $srcRowEnd) {
                $targetColStart = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColStart);
                $targetColEnd = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColEnd);
                $targetRowStart = $destRowStart + $relativeRowStart;
                $targetRowEnd = $destRowStart + $relativeRowEnd;

                $merge = (string)$targetColStart . (string)($targetRowStart) . ":" . (string)$targetColEnd . (string)($targetRowEnd);
                //Merge target cells
                $destSheet->mergeCells($merge);
            }
        }
    }

    public static function copyStyleXFCollection(Spreadsheet $sourceSheet, Spreadsheet $destSheet) {
        $collection = $sourceSheet->getCellXfCollection();

        foreach ($collection as $key => $item) {
            $destSheet->addCellXf($item);
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
        if(!empty($data['formdata']['statusmutasi'])){
            $status = $data['formdata']['statusmutasi'];
        }
        else
            $status = "semua";
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
                $this->dataneraca = DB::select("CALL sp_kartustock(?,?,?,?,?,?)",array($year,$bln,'bl','',$status,$nama));
                break;
            case 'th' :
                $this->dataneraca = DB::select("CALL sp_kartustock(?,?,?,?,?,?)",array($year,0,'th','',$status,$nama));
                break;
            case 'range' :
                $this->dataneraca = DB::select("CALL sp_kartustock($year,0,'rg',$tanggal,$status,'".$nama."')");
                break;
        }
        $data_jurnal = $this->dataneraca;
        $ALAMAT = $this->alamat_lembaga;
        $LEMBAGA = $this->nama_lembaga;
        $PEMKOT = $this->pemkot;
        $logo_lembaga = $request->root()."/".$this->logo_lembaga;
        $modname = get_class($this);
        $nama_pdf = "report/ledger_".time().".pdf";

        $html =  view('laporan.kartustock',  compact('data_jurnal','ALAMAT','LEMBAGA','PEMKOT','logo_lembaga','monthName','periode','year','str_tanggal','modname'));
        Storage::disk('local')->put('table_lr_bln.html', $html);
        if($mode==0){
            WkPdf::loadHTML($html)->setOrientation('Portrait')->setOption('margin-bottom', 20)
            ->setOption('toc', false)
            ->setOption('page-size', 'Legal')
            ->save($nama_pdf);

            return response("/".$nama_pdf, 200);
        }

        else{
            $spreadsheet = IOFactory::load('template_kartu_stock.xlsx');
            $sheet = $spreadsheet->getActiveSheet();
            //$this->copyRows($sheet, 'A1:F3', 'A8');
            $saldo = 0;
            $baris = 4;
            $total = 0;
            $counter = 0;
            $tot_debet = 0;
            $tot_kredit = 0;
            $tot_saldo = 0;
            foreach($data_jurnal as $j){
                if($j->kode_perk=='' || is_null($j->kode_perk))
                    continue;
                if($j->kode_perk != '-'){
                    $saldo=0;
                    $baris = $baris +2;
                    $sheet->setCellValue('A'.$baris,$j->uraian.'   '.$j->kode_perk.'-'.$j->kode_barang);
                    $baris++;
                    $this->copyRows($sheet, 'A2:F3', 'A'.$baris);
                    $baris = $baris+2;
                }

                else{
                    $saldo = $saldo + $j->tambah - $j->kurang;
                    $sheet->setCellValue('A'.$baris,$j->tanggal);
                    $sheet->setCellValue('B'.$baris,$j->tambah);
                    $sheet->setCellValue('C'.$baris,$j->kurang);
                    $sheet->setCellValue('D'.$baris,$saldo);
                    $sheet->setCellValue('E'.$baris,$j->referensi);
                    $sheet->setCellValue('F'.$baris,$j->keterangan);
                    $baris++;
                }

            }

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

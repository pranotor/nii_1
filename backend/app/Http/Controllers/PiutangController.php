<?php
namespace App\Http\Controllers;
use App\Fp;
use App\Jurnal;
use App\Quotation;
use App\Quod;
use App\Piutang;
use App\SJ;
use App\SJD;
use App\Bank;
use App\KirimInvoice;
use App\Invent;
use App\Tandatangan;
use App\Perkiraan;
use App\Param;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreBpb;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Arr;
use App\Traits\Jurnaldata;
use App\Traits\Refno;
use Carbon\Carbon;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Spiritix\Html2Pdf\Converter;
use Spiritix\Html2Pdf\Input\StringInput;
use Spiritix\Html2Pdf\Output\EmbedOutput;
use Spiritix\Html2Pdf\Output\FileOutput;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

class PiutangController extends Controller
{
    use Jurnaldata;
    use Refno;

    public function __construct()
    {
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* return Response::json(Piutang::selectRaw('*,subtotal+ppn+biaya_kirim AS total,concat("false") as status')->with('cust')
                        ->where('jenis','invoice')->get()); */
        return Response::json(DB::table('v_piutang')->selectRaw('*,concat("false") as status')->where('status_piutang','blm lunas')->get());
    }

    public function piutangGrid(Request $request)
    {
        // Handle jqxGrid server-side paging, sorting, and filtering
        $data = json_decode($request->getContent(), true) ?? [];
        if (empty($data)) {
            // Fallback to query/form params if body is empty (e.g., GET requests)
            $data = $request->all();
        }

        // Defaults for jqxGrid
        $pagenum = isset($data['pagenum']) ? (int)$data['pagenum'] : 0;
        $pagesize = isset($data['pagesize']) ? (int)$data['pagesize'] : 50;
        $start = $pagenum * $pagesize;

        // Base query: only unpaid receivables as per previous behavior
        $query_count = DB::table('v_piutang')->where('status_piutang', 'blm lunas');
        $query = DB::table('v_piutang')->select('v_piutang.*')->selectRaw('concat("false") as status')->where('status_piutang', 'blm lunas');

        // Sorting (optional jqxGrid params)
        if (!empty($data['sortdatafield']) && !empty($data['sortorder'])) {
            $sortField = $data['sortdatafield'];
            $sortOrder = strtolower($data['sortorder']) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sortField, $sortOrder);
        } else {
            // Default ordering (can be adjusted to your preference)
            $query->orderBy('inv_tgl', 'desc')->orderBy('inv_no', 'desc');
        }

        // Apply pagination window
        $query->offset($start)->limit($pagesize);

        // Filtering (jqxGrid format similar to jurnallist example)
        if (isset($data['filterscount'])) {
            $filterscount = (int)$data['filterscount'];

            if ($filterscount > 0) {
                $where = " (";
                $tmpdatafield = "";
                $tmpfilteroperator = "";

                for ($i = 0; $i < $filterscount; $i++) {
                    $filtervalue = $data["filtervalue" . $i] ?? '';
                    $filtercondition = $data["filtercondition" . $i] ?? '';
                    $filterdatafield = $data["filterdatafield" . $i] ?? '';
                    $filteroperator = $data["filteroperator" . $i] ?? 0; // 0 = AND, 1 = OR (jqxGrid convention)

                    if ($tmpdatafield == "") {
                        $tmpdatafield = $filterdatafield;
                    } else if ($tmpdatafield <> $filterdatafield) {
                        $where .= ")AND(";
                    } else if ($tmpdatafield == $filterdatafield) {
                        if ($tmpfilteroperator == 0) {
                            $where .= " AND ";
                        } else $where .= " OR ";
                    }

                    // Convert date string from d-m-Y to Y-m-d for date fields if needed
                    if (in_array($filterdatafield, ['inv_tgl', 'payment_due'])) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d-m-Y', $filtervalue);
                            $filtervalue = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            // leave as-is if parsing fails
                        }
                    }

                    switch ($filtercondition) {
                        case "CONTAINS":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "%'";
                            break;
                        case "DOES_NOT_CONTAIN":
                            $where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue . "%'";
                            break;
                        case "EQUAL":
                            $where .= " " . $filterdatafield . " = '" . $filtervalue . "'";
                            break;
                        case "NOT_EQUAL":
                            $where .= " " . $filterdatafield . " <> '" . $filtervalue . "'";
                            break;
                        case "GREATER_THAN":
                            $where .= " " . $filterdatafield . " > '" . $filtervalue . "'";
                            break;
                        case "LESS_THAN":
                            $where .= " " . $filterdatafield . " < '" . $filtervalue . "'";
                            break;
                        case "GREATER_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " >= '" . $filtervalue . "'";
                            break;
                        case "LESS_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " <= '" . $filtervalue . "'";
                            break;
                        case "STARTS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '" . $filtervalue . "%'";
                            break;
                        case "ENDS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "'";
                            break;
                    }

                    if ($i == $filterscount - 1) {
                        $where .= ")";
                    }

                    $tmpfilteroperator = $filteroperator;
                    $tmpdatafield = $filterdatafield;
                }

                // Apply the raw where to both queries
                $query_count = DB::table('v_piutang')
                    ->where('status_piutang', 'blm lunas')
                    ->whereRaw($where);

                // Reset base for data query with same filters and ordering/pagination
                $query = DB::table('v_piutang')
                    ->where('status_piutang', 'blm lunas')
                    ->whereRaw($where);

                if (!empty($data['sortdatafield']) && !empty($data['sortorder'])) {
                    $sortField = $data['sortdatafield'];
                    $sortOrder = strtolower($data['sortorder']) === 'desc' ? 'desc' : 'asc';
                    $query->orderBy($sortField, $sortOrder);
                } else {
                    $query->orderBy('inv_tgl', 'desc')->orderBy('inv_no', 'desc');
                }

                $query->offset($start)->limit($pagesize);
            }
        }

        $result = $query->get();
        $total_rows = $query_count->count();

        $rows = [];
        foreach ($result as $row) {
            $rows[] = $row; // return full row; front-end can select needed columns
        }

        if ($total_rows) {
            $payload = [
                'TotalRows' => $total_rows,
                'Rows' => $rows,
            ];
        } else {
            $payload = [];
        }

        return Response::json($payload);
    }

    public function historybayarret(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $inv_no = $data['inv_no'];
        //echo $inv_no;
        return Response::json(Piutang::selectRaw('*,subtotal+ppn+biaya_kirim AS total')->with('cust')
                        ->where('inv_no',$inv_no)
                        ->where('jenis','<>','invoice')->get());
    }



    public function quotationlist(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = Quotation::whereYear('qt_tgl',$data['THN']);

        /* if($data['bpbmode']=='posting')
            return Response::json($bpb->where('posting',0)->with('supplier','status')->get());

        else */
            return Response::json($bpb->with('qcustomer','status')->get());


        /*  DB::enableQueryLog();
        Bpb::with('supplier','status')->get();
        $query_dump = DB::getQueryLog();

        print_r($query_dump); */

    }

    public function invoicinglist(Request $request){
        $data = json_decode($request->getContent(), true);
        //$bpb = SJ::where('paid',0)->whereYear('sj_tgl',$data['THN']);
        //return Response::json($bpb->with('sales.qcustomer')->get());
        return Response::json(SJ::with('sales.qcustomer')->get());
    }

    public function invoicelist(Request $request){
        $data = json_decode($request->getContent(), true);
       //$bpb = SJ::where('paid',0)->where('print',1)->whereYear('sj_tgl',$data['THN']);
       $bpb = DB::table('v_invoice');
        return Response::json($bpb->get());
    }

    public function piutanglist(Request $request){
        $data = json_decode($request->getContent(), true);
       //$bpb = SJ::where('paid',0)->where('print',1)->whereYear('sj_tgl',$data['THN']);
       $bpb = DB::table('v_piutang');
        return Response::json($bpb->get());
    }

    public function cek_overdue() {
        $today = date('Y-m-d');
        $data = DB::table('v_piutang')->select('cust_id')->where('status_piutang','blm lunas')->whereDate('payment_due','<',$today)->get();
		$dataUnique = $data->unique('cust_id');
		//echo "$today";
		//dd($dataUnique);
        foreach($dataUnique as $row){
            DB::table('t_customer')->where('id',$row->cust_id)->where('cek_due',1)->update(['block'=>1]);
        }
    }

    public function piutangxls($tanggal){
        $tgl = Carbon::createFromFormat('d-m-Y', $tanggal);
        $spreadsheet = IOFactory::load('template_piutang.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        $baris = 1;
        $first = DB::table('v_piutang')->where('status_piutang','blm lunas');
        $dataH = DB::table('v_piutang')->where('status_piutang','lunas')->where('tgl_akhir_bayar','>',$tgl->format('Y-m-d'))
                ->union($first)->orderBy('nick','ASC')->get();
        //dd($dataH);
        $baris++;
        \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder( new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder() );
        $total_inv = 0;
        $total_bayar = 0;
        $total_outstand = 0;
        foreach($dataH as $row){
            $sheet->setCellValue('A'.$baris,$row->inv_tgl);
            $sheet->getStyle('A'.$baris)
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
            $sheet->setCellValue('B'.$baris,$row->po_cust);
            $sheet->setCellValue('C'.$baris,$row->inv_no);
            $sheet->setCellValue('D'.$baris,$row->nick);
            $sheet->setCellValue('E'.$baris,$row->payment_due);
            $sheet->getStyle('E'.$baris)
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
            $sheet->setCellValue('F'.$baris,$row->total);
            $sheet->getStyle('F'.$baris)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            if($row->status_piutang=='lunas'){
                $bayar_tot = $row->bayar;
                $jum_bayar_terakhir = $row->jumlah_bayar_terakhir;
                $bayar_sebelumnya = $bayar_tot - $jum_bayar_terakhir;
                $sheet->setCellValue('G'.$baris,$bayar_sebelumnya);

                $selisih = $row->total - $bayar_sebelumnya;
                $sheet->setCellValue('H'.$baris,$selisih);
            }
            else{
                $sheet->setCellValue('G'.$baris,$row->bayar);
                $sheet->setCellValue('H'.$baris,$row->selisih);
            }

            $sheet->getStyle('G'.$baris)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

            $sheet->getStyle('H'.$baris)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            $today = date('Y-m-d');
            if($row->payment_due < $today){
                $sheet->getStyle('A'.$baris.':'.'H'.$baris)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('EADDCA');
            }
            $total_inv = $total_inv + $row->total;
            $total_bayar = $total_bayar + $row->bayar;
            $total_outstand = $total_outstand + $row->selisih;
            $baris++;
        }
        $baris++;
        $sheet->mergeCells('A'.$baris.':'.'G'.$baris);
        $sheet->setCellValue('A'.$baris,"J U M L A H   B E L U M   T E R B A Y A R");
        $sheet->getStyle('A'.$baris)
            ->getAlignment()
            ->setHorizontal('center');
        $sheet->setCellValue('H'.$baris,$total_outstand);
        $sheet->getStyle('H'.$baris)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

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


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        DB::transaction(function () use ($data) {
            $search  = array(',','.');
            $replace = array('','.');
            $po_inp = array();
            if(isset($data['formdata']['invoices'])){
                foreach($data['formdata']['invoices'] as $inv){
                    $subtotal = str_replace($search,$replace,$inv['subtotal']);
                    $ppn = str_replace($search,$replace,$inv['ppn']);
                    if(isset($inv['biaya_kirim'])){
                        if($inv['biaya_kirim']=='')
                            $biaya_kirim=0;
                        else
                            $biaya_kirim = str_replace($search,$replace,$inv['biaya_kirim']);
                    }
                    else
                        $biaya_kirim = 0;


                    $sj = SJ::where('inv_no',$inv['inv_no'])->first();
                    $data_update['biaya_kirim'] = $biaya_kirim;
                    $data_update['is_payable'] = 1;
                    $sj->update($data_update);
                }
            }
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
        $data = SJ::where('inv_no',$id)->with('sales')->with('sales.qcustomer')->first();
        return Response::json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function invoice_fp(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $inv_no = $data['formdata']['inv_no'];
            $no_fp = $data['formdata']['no_fp'];
            $prevFp = $data['formdata']['prevFp'];
            SJ::where('inv_no',$inv_no)->update(['no_fp'=>$no_fp]);
            $arr_no_fp = explode(".",$no_fp);
            $kode_depan = $arr_no_fp[0].".".$arr_no_fp[1];
            Fp::where('no_fp',$arr_no_fp[2])->where('kode_depan',$kode_depan)->update(['status'=>1]);
            if($prevFp != '-'){
                Fp::where('no_fp',$prevFp)->update(['status'=>0]);
            }
        }, 5);
    }

    public function invoice_bank(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $inv_no = $data['formdata']['inv_no'];
            $bank_id = $data['formdata']['bank_id'];
            SJ::where('inv_no',$inv_no)->update(['bank_id'=>$bank_id]);
        }, 5);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function destroy($inv_no)
    {
        DB::transaction(function () use ($inv_no) {
            $sj = SJ::where('inv_no',$inv_no)->first();
            DB::table('t_bank_in')->where('inv_no',$inv_no)->delete();
            DB::table('t_piutang_bayar')->where('inv_no',$inv_no)->delete();
            $sj->update(['is_payable'=>0]);
        }, 5);
    }

    private function nilaippn($tgl)
    {
        $ppn = DB::table('t_ppn')->whereDate('tgl_mulai','<=',$tgl)->orderBy('id','desc')->first();
        return $ppn->ppn_value;
    }

    public function pdf($id,$generate=0){
        $jurnalH = SJ::where('inv_no',$id)->first();
        $bank = Bank::where('bank_id',$jurnalH->bank_id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpb_id = $jurnalH->id;
        $p = $jurnalH->p;
        $today = date('d-m-Y');
        $ppn = $this->nilaippn($jurnalH->sj_tgl);
        $ref = $jurnalH['inv_no'];
        $jurnalD = SJD::where('sj_id',$bpb_id)->where('qty_kirim','<>',0)->orderBy('kode_perk')->get();

        //$img_file = K_PATH_IMAGES."/images/copy_stamp_tipis.png";
        $img_file = url("/".$this->logo_lembaga);
        $img_file_copy = url("/images/copy_stamp_tipis.png");
        if($p=='P')
            $html =  view('jurnal.inv',  compact('jurnalH','ref','bank','jurnalD','jenis','img_file','img_file_copy','ppn'));
        else
            $html =  view('jurnal.invn',  compact('jurnalH','ref','jurnalD','jenis','img_file','img_file_copy','ppn'));
        Storage::disk('local')->put('table_inv.html', $html);
        $input = new StringInput();
        $input->setHtml($html);

        $converter = new Converter($input, new EmbedOutput());

        $converter->setOption('landscape', true);

        $header = '<div style="color: lightgray; border-top: solid lightgray 1px; font-size: 10pt; padding-top: 5px; text-align: center; width: 100%;">
        <span>This is a test message</span> - <span class="pageNumber"></span></div>';
        $converter->setOptions([
            'landscape' => false,
            'format' => 'legal',
            'printBackground' => true,
            'displayHeaderFooter' => false,
            'headerTemplate' => '<style> h1 { font-size:20px; margin-top:50px;}</style> <h1>Header</h1>',
            'footerTemplate' => '<div style="font-size:10px !important;">I am a footer</div>',
            'margin' => ['top' => '300px', 'bottom' => '40px', 'left' => '40px', 'right' => '40px']
        ]);

        $output = $converter->convert();
        $output->embed('google.pdf');

    }

    public function tt_list(Request $request) {
        $q_list = DB::table('t_ttinvoice as i')
                    ->join('t_customer as c','i.cust_id','c.id')
                    ->select('i.*','c.nama')
                    ->orderBy('tt_tgl','desc')->get();
        return Response::json($q_list);
    }
    public function tt_detail(Request $request) {
        $data = json_decode($request->getContent(), true);
        $tt_id = $data['tt_id'];
        $q_list = SJ::where('tt_id',$tt_id)->get();
        return Response::json($q_list);
    }

    public function invoiceblmkirim(Request $request){
        $data = json_decode($request->getContent(), true);
       //$bpb = SJ::where('paid',0)->where('print',1)->whereYear('sj_tgl',$data['THN']);
       $bpb = DB::table('v_invoice_blmkirim');
        return Response::json($bpb->get());
    }

    public function invoicekirim(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        DB::transaction(function () use ($data) {
            //dd($data);
            $search  = array(',','.');
            $replace = array('','.');
            $po_inp = array();
            $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['tt_tgl']);
            $po_inp['tt_tgl'] = $date->format('Y-m-d');
            $po_inp['inv_no'] = implode(',',$data['formdata']['inv_no']);
            $po_inp['cust_id'] = $data['formdata']['cust_id'];
            $po_inp['p'] = $data['formdata']['p'];

            $po = KirimInvoice::create($po_inp);
            $id = $po->id;
            if(isset($data['formdata']['invoices'])){
                foreach($data['formdata']['invoices'] as $inv){
                    $sj = SJ::where('inv_no',$inv['inv_no'])->first();
                    $data_update['tt_id'] = $id;
                    $sj->update($data_update);
                }
            }
        }, 5);
    }

    public function pdftt($id,$generate=0){
        //$jurnalH = SJ::where('inv_no',$id)->first();
        $jurnalH = DB::table('t_ttinvoice as i')
                    ->join('t_customer as c','i.cust_id','c.id')
                    ->select('i.*','c.nama')
                    ->where('i.id',$id)
                    ->orderBy('tt_tgl','desc')->first();
        //print_r($jurnalH);
        //$jurnalH = DB::table('t_ttinvoice')->where('id',$id)->first();
        $jurnalD = DB::table('v_invoice_all')->where('tt_id',$id)->get();
        //print_r($jurnalD);
        $p = $jurnalH->p;
        //$img_file = K_PATH_IMAGES."/images/copy_stamp_tipis.png";
        $img_file = url("/".$this->logo_lembaga);
        $img_file_copy = url("/images/copy_stamp_tipis.png");
        if($p=='P')
            $html =  view('jurnal.tt',  compact('jurnalH','jurnalD','img_file','img_file_copy'));
        else
            $html =  view('jurnal.tt_n',  compact('jurnalH','jurnalD','img_file','img_file_copy'));
        Storage::disk('local')->put('table_inv.html', $html);
        $input = new StringInput();
        $input->setHtml($html);

        $converter = new Converter($input, new EmbedOutput());

        $converter->setOption('landscape', true);

        $header = '<div style="color: lightgray; border-top: solid lightgray 1px; font-size: 10pt; padding-top: 5px; text-align: center; width: 100%;">
        <span>This is a test message</span> - <span class="pageNumber"></span></div>';
        $converter->setOptions([
            'landscape' => false,
            'format' => 'legal',
            'printBackground' => true,
            'displayHeaderFooter' => false,
            'headerTemplate' => '<style> h1 { font-size:20px; margin-top:50px;}</style> <h1>Header</h1>',
            'footerTemplate' => '<div style="font-size:10px !important;">I am a footer</div>',
            'margin' => ['top' => '300px', 'bottom' => '40px', 'left' => '80px', 'right' => '80px']
        ]);

        $output = $converter->convert();
        $output->embed('google.pdf');

    }

    public function invoicexls($id,$generate=0){
        $spreadsheet = IOFactory::load('template_inv.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        $baris = 1;
        $dataH = SJ::where('inv_no',$id)->first();
        $tgl_inv = Carbon::parse($dataH['sj_tgl'])->translatedFormat('d-M-Y');
        //dd($dataH);
        $jenis = 0;
        $bpb_id = $dataH->id;
        $today = date('d-m-Y');
        $ref = $dataH['inv_no'];
        $dataD = SJD::where('sj_id',$bpb_id)->where('qty_kirim','<>',0)->orderBy('kode_perk')->get();
        $sheet->setCellValue('I'.$baris,'Tanggal    : '.$tgl_inv);
        $baris++;
        $sheet->setCellValue('A'.$baris,$dataH->sales->qcustomer->nama);
        $sheet->setCellValue('I'.$baris,'No Faktur : '.$dataH->inv_no);
        $baris += 4;
        $sheet->setCellValue('A'.$baris,'No. PO : '.$dataH->sales->po_cust);
        $baris +=6;
        $no = 1;
        $total = 0;
        $subtotal = 0;
        foreach($dataD as $item){
            $sheet->insertNewRowBefore($baris);
            if($item->qty_kirim==0)
                    continue;
            $sheet->setCellValue('A'.$baris,$no);
            if($item->kode2 != '')
                    $kode_barang = $item->kode2;
                else
                    $kode_barang = $item->kode_barang;
            if($item->nama_barang2 != '')
                $nama_barang = $item->nama_barang2;
            else
                $nama_barang = $item->itembarang->perkiraan->nama_perk;
            $sheet->setCellValue('C'.$baris,$kode_barang);
            $sheet->setCellValue('E'.$baris,$nama_barang);
            $sheet->setCellValue('I'.$baris,$item->qty_kirim);
            $sheet->setCellValue('J'.$baris,$item->satuan);
            $netto = $item->harga - ($item->harga * $item->discount/100);
            $sheet->setCellValue('K'.$baris,$netto);
            $sum_subtotal = "I".$baris."*K".$baris;
            $sheet->setCellValue('M'.$baris,'='.$sum_subtotal);
            //$this->copyRows($sheet, 'A'.$baris.':M'.$baris, 'A'.$baris+1);
            $subtotal = $item->qty_kirim * $netto;
            $no++;
            $baris = $baris+1;
            $arr_namabarang = array();
            if($item->size != ''){
                $search  = array("\n",":");
                $replace = array('|',':|');
                $str_namabarang = str_replace($search,$replace,$item->size);
                //$str_namabarang = str_replace("\n",'|',$item->size);
                $arr_namabarang = explode('|', $str_namabarang);
            }
            for($i=0;$i<sizeof($arr_namabarang);$i++){
                    $sheet->setCellValue('E'.$baris,$arr_namabarang[$i]);
                    $baris++;
                    $sheet->insertNewRowBefore($baris+3);
            }
            $total = $total + $subtotal;
        }
        $baris = $baris + 7;
        $sheet->setCellValue('A'.$baris,'Terbilang : '.terbilang($total));

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

    public function invprint($id,$generate=0){
        //dd(PHP_EOL);
        $dataH = SJ::where('inv_no',$id)->first();
        SJ::where('inv_no',$id)->update(['print'=>1,'print_tgl'=>date('Y-m-d')]);
        //dd($dataH);
        $jenis = 0;
        $bpb_id = $dataH->id;
        $today = date('d-m-Y');
        $ref = $dataH['inv_no'];
        $dataD = SJD::where('sj_id',$bpb_id)->where('qty_kirim','<>',0)->orderBy('kode_perk')->get();
        $alamat = wordwrap($dataH['sales']['qcustomer']['alamat'], 28, "|", true);
        $arr_alamat = explode("|",$alamat);

        $msg = array();
        array_push($msg,"\x1B\x69\x61\x00  \x1B\x40");//set printer to ESC/P mode and clear memory buffer
        array_push($msg,"\x1B\x55\x02", "\x1B\x33\x0F");// set margin (02) and line feed (0F) values
        //array_push($msg,"\x1B\x6B\x0B\x1B\x58\x00\x3c\x00");// set font and font size esc/p2
        //array_push($msg,"\x1B\x6B\x01\x1B\x58\x18\x15\x00");// set font and font size esc/p2
        array_push($msg,"\x1B\x6B\x02\x1B\x67");// set font and font size esc/p2
        //array_push($msg,"\x1B\x21\x04");// set font condensed
        //array_push($msg,"\x1B  \x77  \x01");// set font and font size esc/p 2x 10.5
        $no = 1;
        $i = 1;
        $subtotal = 0;
        foreach($dataD->chunk(3) as $chunk){
            array_push($msg,"\x0A");// line feed 1 times
            array_push($msg,str_pad("Kepada YTH : ",80,"_",STR_PAD_RIGHT));// tab 8 times
            array_push($msg,str_pad("Tanggal","10","_",STR_PAD_RIGHT).": ".\Carbon\Carbon::parse($dataH['sj_tgl'])->translatedFormat('d-M-y'));// tab 8 times
            array_push($msg,"\x0A\x0A");// line feed 3 times

            array_push($msg,str_pad($dataH['sales']['qcustomer']['nama'],80,"_",STR_PAD_RIGHT));
            array_push($msg,str_pad("No Faktur","10","_",STR_PAD_RIGHT).": ".$dataH['inv_no']);
            array_push($msg,"\x0A\x0A");// line feed 2 times
            for($i=0;$i<sizeof($arr_alamat);$i++){
                array_push($msg,$arr_alamat[$i]);
                array_push($msg,"\x0A\x0A");// line feed 2 times
            }
            array_push($msg,"No PO");
            array_push($msg,"\x09\x09");// tab
            array_push($msg,": ".str_pad($dataH->sales->po_cust,26,"_",STR_PAD_RIGHT));

            array_push($msg,"\x0A\x0A\x0A\x0A");// line feed 2 times
            //array_push($msg,"\x1B\x61\x02");// align right
            array_push($msg,"x1B\x58\x18\x15\x00");// set font and font size esc/p 1x 10.5
            array_push($msg,"No");
            array_push($msg,"\x09");// tab
            array_push($msg,str_pad("Kode Barang",30,"_",STR_PAD_RIGHT));
            //array_push($msg,"\x09\x09\x09");// tab
            array_push($msg,str_pad("Nama Barang",30,"_",STR_PAD_RIGHT));
            //array_push($msg,"\x09\x09\x09\x09\x09\x09\x09");// tab
            array_push($msg,str_pad("Qty",10,"_",STR_PAD_RIGHT));
            array_push($msg,str_pad("Harga",16,"_",STR_PAD_BOTH));
            array_push($msg,str_pad("Total",16,"_",STR_PAD_BOTH));
            array_push($msg,"\x0A\x0A");// line feed 2 times
            array_push($msg,"- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -");
            array_push($msg,"\x0A\x0A");// line feed 2 times
            //data product

            $current_row = 0;
            foreach($chunk as $item){
                $netto = $item->harga - ($item->harga * $item->discount/100);
                $total_i = $item->qty_kirim * $netto;
                if($item->qty_kirim==0)
                    continue;
                //array_namabarang
                $arr_namabarang = array();
                if($item->size != ''){
                    $search  = array("\n",":");
                    $replace = array('|',':|');
                    $str_namabarang = str_replace($search,$replace,$item->size);
                    //$str_namabarang = str_replace("\n",'|',$item->size);
                    $arr_namabarang = explode('|', $str_namabarang);
                }

                array_push($msg,$no);
                array_push($msg,"\x09");// tab
                if($item->kode2 != '')
                    array_push($msg,str_pad($item->kode2,30,"_",STR_PAD_RIGHT));
                else
                    array_push($msg,str_pad($item->kode_barang,30,"_",STR_PAD_RIGHT));
                //array_push($msg,"\x09\x09\x09\x09");// tab
                if($item->nama_barang2 != '')
                    array_push($msg,str_pad($item->nama_barang2,30,"_",STR_PAD_RIGHT));
                else
                    array_push($msg,str_pad($item->itembarang->itemperkiraan->nama_perk,30,"_",STR_PAD_RIGHT));
                //array_push($msg,"\x09\x09\x09\x09\x09\x09\x09");// tab
                array_push($msg,str_pad($item->qty_kirim,5,"_",STR_PAD_RIGHT));
                array_push($msg,str_pad($item->satuan,5,"_",STR_PAD_RIGHT));
                array_push($msg,str_pad(number_format($netto,2),16,"_",STR_PAD_LEFT));
                array_push($msg,str_pad(number_format($total_i,2),16,"_",STR_PAD_LEFT));
                array_push($msg,"\x0A\x0A");// line feed 2 times
                for($i=0;$i<sizeof($arr_namabarang);$i++){
                    array_push($msg,str_repeat("_", 2));
                    array_push($msg,"\x09");// tab
                    array_push($msg,str_repeat("_", 30));// tab
                    array_push($msg,trim($arr_namabarang[$i]));
                    array_push($msg,"\x0A\x0A");// line feed 2 times
                    $current_row++;
                }
                $no++;
                $subtotal = $subtotal + $total_i;
            }
            $jumlah_baris = 10;
            for($j=1;$j<=$jumlah_baris-$current_row;$j++){
            //for($i = $no; $no < $jumlah_baris ; $i++){
                array_push($msg,"\x0A\x0A");// line feed 2 times
            }
           array_push($msg,"- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -");
            array_push($msg,"\x0A\x0A");// line feed 2 times
            array_push($msg,str_pad("Total",70,"_",STR_PAD_LEFT));
            array_push($msg,str_repeat("_", 16));// tab
            array_push($msg,str_pad(number_format($subtotal,2),20,"_",STR_PAD_LEFT));
            array_push($msg,"\x0A\x0A");// line feed 2 times
            array_push($msg,"Terbilang : ".terbilang($subtotal));
            array_push($msg,"\x0A\x0A");// line feed 2 times
            array_push($msg,"Keterangan : ");
            array_push($msg,"\x0A\x0A");// line feed 2 times
            //array_push($msg,str_pad("Bank","10","_",STR_PAD_RIGHT));
            //array_push($msg,": BCA");
            array_push($msg,"\x0A\x0A");// line feed 2 times
            //array_push($msg,str_pad("Address","10","_",STR_PAD_RIGHT));
            //array_push($msg,": Komp. Ruko Kopo Plaza ");
            array_push($msg,str_pad("__","10","_",STR_PAD_RIGHT));
            array_push($msg,"_________________________");
            array_push($msg,"\x09\x09\x09\x09\x09\x09\x09");// tab
            array_push($msg,"Hormat Kami");
            array_push($msg,"\x0A\x0A");// line feed 2 times
            //array_push($msg,str_pad("Acc No","10","_",STR_PAD_RIGHT));
            //array_push($msg,": 453.1139.849");
            //array_push($msg,"\x0A\x0A");// line feed 2 times
            //array_push($msg,str_pad("Acc Name","10","_",STR_PAD_RIGHT));
            //array_push($msg,": YODI");
            //array_push($msg,"\x0A\x0A");// line feed 2 times
            array_push($msg,"\x0C");// <--- Tells the printer to print
        }


        return response()->json($msg, 200,[]);
    }

    public function sjprint($id,$generate=0){
        //dd(PHP_EOL);
        $dataH = SJ::where('inv_no',$id)->first();
        SJ::where('inv_no',$id)->update(['print'=>1,'print_tgl'=>date('Y-m-d')]);
        //dd($dataH);
        $jenis = 0;
        $bpb_id = $dataH->id;
        $today = date('d-m-Y');
        $ref = $dataH['inv_no'];
        $dataD = SJD::where('sj_id',$bpb_id)->where('qty_kirim','<>',0)->orderBy('kode_perk')->get();
        $alamat = wordwrap($dataH['sales']['qcustomer']['alamat'], 28, "|", true);
        $arr_alamat = explode("|",$alamat);

        //dd($arr_alamat);
        /*
        '\x1B' + '\x69' + '\x61' + '\x00' + '\x1B' + '\x40', // set printer to ESC/P mode and clear memory buffer
                    //'\x1B' + '\x69' + '\x4C' + '\x01', // set landscape mode
                    '\x1B' + '\x55' + '\x02', '\x1B' + '\x33' + '\x0F', // set margin (02) and line feed (0F) values
                    '\x1B' + '\x6B' + '\x0B' + '\x1B' + '\x58' + '\x00' + '\x3c' + '\x00', // set font and font size esc/p2
                    '\x1B' + '\x77' + '\x01', // set font and font size esc/p 2x 10.5
                    '\x1B' + '\x21' + '\x04', // set font condensed
                    '\x09'+'\x09',// tab 2 times
                    '\x09'+'\x09',// tab 2 times
                    '\x09'+'\x09',// tab 2 times
                    '\x09'+'\x09',// tab 2 times
                    '\x09'+'\x09',// tab 2 times
                    'Tanggal ', // "Printed by "
                    '25-Jan-22', // "QZ-Tray"
                    '\x0A'+'\x0A',// line feed 2 times
                    //'\x1B' + '\x61' + '\x02' ,// align right
                    //'Kepada YTH ', // "Printed by "
                    //'\x0A'+'\x0A',// line feed 2 times
                    //'Kepada YTH ', // "Printed by "
                    '\x0A' +'\x0A',// line feed 2 times
                    '\x0C' // <--- Tells the printer to print
        */
        $msg = array();
        array_push($msg,"\x1B\x69\x61\x00  \x1B\x40");//set printer to ESC/P mode and clear memory buffer
        array_push($msg,"\x1B\x55\x02", "\x1B\x33\x0F");// set margin (02) and line feed (0F) values
        //array_push($msg,"\x1B\x6B\x0B\x1B\x58\x00\x3c\x00");// set font and font size esc/p2
        //array_push($msg,"\x1B\x6B\x01\x1B\x58\x18\x15\x00");// set font and font size esc/p2
        array_push($msg,"\x1B\x6B\x02\x1B\x67");// set font and font size esc/p2
        //array_push($msg,"\x1B\x21\x04");// set font condensed
        //array_push($msg,"\x1B  \x77  \x01");// set font and font size esc/p 2x 10.5
        $no = 1;
        foreach($dataD->chunk(3) as $chunk){
            array_push($msg,"\x0A");// line feed 1 times
            array_push($msg,"\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09");// tab
            array_push($msg,"Tanggal : ".\Carbon\Carbon::parse($dataH['sj_tgl'])->translatedFormat('d-M-y'));// tab 8 times
            array_push($msg,"\x0A\x0A");// line feed 3 times
            array_push($msg,"\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09");// tab
            array_push($msg,"Kepada YTH : ");// tab 8 times
            array_push($msg,"\x0A\x0A");// line feed 3 times
            array_push($msg,"\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09");// tab
            array_push($msg,$dataH['sales']['qcustomer']['nama']);
            array_push($msg,"\x0A\x0A");// line feed 2 times
            for($i=0;$i<sizeof($arr_alamat);$i++){
                array_push($msg,"\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09");// tab
                array_push($msg,$arr_alamat[$i]);
                array_push($msg,"\x0A\x0A");// line feed 2 times
            }
            array_push($msg,"No Surat Jalan  : ".$dataH['inv_no']);
            array_push($msg,"\x0A\x0A");// line feed 2 times
            array_push($msg,"No PO");
            array_push($msg,"\x09\x09");// tab
            array_push($msg,": ".str_pad($dataH->sales->po_cust,26,"_",STR_PAD_RIGHT));
            //array_push($msg,": ".$dataH->sales->po_cust);
            //if(strlen($dataH->sales->po_cust) < 10)
            //     array_push($msg,"\x09\x09");// tab
            //if(strlen($dataH->sales->po_cust) >= 10 && strlen($dataH->sales->po_cust) < 16)
            //     array_push($msg,"\x09");// tab
            array_push($msg,"\x09");// tab
            array_push($msg,"\x1B\x57\x31");// set font and font size esc/p 2x 10.5
            array_push($msg,"\x1B\x77\x31");// set font and font size esc/p 2x 10.5
            array_push($msg,"Surat Jalan  ");
            array_push($msg,"\x1B\x57\x30");// set font and font size esc/p 2x 10.5
            array_push($msg,"\x1B\x77\x30");// set font and font size esc/p 2x 10.5
            array_push($msg,"\x09");// tab
            array_push($msg,"Telp : ".$dataH['sales']['qcustomer']['telepon']);
            array_push($msg,"\x0A\x0A\x0A\x0A");// line feed 2 times
            //array_push($msg,"\x1B\x61\x02");// align right
            array_push($msg,"x1B\x58\x18\x15\x00");// set font and font size esc/p 1x 10.5
            array_push($msg,"No");
            array_push($msg,"\x09");// tab
            array_push($msg,str_pad("Kode Barang",30,"_",STR_PAD_RIGHT));
            //array_push($msg,"\x09\x09\x09");// tab
            array_push($msg,str_pad("Nama Barang",50,"_",STR_PAD_RIGHT));
            //array_push($msg,"\x09\x09\x09\x09\x09\x09\x09");// tab
            array_push($msg,"Qty");
            array_push($msg,"\x0A\x0A");// line feed 2 times
            array_push($msg,"- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ");
            array_push($msg,"\x0A\x0A");// line feed 2 times
            //data product

            $current_row = 0;
            foreach($chunk as $item){
                if($item->qty_kirim==0)
                    continue;
                //array_namabarang
                $arr_namabarang = array();
                if($item->size != ''){
                    $str_namabarang = str_replace("\n",'|',$item->size);
                    $arr_namabarang = explode('|', $str_namabarang);
                }

                array_push($msg,$no);
                array_push($msg,"\x09");// tab
                if($item->kode2 != ''){
                    $kode2 = wordwrap($item->kode2, 30, "|", true);
                    $arr_kode2 = explode("|",$kode2);
                    array_push($msg,str_pad($arr_kode2[0],30,"_",STR_PAD_RIGHT));
                }
                else
                    array_push($msg,str_pad($item->kode_barang,30,"_",STR_PAD_RIGHT));
                //array_push($msg,"\x09\x09\x09\x09");// tab
                if($item->nama_barang2 != '')
                    array_push($msg,str_pad($item->nama_barang2,50,"_",STR_PAD_RIGHT));
                else
                    array_push($msg,str_pad($item->itembarang->itemperkiraan->nama_perk,50,"_",STR_PAD_RIGHT));
                //array_push($msg,"\x09\x09\x09\x09\x09\x09\x09");// tab
                array_push($msg,$item->qty_kirim);
                array_push($msg,"\x09");// tab
                array_push($msg,$item->satuan);
                array_push($msg,"\x0A\x0A");// line feed 2 times
                if(sizeof($arr_kode2) > 1){
                    for($i=1;$i<sizeof($arr_kode2);$i++){
                        array_push($msg,"\x09");// tab
                        array_push($msg,$arr_kode2[$i]);
                        array_push($msg,"\x0A\x0A");// line feed 2 times
                        $current_row++;
                    }
                }
                for($i=0;$i<sizeof($arr_namabarang);$i++){
                    array_push($msg,"\x09");// tab
                    array_push($msg,str_repeat("_", 30));// tab
                    array_push($msg,$arr_namabarang[$i]);
                    array_push($msg,"\x0A\x0A");// line feed 2 times
                    $current_row++;
                }
                $no++;
            }
            $jumlah_baris = 10;
            for($j=1;$j<=$jumlah_baris-$current_row;$j++){
            //for($i = $no; $no < $jumlah_baris ; $i++){
                array_push($msg,"\x0A\x0A");// line feed 2 times
            }
            array_push($msg,"\x09\x09\x09");// tab
            array_push($msg,"Diterima oleh");
            array_push($msg,"\x09\x09\x09\x09");// tab
            array_push($msg,"Check");
            array_push($msg,"\x09\x09\x09\x09");// tab
            array_push($msg,"Hormat Kami");
            array_push($msg,"\x0C");// <--- Tells the printer to print
        }


        return response()->json($msg, 200,[]);
    }

    public function fp($id,$generate=0){
        $jurnalH = SJ::where('inv_no',$id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpb_id = $jurnalH->id;
        $today = date('d-m-Y');
        $ppn = $this->nilaippn($jurnalH->sj_tgl);
        $ref = $jurnalH['inv_no'];
        $jurnalD = SJD::where('sj_id',$bpb_id)->orderBy('kode_perk')->get();
        $alamat = wordwrap($jurnalH->sales->qcustomer->alamat, 42, "|", true);
        $arr_alamat = explode("|",$alamat);
        $spreadsheet = IOFactory::load('template_fp.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B7','Kode dan Nomor Seri Faktur Pajak :010.'.$jurnalH->no_fp);
        $sheet->setCellValue('E13', ':      '.$jurnalH->sales->qcustomer->nama);
        $baris = 14;
        for($i=0;$i<sizeof($arr_alamat);$i++){
            if($i==0)
                $sheet->setCellValue('E'.$baris, ':      '.$arr_alamat[$i]);
            else
            $sheet->setCellValue('E'.$baris, '       '.$arr_alamat[$i]);
            $baris++;
        }
        //$sheet->setCellValue('E14', ':      '.$jurnalH->sales->qcustomer->alamat);
        $sheet->setCellValue('E17', ':      '.$jurnalH->sales->qcustomer->npwp);
        $baris = 22;
        $no = 1;
        $grand_tot = 0;
        foreach($jurnalD as $item){
            if($item->qty_kirim==0)
                continue;
            $netto = $item->harga - ($item->harga * $item->discount/100);
            $total_i = $item->qty_kirim * $netto;
            $grand_tot += $total_i;
            $sheet->setCellValue('B'.$baris,$no);
            if($item->kode2 != '')
                $sheet->setCellValue('C'.$baris,$item->kode2);
            else
                $sheet->setCellValue('C'.$baris,$item->kode_barang);
            $sheet->setCellValue('H'.$baris,$item->qty_kirim.' '.$item->satuan.' X '.number_format($netto,2,',','.'));
            $sheet->setCellValue('K'.$baris,number_format($total_i,2,',','.'));
            $merge = "K".$baris.":L".$baris;
            $sheet->mergeCells($merge);
            $sheet->getStyle('H'.$baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('K'.$baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $baris++;
            $sheet->setCellValue('C'.$baris,$item->nama_barang2);
            $baris++;
            $sheet->setCellValue('C'.$baris,$item->size);
            $no++;
        }
        $baris = 35;
        $sheet->setCellValue('K'.$baris,number_format($grand_tot,2,',','.'));

        $baris = 38;
        $sheet->setCellValue('K'.$baris,number_format($grand_tot,2,',','.'));

        $baris = 39;
        $percent_ppn = $ppn*100;
        $sheet->setCellValue('B'.$baris,'  PPN = '.$percent_ppn.'% X Dasar Pengenaan Pajak');
        $sheet->setCellValue('K'.$baris,number_format($grand_tot*$ppn,2,',','.'));

        /* $writer = new Xlsx($spreadsheet);
        $writer->save('test_fp.xlsx'); */

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        header('Access-Control-Allow-Origin: *');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function fpcsv($id,$generate=0){
        $jurnalH = SJ::where('inv_no',$id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpb_id = $jurnalH->id;
        $sj_tgl = Carbon::createFromFormat('Y-m-d', $jurnalH->sj_tgl);
        $ppn = $this->nilaippn($jurnalH->sj_tgl);
        $ref = $jurnalH['inv_no'];
        $jurnalD = SJD::select(DB::raw('id,item_id,kode_barang,kode2,nama_barang2,size,harga,qty_kirim,discount,qty_kirim*harga*(1-discount) as subtotal'))->where('sj_id',$bpb_id)->orderBy('kode_perk')->get();
        $alamat = wordwrap($jurnalH->sales->qcustomer->alamat, 42, "|", true);
        $arr_alamat = explode("|",$alamat);
        //$spreadsheet = IOFactory::load('template_fp.xlsx');

        $reader = new CsvReader();
        $reader->setDelimiter(',')
            ->setEnclosure('"')
            ->setSheetIndex(0);
        $spreadsheet = $reader->load('template_fp.csv');
        $no_fp = $jurnalH->no_fp;
        $no_fp = str_replace('.','',$no_fp);
        $no_fp = (int)$no_fp;
        $no_fp = str_pad($no_fp,13,"0",STR_PAD_LEFT);
        $sheet = $spreadsheet->getActiveSheet();
        $baris = 4;
        $sheet->setCellValue('A'.$baris, 'FK');
        //$sheet->setCellValue('B'.$baris, '1');
        $sheet->getCell('B'.$baris)->setValueExplicit('01', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C'.$baris, '0');
        //$sheet->setCellValue('D'.$baris, $no_fp);
        $sheet->getCell('D'.$baris)->setValueExplicit($no_fp, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('E'.$baris, $sj_tgl->format('m'));
        $sheet->setCellValue('F'.$baris, $sj_tgl->format('Y'));
        $sheet->setCellValue('G'.$baris, $sj_tgl->format('d/m/Y'));
        $find = array('.','-');
        $sheet->setCellValue('H'.$baris, str_replace($find,'',$jurnalH->sales->qcustomer->npwp));
        $sheet->setCellValue('I'.$baris, $jurnalH->sales->qcustomer->nama);
        $sheet->setCellValue('J'.$baris, $jurnalH->sales->qcustomer->alamat);

        $sheet->setCellValue('M'.$baris, 0);
        $sheet->setCellValue('N'.$baris, 1);
        $sheet->setCellValue('O'.$baris, 0);
        $sheet->setCellValue('P'.$baris, 0);
        $sheet->setCellValue('Q'.$baris, 0);
        $sheet->setCellValue('R'.$baris, 0);

        $baris++;
        $no = 1;
        $grand_tot = 0;
        foreach($jurnalD as $item){
            if($item->qty_kirim==0)
                continue;
            $netto = $item->harga - ($item->harga * $item->discount/100);
            $total_i = $item->qty_kirim * $netto;
            $grand_tot += $total_i;
            $sheet->setCellValue('A'.$baris,'OF');
            if($item->kode2 != '')
                $sheet->setCellValue('C'.$baris,$item->kode2.' '.$item->nama_barang2.' '.$item->size);
            else
                $sheet->setCellValue('C'.$baris,$item->kode_barang.' '.$item->nama_barang2.' '.$item->size);

            $sheet->setCellValue('D'.$baris,$netto);
            $sheet->setCellValue('E'.$baris,$item->qty_kirim);
            $sheet->setCellValue('F'.$baris,$netto * $item->qty_kirim);
            $sheet->setCellValue('G'.$baris,0);
            $sheet->setCellValue('H'.$baris,$total_i);
            $sheet->setCellValue('I'.$baris,$total_i*$ppn);
            $sheet->setCellValue('J'.$baris,0);
            $sheet->setCellValue('K'.$baris,0);
            $baris++;
        }

        $sheet->setCellValue('K4', $grand_tot);
        $sheet->setCellValue('L4', $grand_tot*$ppn);

        $filename = 'fp_'.$jurnalH->inv_no.'.csv';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Access-Control-Allow-Origin: *');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        ob_end_clean();
        $writerCSV = new CsvWriter($spreadsheet);
        $writerCSV->save('php://output');
    }

    public function hariancsv(Request $request,$tgl){
        $print_tgl = Carbon::createFromFormat('d-m-Y', $tgl);
        $jurnalH = SJ::whereDate('sj_tgl',$print_tgl->format('Y-m-d'))->get();
        //dd($jurnalH);
        //$spreadsheet = IOFactory::load('template_fp.xlsx');

        $reader = new CsvReader();
        $reader->setDelimiter(',')
            ->setEnclosure('"')
            ->setSheetIndex(0);
        $spreadsheet = $reader->load('template_fp.csv');

        $sheet = $spreadsheet->getActiveSheet();
        $baris_header = 4;
        foreach($jurnalH as $inv){
            $no_fp = $inv->no_fp;
            $no_fp = str_replace('.','',$no_fp);
            $no_fp = (int)$no_fp;
            $no_fp = str_pad($no_fp,13,"0",STR_PAD_LEFT);
            $jenis = 0;
            $sj_id = $inv->id;
            $sj_tgl = Carbon::createFromFormat('Y-m-d', $inv->sj_tgl);;
            $ppn = $this->nilaippn($inv->sj_tgl);
            $ref = $inv->inv_no;
            $jurnalD = SJD::select(DB::raw('id,item_id,kode_barang,kode2,nama_barang2,size,harga,qty_kirim,discount,qty_kirim*harga*(1-discount) as subtotal'))->where('sj_id',$sj_id)->orderBy('kode_perk')->get();
            $alamat = wordwrap($inv->sales->qcustomer->alamat, 42, "|", true);
            $arr_alamat = explode("|",$alamat);

            $sheet->setCellValue('A'.$baris_header, 'FK');
            //$sheet->setCellValue('B'.$baris, '1');
            $sheet->getCell('B'.$baris_header)->setValueExplicit('01', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C'.$baris_header, '0');
            //$sheet->setCellValue('D'.$baris, $no_fp);
            $sheet->getCell('D'.$baris_header)->setValueExplicit($no_fp, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E'.$baris_header, $sj_tgl->format('m'));
            $sheet->setCellValue('F'.$baris_header, $sj_tgl->format('Y'));
            $sheet->setCellValue('G'.$baris_header, $sj_tgl->format('d/m/Y'));
            $find = array('.','-');
            $sheet->setCellValue('H'.$baris_header, str_replace($find,'',$inv->sales->qcustomer->npwp));
            $sheet->setCellValue('I'.$baris_header, $inv->sales->qcustomer->nama);
            $sheet->setCellValue('J'.$baris_header, $inv->sales->qcustomer->alamat);

            $sheet->setCellValue('M'.$baris_header, 0);
            $sheet->setCellValue('N'.$baris_header, 1);
            $sheet->setCellValue('O'.$baris_header, 0);
            $sheet->setCellValue('P'.$baris_header, 0);
            $sheet->setCellValue('Q'.$baris_header, 0);
            $sheet->setCellValue('R'.$baris_header, 0);

            $baris_item = $baris_header + 1;
            $no = 1;
            $grand_tot = 0;
            foreach($jurnalD as $item){
                if($item->qty_kirim==0)
                    continue;
                $netto = $item->harga - ($item->harga * $item->discount/100);
                $total_i = $item->qty_kirim * $netto;
                $grand_tot += $total_i;
                $sheet->setCellValue('A'.$baris_item,'OF');
                if($item->kode2 != '')
                    $sheet->setCellValue('C'.$baris_item,$item->kode2.' '.$item->nama_barang2.' '.$item->size);
                else
                    $sheet->setCellValue('C'.$baris_item,$item->kode_barang.' '.$item->nama_barang2.' '.$item->size);

                $sheet->setCellValue('D'.$baris_item,$netto);
                $sheet->setCellValue('E'.$baris_item,$item->qty_kirim);
                $sheet->setCellValue('F'.$baris_item,$netto * $item->qty_kirim);
                $sheet->setCellValue('G'.$baris_item,0);
                $sheet->setCellValue('H'.$baris_item,$total_i);
                $sheet->setCellValue('I'.$baris_item,$total_i*$ppn);
                $sheet->setCellValue('J'.$baris_item,0);
                $sheet->setCellValue('K'.$baris_item,0);
                $baris_item++;
            }

            $sheet->setCellValue('K'.$baris_header, $grand_tot);
            $sheet->setCellValue('L'.$baris_header, $grand_tot*$ppn);
            $baris_header = $baris_item;
        }


        $filename = 'fp_harian'.$print_tgl->format('Y_m_d').'.csv';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header('Access-Control-Allow-Origin: http://'.env('SANCTUM_STATEFUL_DOMAINS'));
        header('Access-Control-Allow-Origin: *');
        //header('Access-Control-Allow-Origin: *');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        ob_end_clean();
        $writerCSV = new CsvWriter($spreadsheet);
        $writerCSV->save('php://output');
    }
}

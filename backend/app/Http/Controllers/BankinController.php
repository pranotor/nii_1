<?php
namespace App\Http\Controllers;
use App\Fp;
use App\Jurnal;
use App\Quotation;
use App\Bankin;
use App\Piutang;
use App\PiutangBayar;
use App\SJ;
use App\SJD;
use App\Item;
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
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Spiritix\Html2Pdf\Converter;
use Spiritix\Html2Pdf\Input\StringInput;
use Spiritix\Html2Pdf\Output\EmbedOutput;
use Spiritix\Html2Pdf\Output\FileOutput;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

class BankinController extends Controller
{
    public function index()
    {
        $sql = "SELECT b.id,b.trans_tgl,b.trans_no,b.trans_jumlah,b.inv_no,b.keterangan,c.nama,c.nick FROM t_bank_in AS b
                INNER JOIN t_sj AS s
                ON s.inv_no = IF(LOCATE(',',b.inv_no),SUBSTRING(b.inv_no, 1, LOCATE(',',b.inv_no) - 1),b.inv_no)
                INNER JOIN t_quotation as q on q.so_no = s.so_no
                INNER JOIN t_customer as c on q.cust_id = c.id";
        $data_resp = DB::select($sql);
        return Response::json($data_resp);
        //return Response::json(Bankin::with('inv.sales.qcustomer')->get());
    }

    public function bankindet(Request $request){
        $data = json_decode($request->getContent(), true);
        if(isset($data['bankin_id']))
            $bankin_id = $data['bankin_id'];
        if(isset($data['inv_no']))
            $inv_no = $data['inv_no'];
        $data_ret = PiutangBayar::select('*');
        if(isset($bankin_id)){
            return Response::json($data_ret->where('bankin_id',$bankin_id)->get());
        }
        if(isset($inv_no)){
            return Response::json($data_ret->where('inv_no',$inv_no)->get());
        }
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $search  = array(',','.');
            $replace = array('','.');
            $po_inp = array();
            $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['trans_tgl']);
            $po_inp['trans_no'] = $data['formdata']['referensi'];
            $po_inp['trans_tgl'] = $date->format('Y-m-d');
            $po_inp['inv_no'] = implode(',',$data['formdata']['inv_no']);
            $jumlah_uang_masuk = str_replace($search,$replace,$data['formdata']['trans_jumlah']);
            $po_inp['trans_jumlah'] = str_replace($search,$replace,$data['formdata']['trans_jumlah']);
            $po_inp['keterangan'] = $data['formdata']['keterangan'];
            $po = Bankin::create($po_inp);
            $bankin_no = $data['formdata']['referensi'];
            $bankin_id = $po->id;
            $data_cust = array();
            if(isset($data['formdata']['invoices'])){
                foreach($data['formdata']['invoices'] as $inv){
                    $bayar = str_replace($search,$replace,$inv['bayar']);
                    $selisih = str_replace($search,$replace,$inv['selisih']);
                    $pay = array();
                    $pay['tgl'] = $date->format('Y-m-d');
                    $pay['bankin_no'] = $bankin_no;
                    $pay['bankin_id'] = $bankin_id;
                    $pay['inv_no'] = $inv['inv_no'];
                    $pay['jumlah'] = $bayar;
                    if($selisih >= 0){
                        if(isset($inv['keterangan']))
                            $pay['keterangan'] = ($selisih > 0 ? number_format($selisih,2)." : " : '').$inv['keterangan'];
                    }
                    else{
                        $pay['keterangan'] = $inv['keterangan'];
                    }
                    $pay['tipe'] = 'income';
                    PiutangBayar::create($pay);
                    if($inv['status']=='true' && $selisih < 0){
                        $pay['tgl'] = $date->format('Y-m-d');
                        $pay['bankin_no'] = $bankin_no;
                        $pay['bankin_id'] = $bankin_id;
                        $pay['inv_no'] = $inv['inv_no'];
                        $pay['jumlah'] = abs($selisih);
                        $pay['keterangan'] = $inv['keterangan'];
                        $pay['tipe'] = 'outcome';
                        PiutangBayar::create($pay);
                    }
                    $data_cust[] = $inv['cust_id'];
                }
            }
            if(sizeof($data_cust) > 0){
                $data_cust = array_unique($data_cust);
                $today = date('Y-m-d');
                foreach($data_cust as $cust){
                    $data = DB::table('v_piutang')->select('cust_id')
                        ->where('cust_id',$cust)
                        ->where('status_piutang','blm lunas')
                        ->whereDate('payment_due','<',$today)->first();
                    if(is_null($data)){
                        DB::table('t_customer')->where('id',$cust)->update(['block'=>0]);
                    }
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
        $data = Bankin::where('trans_no',$id)->with('bayar')->first();
        return Response::json($data);
        /*
        SELECT pb.`bankin_id`, pb.bankin_no,pb.`tgl`,pb.`jumlah`,
(p.`subtotal` + p.`ppn` + p.`biaya_kirim` + p.`biaya_lain`) AS jumlah_tagihan,
(SELECT IFNULL(0,SUM(pb2.jumlah)) FROM t_piutang_bayar AS pb2 WHERE pb.`inv_no` = pb2.inv_no AND pb2.bankin_id <> pb.`bankin_id` AND pb2.tgl < pb.tgl) AS bayar_prev
FROM t_piutang_bayar AS pb
INNER JOIN t_piutang AS p ON pb.`inv_no` = p.`inv_no`
WHERE pb.bankin_id=48 AND pb.`tipe`='income';
        */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bankin $bankin)
    {
        $bankin_id = $bankin->id;
        $bankin->delete();
        DB::table('t_piutang_bayar')->where('bankin_id',$bankin_id)->delete();
    }

    private function nilaippn($tgl)
    {
        $ppn = DB::table('t_ppn')->whereDate('tgl_mulai','<=',$tgl)->orderBy('id','desc')->first();
        return $ppn->ppn_value;
    }

    public function pdf($id,$generate=0){
        $jurnalH = SJ::where('inv_no',$id)->first();
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
            $html =  view('jurnal.inv',  compact('jurnalH','ref','jurnalD','jenis','img_file','img_file_copy','ppn'));
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
            'printBackground' => true,
            'displayHeaderFooter' => false,
            'headerTemplate' => '<style> h1 { font-size:20px; margin-top:50px;}</style> <h1>Header</h1>',
            'footerTemplate' => '<div style="font-size:10px !important;">I am a footer</div>',
            'margin' => ['top' => '300px', 'bottom' => '40px', 'left' => '40px', 'right' => '40px']
        ]);

        $output = $converter->convert();
        $output->embed('google.pdf');

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
                    array_push($msg,str_pad($item->itembarang->perkiraan->nama_perk,30,"_",STR_PAD_RIGHT));
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
                    array_push($msg,str_pad($item->itembarang->perkiraan->nama_perk,50,"_",STR_PAD_RIGHT));
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

            $sheet->setCellValue('D'.$baris,$item->harga);
            $sheet->setCellValue('E'.$baris,$item->qty_kirim);
            $sheet->setCellValue('F'.$baris,$item->harga * $item->qty_kirim);
            $sheet->setCellValue('G'.$baris,$item->discount);
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

                $sheet->setCellValue('D'.$baris_item,$item->harga);
                $sheet->setCellValue('E'.$baris_item,$item->qty_kirim);
                $sheet->setCellValue('F'.$baris_item,$item->harga * $item->qty_kirim);
                $sheet->setCellValue('G'.$baris_item,$item->discount);
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

        $writerCSV = new CsvWriter($spreadsheet);
        $writerCSV->save('php://output');
    }
}

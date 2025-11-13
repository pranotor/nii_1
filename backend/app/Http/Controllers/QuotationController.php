<?php

namespace App\Http\Controllers;

use App\Jurnal;
use App\Quotation;
use App\Quod;
use App\QuodH;
use App\Item;
use App\Invent;
use App\Tandatangan;
use App\Perkiraan;
use App\Param;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreQuotation;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Arr;
use App\Traits\Jurnaldata;
use App\Traits\Refno;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class QuotationController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Bpb::get());
    }

    private function nilaippn($tgl)
    {
        $ppn = DB::table('t_ppn')->whereDate('tgl_mulai','<=',$tgl)->orderBy('id','desc')->first();
        return $ppn->ppn_value;
    }

    public function quotationlist(Request $request){
        $data = json_decode($request->getContent(), true);
        //$bpb = Quotation::whereYear('qt_tgl',$data['THN']);
        $bpb = Quotation::where('is_sample',0);
        return Response::json($bpb->with('qcustomer','status')->orderBy('qt_no','DESC')->get());
    }

    public function quotationhistory(Request $request){
        $data = json_decode($request->getContent(), true);
        if(isset($data['qt_no']))
            $bpb = QuodH::distinct()->where('qt_no',$data['qt_no'])->orderBy('rev','DESC')->get(['rev','created_at','qt_no']);
        else
            $bpb = QuodH::distinct()->get(['rev','created_at']);
        return Response::json($bpb);
    }

    public function quotationdh(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = QuodH::where('qt_no',$data['qt_no'])->where('rev',$data['rev'])
                    ->with('itembarang')
                    ->orderBy('kode_perk')->get();
        return Response::json($bpb);
    }

    public function quotationdet(Request $request){
        $data = json_decode($request->getContent(), true);
		if(isset($data['qt_id'])){
			$bpb = Quod::where('qt_id',$data['qt_id'])
                    ->with('quo_head')
                    ->with('itembarang')
                    ->with('itembarang.itemperkiraan')
                    ->with('itembarang.itemperkiraan.parentperkiraan')
                    ->with('workorder')
                    ->orderBy('kode_perk')->get()->map(function($item){
                        $item['ppn'] = DB::table('t_ppn')->whereDate('tgl_mulai','<=',$item->quo_head->qt_tgl)->orderBy('id','desc')->first()->ppn_value;
                        return $item;
                        })->all();
		}
		elseif(isset($data['wo_id'])){
			$bpb = Quod::where('wo_id',$data['wo_id'])
                    ->with('quo_head')
                    ->with(['itembarang' => function($query){
                        $query->select('*');
                    }])
                    ->orderBy('kode_perk')->get()->map(function($woitem){
                        $woitem['kategori'] = Perkiraan::select('nama_perk')->where('kode_perk',substr($woitem->itembarang->kode_perk,0,5))->first()->nama_perk."-".$woitem->kode_barang."-".$woitem->size;
                        $woitem['ppn'] = DB::table('t_ppn')->whereDate('tgl_mulai','<=',$woitem->quo_head->qt_tgl)->orderBy('id','desc')->first()->ppn_value;
                        return $woitem;
                        })->all();
		}
        return Response::json($bpb);
    }

    public function solist(Request $request){
        $data = DB::table('v_quotation_sj')->where('is_sample',0)->get();
        return Response::json($data);
    }

    public function samplelist(Request $request){
        $data = DB::table('v_quotation_sj')->where('is_sample',1)->get();
        return Response::json($data);
    }

    public function cekpocust(Request $request){
        $cust_id = $request->cust_id;
        $so_tgl =  Carbon::createFromFormat('d-m-Y',$request->tanggal);
        $po_no = trim($request->po_no);
        $q_po = Quotation::where('po_cust',$po_no)
                ->where('cust_id',$cust_id)
                ->where('so_tgl',$so_tgl->format('Y-m-d'))
                ->first();
        if (!$q_po) {
            $data= array('status_po' => 'ok');
        }
        else
            $data= array('status_po' => 'error');
        return json_encode($data);
    }

    public function getSO($id){
        $jurnalH = Quotation::where('so_no',$id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpb_id = $jurnalH->id;
        $today = date('d-m-Y');
        $ref = $jurnalH['qt_no'];
        $jurnalD = Quod::select(DB::raw("id,id as qod_id,qt_id,item_id,kode2,nama_barang2,size,qty_pesan"))
                    ->with('itembarang')->where('qt_id',$bpb_id)->where('wo_id',0)->orderBy('kode_perk')->get();
        return Response::json($jurnalD);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuotation $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $search  = array('.',',');
            $replace = array('','.');
            $po_inp = array();
            $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
            $po_inp['qt_tgl'] = $date->format('Y-m-d');
            if($data['formdata']['jenis']=='so'){
                $po_inp['so_no'] = $data['formdata']['referensi'];
                $po_inp['so_tgl'] = $date->format('Y-m-d');
                $po_inp['po_cust'] = $data['formdata']['po_no'];
                $po_inp['qt_no'] = $data['formdata']['referensi'];
                $po_inp['posting'] = 1;
            }
            elseif($data['formdata']['jenis']=='sr'){
                $po_inp['so_no'] = $data['formdata']['referensi'];
                $po_inp['so_tgl'] = $date->format('Y-m-d');
                $po_inp['po_cust'] = $data['formdata']['po_no'];
                $po_inp['qt_no'] = $data['formdata']['referensi'];
                $po_inp['is_sample'] = 1;
                $po_inp['posting'] = 1;
            }

            else{
                $po_inp['so_no'] = $data['formdata']['referensi'];
                $po_inp['qt_no'] = $data['formdata']['referensi'];
                $po_inp['po_cust'] = $data['formdata']['po_no'];
            }
            $po_inp['p'] = $data['formdata']['p'];
            $po_inp['cust_id'] = $data['formdata']['cust_id'];
            $po_inp['leadtime'] = $data['formdata']['leadtime'];
            $po_inp['validity'] = $data['formdata']['validity'];
            $po_inp['payment'] = $data['formdata']['payment']." Days after received the goods";
            $po_inp['notes'] = $data['formdata']['notes'];
            $po_inp['opr'] = $data['formdata']['opr'];
            $po_inp['sales_id'] = $data['formdata']['sales_id'];
            if($data['formdata']['isEdit']) {
                $bpb = Quotation::where('qt_no',$data['formdata']['prevReferensi'])->first();
                $bpb_id = $bpb->id;
                $bpb->update($po_inp);
                $data['qt_id'] = $bpb_id;
                //$bpb->delete();

                $q_rev = QuodH::selectRaw('max(rev) as rev')->where('qt_no',$data['formdata']['prevReferensi'])->first();
                $rev = $q_rev->rev;
                $rev++;
                foreach($data['formdata']['datatrans'] as $jurnal){
                    if(isset($jurnal['quotationd_id']))
                        $detail_id = $jurnal['quotationd_id'];
                    else
                        $detail_id = 0;
                    $arr_barang_perk = explode("_",$jurnal['kode_barang']);
                    $bpbd_inp = array();
                    $bpbd_inp['qt_id'] = $data['qt_id'];
                    $bpbd_inp['item_id'] = $jurnal['item_id'];
                    $bpbd_inp['kode_perk'] = $arr_barang_perk[0];
                    $bpbd_inp['kode_barang'] = $arr_barang_perk[1];
                    $bpbd_inp['kode2'] = $jurnal['kode2'];
                    $bpbd_inp['nama_barang2'] = $jurnal['nama2'];
                    $bpbd_inp['size'] = $jurnal['size'];
                    $bpbd_inp['satuan'] = $jurnal['satuan'];
                    $bpbd_inp['qty_pesan'] = str_replace($search,$replace,$jurnal['qty_pesan']);
                    $bpbd_inp['harga'] = str_replace($search,$replace,$jurnal['harga']);
                    $bpbd_inp['discount'] = str_replace($search,$replace,$jurnal['discount']);

                    if($detail_id != 0){
                        $quod = Quod::where('id',$detail_id)->first();
                        $quod->update($bpbd_inp);
                    }
                    else{
                        Quod::create($bpbd_inp);
                    }

                    unset($bpbd_inp['qt_id']);
                    $bpbd_inp['rev'] = $rev;
                    $bpbd_inp['qt_no'] = $data['formdata']['referensi'];
                    QuodH::create($bpbd_inp);
                }
                foreach($data['formdata']['datahapus'] as $jurnal){
                    if(isset($jurnal['quotationd_id'])){
						$detail_id = $jurnal['quotationd_id'];
						Quod::where('id',$detail_id)->delete();
					}
                }
            }
            else{
                $rev = 0;
                $po = Quotation::create($po_inp);
                $data['qt_id'] = $po->id;
                foreach($data['formdata']['datatrans'] as $jurnal){
                    $arr_barang_perk = explode("_",$jurnal['kode_barang']);
                    $bpbd_inp = array();
                    $bpbd_inp['qt_id'] = $data['qt_id'];
                    $bpbd_inp['item_id'] = $jurnal['item_id'];
                    $bpbd_inp['kode_perk'] = $arr_barang_perk[0];
                    $bpbd_inp['kode_barang'] = $arr_barang_perk[1];
                    $bpbd_inp['kode2'] = $jurnal['kode2'];
                    $bpbd_inp['nama_barang2'] = $jurnal['nama2'];
                    $bpbd_inp['size'] = $jurnal['size'];
                    $bpbd_inp['satuan'] = $jurnal['satuan'];
                    $bpbd_inp['qty_pesan'] = str_replace($search,$replace,$jurnal['qty_pesan']);
                    $bpbd_inp['harga'] = str_replace($search,$replace,$jurnal['harga']);
                    $bpbd_inp['discount'] = str_replace($search,$replace,$jurnal['discount']);

                    Quod::create($bpbd_inp);
                    unset($bpbd_inp['qt_id']);
                    $bpbd_inp['rev'] = $rev;
                    $bpbd_inp['qt_no'] = $data['formdata']['referensi'];
                    QuodH::create($bpbd_inp);
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
    public function show($id,$tgl='')
    {
        $jenis = substr($id,0,2);
        $closing = DB::table('t_closing')->select('closing_year')->first();
        if($tgl==''){
            $tanggal = date('Y-m-d');
            $closing_year = $closing->closing_year;
        }
        else{
            $date = Carbon::createFromFormat('d-m-Y',$tgl);
            $tanggal = $date->format('Y-m-d');
            $closing_year = $date->format('Y').'-01-01';
        }
        if($jenis == 'SO' || $jenis=='PH' || $jenis=='SR'){
            $collection = Quotation::where('so_no',$id)->with(['quod' => function($query){
                $query->select(DB::raw("id,id as quotationd_id,qt_id,item_id,format(qty_pesan,2,'de_DE') as qty_pesan,format(qty_pesan-tot_kirim-tot_retur,2,'de_DE') as qty_pesan2,format(harga,2,'de_DE') as harga,format(discount,2,'de_DE') as discount,size,nama_barang2 as nama2,kode2"));
            }
            ,'quod.itembarang' => function($query){
                    $query->select(DB::raw('id,kode_perk,satuan,CONCAT(kode_perk,"_",kode_barang) as kode_barang, uraian as nama_barang, stock_controll'));
            },'quod.itembarang.stock'=> function($query) use ($tanggal, $closing_year){
                $query->select(DB::raw('item_id,qty,status'))
                ->whereDate('tgl','<=',$tanggal)
                ->whereDate('tgl','>=',$closing_year);
                }
            ])->get()->toArray();
        }
        else{
            $collection = Quotation::where('qt_no',$id)->with(['quod' => function($query){
                $query->select(DB::raw("id,id as quotationd_id,qt_id,item_id,format(qty_pesan,2,'de_DE') as qty_pesan,format(harga,2,'de_DE') as harga,format(discount,2,'de_DE') as discount,size,nama_barang2 as nama2,kode2"));
            }
            ,'quod.itembarang' => function($query){
                    $query->select(DB::raw('id,kode_perk,satuan,CONCAT(kode_perk,"_",kode_barang) as kode_barang, uraian as nama_barang'));
            }])->get()->toArray();
        }

        //$query_dump = DB::getQueryLog();
        //print_r($query_dump);
        //echo "<pre>";
        //print_r($collection);
        //echo "</pre>";
        //print_r($collects);
        //return Response::json($collect);
        $arrbppd = array();
        $i=0;
        foreach($collection as $field){
            foreach($field as $key=>$value){
                //print_r($key);
                if($key == 'quod'){
                    $j=0;
                    foreach($value as $f){
                        //print_r($f);
                        foreach($f as $k1=>$v1){
                            if($k1 != 'itembarang')
                                $arrbppd[$i]['datatrans'][$j][$k1] = $v1;
                            else{
                                foreach($v1 as $k2=>$v2){
                                    $tot_stock = 0;
                                    if($k2 != 'stock')
                                        $arrbppd[$i]['datatrans'][$j][$k2] = $v2;
                                    else{
                                        $tot_in = 0;
                                        $tot_out = 0;
                                        foreach($v2 as $destock){
                                            if($destock['status']=='IN')
                                                $tot_in = $tot_in + $destock['qty'];
                                            else
                                                $tot_out = $tot_out + $destock['qty'];
                                        }
                                        $sisa = $tot_in - $tot_out;
                                        $arrbppd[$i]['datatrans'][$j][$k2] = $sisa;
                                    }
                                }
                            }
                        }
                        $j++;
                    }

                }
                else{
                    $arrbppd[$i][$key] = $value;
                }
            }
            $i++;
        }

        /* echo "<pre>";
        print_r($arrbppd);
        echo "</pre>"; */
        return Response::json($arrbppd);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dvud $dvud)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            Quotation::where('id',$id)->delete();
            Quod::where('qt_id',$id)->delete();
        }, 5);
    }

    public function unposting(Request $request)
    {
        DB::transaction(function () use ($request) {
            $bpb_no = $request->referensi;
            $data_jurnal = Jurnal::where('document',$bpb_no)->where('jenis',1)->first();
            //dd($data_jurnal);
            if(!is_null($data_jurnal)){
                $data['referensi'] = $data_jurnal->referensi;
                $this->deletejurnal($data);
            }
            Bpb::where('bpb_no',$bpb_no)->update(['posting'=>0]);
        }, 5);
    }

    public function convert(Request $request)
    {
        DB::transaction(function () use ($request) {
            $qt_no = $request->referensi;
            $po_no = $request->nomor_po;
            $data_jurnal = Quotation::where('qt_no',$qt_no)->first();
            //dd($data_jurnal);
            if(!is_null($data_jurnal)){
                $date = Carbon::now();
                $sql = "SELECT MAX(CAST(SUBSTR(so_no,5,5) AS DECIMAL)) as number FROM t_quotation WHERE YEAR(qt_tgl)=".$date->format('Y')."  AND MONTH(qt_tgl)=".$date->format('n');

                $p_n = $data_jurnal->p;
                if($p_n != 1)
                    $sql .= " AND P='N'";
                else
                    $sql .= " AND P='P'";
                echo $sql;
                $referensi = DB::select($sql);
				//dd($referensi);
				$number = $referensi[0]->number + 1;

                if($p_n != 1)
                    $str_ref = "SO-1".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                else
                    $str_ref = "SO-".str_pad($number, 5, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');

				//$str_ref = "SO-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');

                Quotation::where('qt_no',$qt_no)->update(['posting'=>1,'so_no'=>$str_ref,'po_cust'=>$po_no,'so_tgl'=>$date->format('Y-m-d')]);
            }

        }, 5);
    }

    public function polist(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = Po::whereYear('po_tgl',$data['THN']);

        /* if($data['bpbmode']=='posting')
            return Response::json($bpb->where('posting',0)->with('supplier','status')->get());

        else */
            return Response::json($bpb->with('supplier','status')->get());


        /*  DB::enableQueryLog();
        Bpb::with('supplier','status')->get();
        $query_dump = DB::getQueryLog();

        print_r($query_dump); */

    }

    public function pdf($id,$generate=0){
        $jurnalH = Quotation::where('qt_no',$id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpb_id = $jurnalH->id;
        $today = date('d-m-Y');
        $ref = $jurnalH['qt_no'];
        $jurnalD = Quod::where('qt_id',$bpb_id)->orderBy('kode_perk')->get();
        $ppn = $this->nilaippn($jurnalH->qt_tgl);
        //dd($ppn);
        if($this->nama_lembaga != '' && $this->logo_lembaga != ''){
            // Custom Header
                PDF::setHeaderCallback(function($pdf) {
                    $img_file = K_PATH_IMAGES."/".$this->logo_lembaga;
                    //$pdf->Cell(0, 0, 'TEST lOKASI '.$img_file, 1, 1, 'C', 0, '', 0);
                    if ($pdf->getPage() == 1 || $pdf->getPage() == 2){
                        $pdf->setY(5);
                        $pdf->SetFont('cid0jp', '', 14);
                        /* $html = '<table cellspacing="0" cellpadding="0" border="0"><tr><td width="10%" style="border-bottom: 2px solid red;"><img src="'.$img_file.'"/></td>
                             <td width="90%" style="border-bottom: 2px solid red;">&nbsp;&nbsp;&nbsp;<br/><br/><span style="font-size: 30px; font-weight:bold">'.$this->nama_lembaga.'</span></td></tr>
                             <tr><td colspan="2"><span style="font-size: 10pt;" >Komp. Pergudangan Biz Park Commercial Estate Blok A1-36</span></td></tr>
                             <tr><td colspan="2"><span style="font-size: 10pt;" >Jl. Raya Kopo No. 455</span></td></tr>
                             <tr><td colspan="2"><span style="font-size: 10pt;" >Bandung 40236 - Indonesia</span></td></tr>
                             <tr><td colspan="2"><span style="font-size: 10pt;" >Tel: +62-22-8778 4636 ; 8777 5656</span></td></tr>
                             <tr><td colspan="2"><span style="font-size: 10pt;" >Fax: +62-22-8778 4536</span></td></tr>
                             <tr><td colspan="2"><span style="font-size: 10pt;" >Email : info@nii-ltd.com  Website : www.nii-ltd.com</span></td></tr>
                             </table>'; */
                        $html = '<table cellspacing="0" cellpadding="1" border="0"><tr><td width="10%" style="border-bottom: 2px solid red;"><img src="'.$img_file.'"/></td>
                             <td width="90%" style="border-bottom: 2px solid red;">&nbsp;&nbsp;&nbsp;&nbsp;<br/><span style="font-size: 20pt; font-weight:bold">'.$this->nama_lembaga.'</span></td></tr>
                             <tr><td colspan="2"><span style="font-size: 10pt;" >Komp. Pergudangan Biz Park Commercial Estate Blok A1-36
                             <br/>Jl. Raya Kopo No. 455
                             <br/>Bandung 40236 - Indonesia
                             <br/>Tel: +62-22-8778 4636 ; 8777 5656
                             <br/>Fax: +62-22-8778 4536
                             <br/>Email : info@nii-ltd.com  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website : www.nii-ltd.com</span></td></tr>
                             </table>';
                        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
                    }
                });
        }
        PDF::SetFont('cid0jp', '', 8,'', 'false');
        PDF::SetTitle('BUKTI JURNAL)');
        PDF::SetMargins(5,60, 5,true);
        PDF::SetAutoPageBreak(TRUE, 0);
        $auto_page_break = PDF::getAutoPageBreak();
        $modname = get_class($this);

        $html =  view('jurnal.qt',  compact('jurnalH','ref','jurnalD','modname','jenis','ppn'));
        Storage::disk('local')->put('table_quot.html', $html);
        PDF::AddPage('P','F4');
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('bpb_'.$ref.'.pdf');


    }
}

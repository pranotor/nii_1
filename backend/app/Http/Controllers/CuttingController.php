<?php

namespace App\Http\Controllers;

use App\Jurnal;
use App\Quotation;
use App\Quod;
use App\QuodH;
use App\Item;
use App\Invent;
use App\Cutting;
use App\CuttingD;
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


class CuttingController extends Controller
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
        return Response::json(Cutting::get());
    }

    private function nilaippn($tgl)
    {
        $ppn = DB::table('t_ppn')->whereDate('tgl_mulai','<=',$tgl)->orderBy('id','desc')->first();
        return $ppn->ppn_value;
    }

    public function quotationlist(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = Quotation::whereYear('qt_tgl',$data['THN']);

        /* if($data['bpbmode']=='posting')
            return Response::json($bpb->where('posting',0)->with('supplier','status')->get());

        else */
            return Response::json($bpb->with('qcustomer','status')->orderBy('qt_no','DESC')->get());


        /*  DB::enableQueryLog();
        Bpb::with('supplier','status')->get();
        $query_dump = DB::getQueryLog();

        print_r($query_dump); */

    }

    public function cuttingdet(Request $request){
        $data = json_decode($request->getContent(), true);
		if(isset($data['ct_id'])){
			$bpb = CuttingD::where('ct_id',$data['ct_id'])
                    ->with('cut_head')
                    ->with('itembarang')
                    ->orderBy('kode_perk')->get();
		}
        return Response::json($bpb);
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
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);

            DB::transaction(function () use ($data) {
                if($data['formdata']['isEdit']) {
                    //delete
                    $bpb = Cutting::where('ct_no',$data['formdata']['prevReferensi'])->first();
                    $bpb_id = $bpb->id;
                    $bpb->delete();
                    CuttingD::where('ct_id',$bpb_id)->delete();
                    $q_inven = DB::table('t_invent')->where('status','OUT')->where('ref',$data['formdata']['prevReferensi'])->get();
                    foreach($q_inven as $inv){
                        $ref_item_used = intval($inv->used);
                        $qty_used = $inv->qty;
                        $q_item_used = Invent::where('id',$ref_item_used)->first();
                        //$qty_refitem = $q_item_used->qty + $qty_used;
                        $qty_refitem_used = $q_item_used->used - $qty_used;
                        $q_item_used->update(['used'=>$qty_refitem_used]);
                    }
                    $q_inven = DB::table('t_invent')->where('ref',$data['formdata']['prevReferensi'])->delete();
                }
                else
                    $rev = 0;
                /* $search  = array('.',',');
                $replace = array('','.'); */
                $search  = array(',');
                $replace = array('');

                $ct_inp = array();
                $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
                $ct_inp['ct_tgl'] = $date->format('Y-m-d');
                $ct_inp['ct_no'] = $data['formdata']['referensi'];

                $ct_inp['opr'] = $data['formdata']['opr'];
                $ct = Cutting::create($ct_inp);
                $data['ct_id'] = $ct->id;

                //barang keluar--
                $tot_harga = 0;
                foreach($data['formdata']['datatransbaku'] as $bhn){
                    $qty_keluar = str_replace($search,$replace,$bhn['qty']);
                    $arr_barang_perk = explode("_",$bhn['kode_barang']);
                    $bpbd_inp = array();
                    $bpbd_inp['ct_id'] = $data['ct_id'];
                    $bpbd_inp['item_id'] = $bhn['item_id'];
                    $bpbd_inp['kode_perk'] = $arr_barang_perk[0];
                    $bpbd_inp['kode_barang'] = $arr_barang_perk[1];
                    $bpbd_inp['qty'] = -1*$qty_keluar;
                    $bpbd_inp['harga'] = '';

                    $cutting = CuttingD::create($bpbd_inp);

                    $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                            WHERE qty <> used and status='IN' and item_id=:item_id order by tgl ASC",['item_id'=>$bhn['item_id']]);

                    $repeat_loop = TRUE;
                    $sisa_pakai = $qty_keluar;
                    $str_harga = "";
                    $j=0;
                    foreach($stock as $stk){
                        if($j>0)
                            $str_harga .= "|";
                        $sisa_stock = $stk->sisa;
                        $used = $stk->used;
                        if($sisa_stock < $sisa_pakai){
                            $used = $stk->qty;
                            $str_harga .= $stk->sisa."*@".$stk->harga;
                            $sisa_pakai = $sisa_pakai - $stk->sisa;
                            $qty_pakai = $stk->sisa;
                        }
                        else{
                            $used += $sisa_pakai;
                            $str_harga .= $sisa_pakai."*@".$stk->harga;
                            $qty_pakai = $sisa_pakai;
                            $repeat_loop = FALSE;
                        }

                        //--insert kartu stock
                        $stock = new Invent();
                        $stock->tgl = $date->format('Y-m-d');
                        $stock->kode_barang = $bhn['kode_barang'];
                        $stock->status = 'OUT';
                        $stock->qty = $qty_pakai;
                        $stock->harga = $stk->harga;
                        $stock->ref = $data['formdata']['referensi'];
                        $stock->ref_harga = $stk->ref;
                        $stock->item_id = $bhn['item_id'];
                        $stock->used = $stk->id;
                        $stock->save();
                        $tot_harga = $tot_harga + ($stk->harga * $qty_pakai);

                        //--update used in stock--
                        $inv = Invent::find($stk->id);
                        $inv_upd['used'] = $used;
                        $inv->update($inv_upd);

                        if(!$repeat_loop){
                            break;
                        }
                        $j++;
                    }
                    //--update harga di permintaan detail--
                    $permintaan = CuttingD::where('id',$cutting->id)->update(['harga'=>$str_harga]);
                }

                //barang masuk
                if(sizeof($data['formdata']['datatransprod']) > 1)
                    $hasil_banyak = true;
                else
                    $hasil_banyak = false;
                //$tot_hasil_prod = 0;
                $tot_hasil_prod = array_sum(array_map(function($element) use($search,$replace) {return str_replace($search,$replace,$element['qty'])*$element['fp'];}, $data['formdata']['datatransprod']));
                foreach($data['formdata']['datatransprod'] as $jurnal){
                    $qty_masuk = str_replace($search,$replace,$jurnal['qty']);
                    $arr_barang_perk = explode("_",$jurnal['kode_barang']);
                    if($hasil_banyak){
                        $harga = $tot_harga*$jurnal['fp']/$tot_hasil_prod;
                    }
                    else{
                        $harga = $tot_harga / $qty_masuk;
                    }
                    $bpbd_inp = array();
                    $bpbd_inp['ct_id'] = $data['ct_id'];
                    $bpbd_inp['item_id'] = $jurnal['item_id'];
                    $bpbd_inp['kode_perk'] = $arr_barang_perk[0];
                    $bpbd_inp['kode_barang'] = $arr_barang_perk[1];
                    $bpbd_inp['fp'] = $jurnal['fp'];
                    $bpbd_inp['qty'] = $qty_masuk;
                    $bpbd_inp['harga'] = $harga;

                    CuttingD::create($bpbd_inp);

                    //--insert kartu stock
                    $item = Item::where('kode_perk',$arr_barang_perk[0])->where('kode_barang',$arr_barang_perk[1])->first();
                    $stock = new Invent();
                    $stock->tgl = $date->format('Y-m-d');
                    $stock->kode_barang = $jurnal['kode_barang'];
                    $stock->item_id = $jurnal['item_id'];
                    $stock->status = 'IN';
                    $stock->qty = $qty_masuk;
                    $stock->harga = $harga;
                    $stock->ref = $data['formdata']['referensi'];
                    $stock->ref_harga = "harga:".$tot_harga."fp:".$jurnal['fp']."tot_prod:".$tot_hasil_prod;
                    $stock->save();
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
        $collection = Cutting::where('ct_no',$id)->with('cuttd')->get();
        return Response::json($collection);
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
    public function destroy(Cutting $cutting)
    {
        DB::transaction(function () use ($cutting) {
            $q_inven = DB::table('t_invent')->where('status','OUT')->where('ref',$cutting->ct_no)->get();
            foreach($q_inven as $inv){
                $ref_item_used = intval($inv->used);
                $qty_used = $inv->qty;
                $q_item_used = Invent::where('id',$ref_item_used)->first();
                //$qty_refitem = $q_item_used->qty + $qty_used;
                $qty_refitem_used = $q_item_used->used - $qty_used;
                $q_item_used->update(['used'=>$qty_refitem_used]);
            }
            $q_inven = DB::table('t_invent')->where('ref',$cutting->ct_no)->delete();
            $cutting->delete();
            CuttingD::where('ct_id',$cutting->id)->delete();
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
                $referensi = DB::select('SELECT MAX(CAST(SUBSTR(so_no,5,4) AS DECIMAL)) as number FROM t_quotation WHERE YEAR(qt_tgl)='.$date->format('Y').'  AND MONTH(qt_tgl)='.$date->format('n'));
				//dd($referensi);
				$number = $referensi[0]->number + 1;
				$str_ref = "SO-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');

                Quotation::where('qt_no',$qt_no)->update(['posting'=>1,'so_no'=>$str_ref,'po_cust'=>$po_no]);
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
         PDF::AddPage('P','A4');
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('bpb_'.$ref.'.pdf');


    }
}

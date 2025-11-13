<?php

namespace App\Http\Controllers;

use App\Jurnal;
use App\Produksi;
use App\Wo;
use App\Wod;
use App\Quod;
use App\Item;
use App\Invent;
use App\Tandatangan;
use App\Perkiraan;
use App\Param;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreBpb;
use App\Http\Requests\StoreProduksi;
use App\Pod;
use App\ProduksiD;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Arr;
use App\Traits\Jurnaldata;
use App\Traits\Refno;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class ProduksiController extends Controller
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
        //$bpb = Produksi::whereYear('prod_tgl',$data['THN']);
        return Response::json(Produksi::with('work','work.quotation','work.quotation.qcustomer')->get());
        //return Response::json(Produksi::orderBy('prod_tgl','Desc')->get());
    }

    public function produksidet(Request $request){
        $data = json_decode($request->getContent(), true);
		if(isset($data['prod_id'])){
			$bpb = ProduksiD::where('prod_id',$data['prod_id'])
                    ->with('prod_head')
                    ->with('detailquot')
                    ->with('itembarang')
                    ->orderBy('kode_perk')->get();
		}
        return Response::json($bpb);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProduksi $request)
    {
        $data = json_decode($request->getContent(), true);

        DB::transaction(function () use ($data) {
            $search  = array(',','.');
            $replace = array('','.');
            $wo_id = $data['formdata']['wo_id'];
            $q_wo = Wo::where('id',$wo_id)->first();
            if($data['formdata']['isEdit']) {
                $tot_harga = 0;
                //delete
                $produksi = Produksi::where('prod_no',$data['formdata']['prevReferensi'])->first();
                $prod_id = $produksi->id;
                $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
                $produksi->prod_tgl = $date->format('Y-m-d');
                $produksi->prod_no = $data['formdata']['referensi'];
                $produksi->wo_id = $data['formdata']['wo_id'];
                $produksi->save();

                //barang keluar
                $q_inven = DB::table('t_invent')->where('status','OUT')->where('ref',$data['formdata']['prevReferensi'])->get();
                foreach($q_inven as $inv){
                    $ref_item_used = intval($inv->used);
                    $qty_used = $inv->qty;
                    $q_item_used = Invent::where('id',$ref_item_used)->first();
                    //$qty_refitem = $q_item_used->qty + $qty_used;
                    if(!is_null($q_item_used)){
                        $qty_refitem_used = $q_item_used->used - $qty_used;
                        $q_item_used->update(['used'=>$qty_refitem_used]);
                    }
                }
                $q_inven = DB::table('t_invent')->where('status','OUT')->where('ref',$data['formdata']['prevReferensi'])->delete();
                ProduksiD::where('qty','<',0)->where('prod_id',$prod_id)->delete();
                foreach($data['formdata']['datatransprod'] as $jurnal){
                    $qty_keluar = str_replace($search,$replace,$jurnal['qty']);
                    $arr_barang_perk = explode("_",$jurnal['kode_barang']);
                    $bpbd_inp = array();
                    $bpbd_inp['prod_id'] = $prod_id;
                    $bpbd_inp['item_id'] = $jurnal['item_id'];
                    $bpbd_inp['kode_perk'] = $arr_barang_perk[0];
                    $bpbd_inp['kode_barang'] = $arr_barang_perk[1];
                    $bpbd_inp['qty'] = -1*$qty_keluar;
                    $bpbd_inp['harga'] = '';

                    $prod = ProduksiD::create($bpbd_inp);

                    $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and status='IN' and kode_barang=:kode order by tgl ASC",['kode'=>$jurnal['kode_barang']]);

                    $repeat_loop = TRUE;
                    $sisa_pakai = $qty_keluar;
                    $str_harga = "";
                    $j=0;
                    $tot_harga = 0;
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
                        $stock->kode_barang = $jurnal['kode_barang'];
                        $stock->status = 'OUT';
                        $stock->qty = $qty_pakai;
                        $stock->harga = $stk->harga;
                        $stock->ref = $data['formdata']['referensi'];
                        $stock->ref_harga = $stk->ref;
                        $stock->item_id = $jurnal['item_id'];
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
                    $permintaan = ProduksiD::where('id',$prod->id)->update(['harga'=>$str_harga]);
                }

                //barang hasil
                $qd_id = $data['formdata']['barang_hasil'];
                $prev_qd_id = $data['formdata']['prev_barang_hasil'];
                $qty_masuk = str_replace($search,$replace,$data['formdata']['qty_hasil']);
                $harga = $tot_harga / $qty_masuk;
                if($qd_id != $prev_qd_id){
                    $q_qd = Quod::where('id',$qd_id)->first();
                    $q_qd_prev = Quod::where('id',$prev_qd_id)->first();
                     //update prod_id di quotationd
                    $q_qd->update(['prod_id'=>$prod_id]);
                    $q_qd_prev->update(['prod_id'=>0]);
                    $item_id = $q_qd->item_id;

                    //get item_id
                    $item = Item::where('id',$item_id)->first();

                    $bpbd_inp['prod_id'] = $prod_id;
                    $bpbd_inp['qd_id'] = $qd_id;
                    $bpbd_inp['item_id'] = $item->id;
                    $bpbd_inp['kode_perk'] = $item->kode_perk;
                    $bpbd_inp['kode_barang'] = $item->kode_barang;
                    $bpbd_inp['qty'] = $qty_masuk;
                    ProduksiD::create($bpbd_inp);
                    //hapus produksi item sebelum
                    ProduksiD::where('prod_id',$prod_id)->where('qd_id',$prev_qd_id)->delete();
                    Invent::where('ref',$data['formdata']['prevReferensi'])->where('status','in')->delete();

                    $stock = new Invent();
                    $stock->tgl = $date->format('Y-m-d');
                    $stock->kode_barang = $item->kode_perk."_".$item->kode_barang;
                    $stock->item_id = $item->id;
                    $stock->status = 'IN';
                    $stock->qty = $qty_masuk;
                    $stock->harga = $harga;
                    $stock->ref = $data['formdata']['referensi'];
                    $stock->keterangan = $q_wo->quotation->so_no."|".$q_wo->quotation->qcustomer->nick;
                    $stock->cust_id = $q_wo->quotation->cust_id;
                    $stock->save();
                }
                else{ //tidak beruah qd_id
                    ProduksiD::where('prod_id',$prod_id)->where('qd_id',$prev_qd_id)->update(['qty'=>$qty_masuk]);
                    Invent::where('ref',$data['formdata']['prevReferensi'])
                            ->where('status','in')
                            ->update(['qty'=>$qty_masuk,'tgl'=>$date->format('Y-m-d'),'harga'=>$harga]);
                }

            }
            else {
                $tot_harga = 0;
                $ct_inp = array();
                $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
                $ct_inp['prod_tgl'] = $date->format('Y-m-d');
                $ct_inp['prod_no'] = $data['formdata']['referensi'];
                $ct_inp['wo_id'] = $data['formdata']['wo_id'];

                $ct_inp['opr'] = $data['formdata']['opr'];
                $ct = Produksi::create($ct_inp);
                $data['prod_id'] = $ct->id;

                //barang keluar
                foreach($data['formdata']['datatransprod'] as $jurnal){
                    $qty_keluar = str_replace($search,$replace,$jurnal['qty']);
                    $arr_barang_perk = explode("_",$jurnal['kode_barang']);
                    $bpbd_inp = array();
                    $bpbd_inp['prod_id'] = $data['prod_id'];
                    $bpbd_inp['item_id'] = $jurnal['item_id'];
                    $bpbd_inp['kode_perk'] = $arr_barang_perk[0];
                    $bpbd_inp['kode_barang'] = $arr_barang_perk[1];
                    $bpbd_inp['qty'] = -1*$qty_keluar;
                    $bpbd_inp['harga'] = '';

                    $prod = ProduksiD::create($bpbd_inp);

                    $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and status='IN' and kode_barang=:kode order by tgl ASC",['kode'=>$jurnal['kode_barang']]);

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
                        $stock->kode_barang = $jurnal['kode_barang'];
                        $stock->status = 'OUT';
                        $stock->qty = $qty_pakai;
                        $stock->harga = $stk->harga;
                        $stock->ref = $data['formdata']['referensi'];
                        $stock->ref_harga = $stk->ref;
                        $stock->item_id = $jurnal['item_id'];
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
                    $permintaan = ProduksiD::where('id',$prod->id)->update(['harga'=>$str_harga]);
                }
                //barang masuk

                $qd_id = $data['formdata']['barang_hasil'];
                //ambil data dari quotationd
                $q_qd = Quod::where('id',$qd_id)->first();
                $item_id = $q_qd->item_id;
                //barang masuk--

                //update prod_id di quotationd
                $q_qd->update(['prod_id'=>$data['prod_id']]);
                //get item_id
                $item = Item::where('id',$item_id)->first();
                $qty_masuk = str_replace($search,$replace,$data['formdata']['qty_hasil']);
                $harga = $tot_harga / $qty_masuk;
                $bpbd_inp['prod_id'] = $data['prod_id'];
                $bpbd_inp['qd_id'] = $qd_id;
                $bpbd_inp['item_id'] = $item->id;
                $bpbd_inp['kode_perk'] = $item->kode_perk;
                $bpbd_inp['kode_barang'] = $item->kode_barang;
                $bpbd_inp['qty'] = $qty_masuk;
                ProduksiD::create($bpbd_inp);

                $stock = new Invent();
                $stock->tgl = $date->format('Y-m-d');
                $stock->kode_barang = $item->kode_perk."_".$item->kode_barang;
                $stock->item_id = $item->id;
                $stock->status = 'IN';
                $stock->qty = $qty_masuk;
                $stock->harga = $harga;
                $stock->ref = $data['formdata']['referensi'];
                $stock->keterangan = $q_wo->quotation->so_no."|".$q_wo->quotation->qcustomer->nick;
                $stock->cust_id = $q_wo->quotation->cust_id;
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
        $collection = Produksi::where('prod_no',$id)->with('detail')->get();
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
    public function destroy(Produksi $produksi)
    {
        DB::transaction(function () use ($produksi) {
            $q_inven = DB::table('t_invent')->where('status','OUT')->where('ref',$produksi->prod_no)->get();
            foreach($q_inven as $inv){
                $ref_item_used = intval($inv->used);
                $qty_used = $inv->qty;
                $q_item_used = Invent::where('id',$ref_item_used)->first();
                //$qty_refitem = $q_item_used->qty + $qty_used;
                $qty_refitem_used = $q_item_used->used - $qty_used;
                $q_item_used->update(['used'=>$qty_refitem_used]);
            }
            $q_inven = DB::table('t_invent')->where('ref',$produksi->prod_no)->delete();
            $produksi->delete();
            ProduksiD::where('prod_id',$produksi->id)->delete();
        }, 5);
    }

	public function wolist(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = Wo::whereYear('wo_tgl',$data['THN']);
        return Response::json($bpb->with('quotation','quotation.qcustomer')->get());
    }

	public function woitem(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = Wod::where('wo_id',$data['wo_id']);
        return Response::json($bpb->get());
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

    public function bpblist(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = Bpb::whereYear('bpb_tgl',$data['THN']);

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
        $jurnalH = Bpb::where('bpb_no',$id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpb_id = $jurnalH->id;
        $today = date('d-m-Y');
        $ref = $jurnalH['bpp_no'];
        $jurnalD = Bpbd::where('bpb_id',$bpb_id)->orderBy('kode_perk')->get();

        if($this->nama_lembaga != '' && $this->logo_lembaga != ''){
            // Custom Header
                PDF::setHeaderCallback(function($pdf) {
                    $img_file = K_PATH_IMAGES."/".$this->logo_lembaga;
                    //$pdf->Cell(0, 0, 'TEST lOKASI '.$img_file, 1, 1, 'C', 0, '', 0);
                    if ($pdf->getPage() == 1 || $pdf->getPage() == 2){
                        $pdf->setY(5);
                        $pdf->SetFont('cid0jp', '', 14);
                        $html = '<table cellspacing="0" cellpadding="1" border="0"><tr><td rowspan="3" width="10%"><img src="'.$img_file.'"/></td>
                             <td width="88%">&nbsp;&nbsp;&nbsp;<span style="font-size: 12px;">'.$this->pemkot.'</span></td></tr>
                             <tr><td>&nbsp;&nbsp;&nbsp;<span style="font-size: 14px; font-weight:bold">'.$this->nama_lembaga.'</span></td></tr>
                             <tr><td>&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'.$this->alamat_lembaga.'</span></td></tr></table>';
                        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
                    }
                });
        }
        PDF::SetFont('cid0jp', '', 8,'', 'false');
        PDF::SetTitle('BUKTI JURNAL)');
        PDF::SetMargins(5,32, 5,true);
        PDF::SetAutoPageBreak(TRUE, 0);
        $auto_page_break = PDF::getAutoPageBreak();
        $modname = get_class($this);

        $html =  view('jurnal.bpb',  compact('jurnalH','ref','jurnalD','modname','jenis'));
        //Storage::disk('local')->put('table_bpb.html', $html);
         PDF::AddPage('L','F4');
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('bpb_'.$ref.'.pdf');


    }
}

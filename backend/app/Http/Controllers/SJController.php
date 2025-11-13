<?php

namespace App\Http\Controllers;

use App\Jurnal;
use App\Quotation;
use App\Quod;
use App\SJ;
use App\SJD;
use App\Retur;
use App\ReturD;
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


class SJController extends Controller
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

    public function solist(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = Quotation::where('posting',1)->whereYear('qt_tgl',$data['THN']);
        return Response::json($bpb->with('qcustomer','status')->get());
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
            $so = Quotation::where('so_no',$data['formdata']['prevReferensi'])->first();
            $day_due = (int) $so->payment;
            $keterangan = $so->so_no."|".$so->qcustomer->nick;
            $qt_id = $so->id;
            if($so->p == 1)
                $p = 'P';
            else
                $p = 'N';
            if($data['formdata']['jenis']=='sr'){
                $po_inp['is_sample'] = 1;
                $po_inp['inv_no'] = "S".substr($data['formdata']['referensi'],3);
            }

            else{
                $po_inp['is_sample'] = 0;
                $po_inp['inv_no'] = "J".substr($data['formdata']['referensi'],3);
            }

            $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
            $tgl_sj = $date->format('Y-m-d');
            $today = $date->format('Y-m-d');
            $po_inp['sj_tgl'] = $tgl_sj;
            $po_inp['payment_due'] = $date->addDays($day_due)->format('Y-m-d');
            $po_inp['sj_no'] = $data['formdata']['referensi'];

            $po_inp['so_no'] = $data['formdata']['prevReferensi'];
            $po_inp['opr'] = $data['formdata']['opr'];
            $po_inp['p'] = $p;
            $po = SJ::create($po_inp);
            $data['sj_id'] = $po->id;

            foreach($data['formdata']['datatrans'] as $jurnal){
                $tot_kirim = 0;
                $arr_barang_perk = explode("_",$jurnal['kode_barang']);
                $bpbd_inp = array();
                $bpbd_inp['sj_id'] = $data['sj_id'];
                $bpbd_inp['item_id'] = $jurnal['item_id'];
                $bpbd_inp['kode_perk'] = $arr_barang_perk[0];
                $bpbd_inp['kode_barang'] = $arr_barang_perk[1];
                $bpbd_inp['kode2'] = $jurnal['kode2'];
                $bpbd_inp['nama_barang2'] = $jurnal['nama2'];
                $bpbd_inp['size'] = $jurnal['size'];
                if(!isset($jurnal['qty_kirim'])){
                    //$jurnal['qty_kirim'] = 0;
                    continue;
                }
                $qty_kirim = str_replace($search,$replace,$jurnal['qty_kirim']);
                $bpbd_inp['qty_kirim'] = $qty_kirim;
                $bpbd_inp['satuan'] = str_replace($search,$replace,$jurnal['satuan']);
                $bpbd_inp['harga'] = str_replace($search,$replace,$jurnal['harga']);
                $bpbd_inp['discount'] = str_replace($search,$replace,$jurnal['discount']);
                $bpbd_inp['qd_id'] = $jurnal['quotationd_id'];

                $sjd = SJD::create($bpbd_inp);
                $qtd = Quod::where('id',$jurnal['quotationd_id'])->first();
                $tot_kirim = $qtd->tot_kirim;
                $tot_kirim = $tot_kirim + str_replace($search,$replace,$jurnal['qty_kirim']);
                Quod::where('id',$jurnal['quotationd_id'])->update(['tot_kirim'=> $tot_kirim]);

                 //check production first--
                //1. get qt_id from t_quotation using so_no
                $wo_id = 0;
                $q_quotD = Quod::where('qt_id',$qt_id)->where('item_id',$jurnal['item_id'])->first();
                $q_item = DB::table('t_item')->where('id',$jurnal['item_id'])->first();
                //dd($q_quotD);
                if(!is_null($q_quotD))
                    $wo_id = $q_quotD->wo_id;
                //check stock controll
                if($q_item->stock_controll==1){
                    if($wo_id != 0){
                        $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and status='IN' and item_id=:item_id and cust_id=:cust_id and keterangan=:keterangan
                                order by tgl,id ASC",['item_id'=>$jurnal['item_id'],'cust_id'=>$so->cust_id,'keterangan'=>$keterangan]);
                        $repeat_loop = TRUE;
                        $sisa_pakai = $qty_kirim;
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
                                $sisa_pakai = 0;
                                $repeat_loop = FALSE;
                            }

                            //--insert kartu stock
                            $stock = new Invent();
                            $stock->tgl = $today;
                            $stock->kode_barang = $jurnal['kode_barang'];
                            $stock->status = 'OUT';
                            $stock->qty = $qty_pakai;
                            $stock->harga = $stk->harga;
                            $stock->ref = $data['formdata']['referensi'];
                            $stock->ref_harga = $stk->ref;
                            $stock->keterangan = $so->so_no."|".$so->qcustomer->nick;
                            $stock->item_id = $jurnal['item_id'];
                            $stock->used = $stk->id;
                            $stock->save();

                            //--update used in stock--
                            $inv = Invent::find($stk->id);
                            $inv_upd['used'] = $used;
                            $inv->update($inv_upd);

                            if(!$repeat_loop){
                                break;
                            }
                            $j++;
                        }
                        if($sisa_pakai > 0){
                            $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and status='IN' and item_id=:item_id and cust_id=:cust_id
                                order by tgl,id ASC",['item_id'=>$jurnal['item_id'],'cust_id'=>$so->cust_id]);
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
                                    $sisa_pakai = 0;
                                    $repeat_loop = FALSE;
                                }

                                //--insert kartu stock
                                $stock = new Invent();
                                $stock->tgl = $today;
                                $stock->kode_barang = $jurnal['kode_barang'];
                                $stock->status = 'OUT';
                                $stock->qty = $qty_pakai;
                                $stock->harga = $stk->harga;
                                $stock->ref = $data['formdata']['referensi'];
                                $stock->ref_harga = $stk->ref;
                                $stock->keterangan = $so->so_no."|".$so->qcustomer->nick;
                                $stock->item_id = $jurnal['item_id'];
                                $stock->used = $stk->id;
                                $stock->save();
                                //--update used in stock--
                                $inv = Invent::find($stk->id);
                                $inv_upd['used'] = $used;
                                $inv->update($inv_upd);

                                if(!$repeat_loop){
                                    break;
                                }
                                $j++;
                            }
                        }
                        if($sisa_pakai > 0){
                            $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and status='IN' and item_id=:item_id and cust_id=0
                                order by tgl,id ASC",['item_id'=>$jurnal['item_id']]);
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
                                    $sisa_pakai = 0;
                                    $repeat_loop = FALSE;
                                }
                                //$today = $date->format('Y-m-d');
                                //--insert kartu stock
                                $stock = new Invent();
                                $stock->tgl = $today;
                                $stock->kode_barang = $jurnal['kode_barang'];
                                $stock->status = 'OUT';
                                $stock->qty = $qty_pakai;
                                $stock->harga = $stk->harga;
                                $stock->ref = $data['formdata']['referensi'];
                                $stock->ref_harga = $stk->ref;
                                $stock->keterangan = $so->so_no."|".$so->qcustomer->nick;
                                $stock->item_id = $jurnal['item_id'];
                                $stock->used = $stk->id;
                                $stock->save();
                                //--update used in stock--
                                $inv = Invent::find($stk->id);
                                $inv_upd['used'] = $used;
                                $inv->update($inv_upd);

                                if(!$repeat_loop){
                                    break;
                                }
                                $j++;
                            }
                        }
                        if($sisa_pakai > 0){
                            //$today = $date->format('Y-m-d');
                            //--insert kartu stock
                            $stock = new Invent();
                            $stock->tgl = $today;
                            $stock->kode_barang = $jurnal['kode_barang'];
                            $stock->status = 'OUT';
                            $stock->qty = $sisa_pakai;
                            $stock->harga = 0;
                            $stock->ref = $data['formdata']['referensi'];
                            $stock->ref_harga = '';
                            $stock->keterangan = $so->so_no."|".$so->qcustomer->nick;
                            $stock->item_id = $jurnal['item_id'];
                            $stock->used = 0;
                            $stock->save();
                        }
                    }
                    else{
                        $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and status='IN' and kode_barang=:kode order by tgl ASC",['kode'=>$jurnal['kode_barang']]);

                        $repeat_loop = TRUE;
                        $sisa_pakai = str_replace($search,$replace,$jurnal['qty_kirim']);
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
                                $sisa_pakai = 0;
                                $repeat_loop = FALSE;
                            }

                            //$today = $date->format('Y-m-d');
                            //--insert kartu stock
                            $stock = new Invent();
                            $stock->tgl = $today;
                            $stock->kode_barang = $jurnal['kode_barang'];
                            $stock->status = 'OUT';
                            $stock->qty = $qty_pakai;
                            $stock->harga = $stk->harga;
                            $stock->ref = $data['formdata']['referensi'];
                            $stock->ref_harga = $stk->ref;
                            $stock->keterangan = $so->so_no."|".$so->qcustomer->nick;
                            $stock->item_id = $jurnal['item_id'];
                            $stock->used = $stk->id;
                            $stock->save();

                            //--update used in stock--
                            $inv = Invent::find($stk->id);
                            $inv_upd['used'] = $used;
                            $inv->update($inv_upd);
                            if(!$repeat_loop){
                                break;
                            }
                            $j++;
                        }
                        if($sisa_pakai > 0){
                            //$today = $date->format('Y-m-d');
                            //--insert kartu stock
                            $stock = new Invent();
                            $stock->tgl = $today;
                            $stock->kode_barang = $jurnal['kode_barang'];
                            $stock->status = 'OUT';
                            $stock->qty = $sisa_pakai;
                            $stock->harga = 0;
                            $stock->ref = $data['formdata']['referensi'];
                            $stock->ref_harga = '';
                            $stock->keterangan = $so->so_no."|".$so->qcustomer->nick;
                            $stock->item_id = $jurnal['item_id'];
                            $stock->used = 0;
                            $stock->save();
                        }
                        //--update harga di permintaan detail--
                        $permintaan = SJD::find($sjd->id);
                        $permintaan_upd['hpp'] = $str_harga;
                        $permintaan->update($permintaan_upd);
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
        $data = SJ::where('sj_no',$id)->first();
        return Response::json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SJ $sj)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data,$sj) {
            $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['sj_tgl']);
            $prev_no = $sj->sj_no;
            $sj->update(['sj_tgl'=>$date->format('Y-m-d'),'sj_no'=>$data['formdata']['sj_no']]);
            $sj->update(['sj_tgl'=>$date->format('Y-m-d'),'sj_no'=>$data['formdata']['sj_no']]);
            DB::table('t_invent')->where('ref',$prev_no)->update(['ref'=>$data['formdata']['sj_no']]);
        }, 5);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function destroy(SJ $sj) {
        DB::transaction(function () use ($sj) {
            $sj_id = $sj->id;
            $so_no = $sj->so_no;
            $sj_no = $sj->sj_no;
            $no_fp = $sj->no_fp;
            //sj detail
            $q_sjd = SJD::where('sj_id',$sj_id)->get();
            foreach($q_sjd as $det){
                $item_id = $det->item_id;
                $qty_kirim = $det->qty_kirim;
                $q_invent = DB::table('t_invent')->where('ref',$sj_no)->where('item_id',$item_id)->get();
                //dd($q_invent);
                foreach($q_invent as $invent){
                    $inv_id = intval($invent->used);
                    $q_invItem = DB::table('t_invent')->where('id',$inv_id)->first();
                    $item_used = $q_invItem->used - $invent->qty;
                    DB::table('t_invent')->where('id',$inv_id)->update(['used'=>$item_used]);
                    DB::table('t_invent')->where('id',$invent->id)->delete();
                }
                //quotation detail
                $q_quot = Quotation::where('so_no',$so_no)->first();
                $qt_id = $q_quot->id;
                $q_quotD = Quod::where('qt_id',$qt_id)->where('item_id',$item_id)->first();
                if(!is_null($q_quotD)){
                    $tot_kirim = $q_quotD->tot_kirim - $qty_kirim;
                    Quod::where('qt_id',$qt_id)->where('item_id',$item_id)->update(['tot_kirim'=>$tot_kirim]);
                }
            }
            SJD::where('sj_id',$sj_id)->delete();
            $sj->delete();
            if($no_fp !='-'){
                $arr_no = explode('.',$no_fp);
                $kode_depan = $arr_no[0].".".$arr_no[1];
                DB::table('t_fakturpajak')->where('no_fp',$arr_no[2])->where('kode_depan',$kode_depan)->update(['status'=>0]);
            }
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
            $data_jurnal = Quotation::where('qt_no',$qt_no)->first();
            //dd($data_jurnal);
            if(!is_null($data_jurnal)){
                $date = Carbon::now();
                $referensi = DB::select('SELECT MAX(CAST(SUBSTR(so_no,5,4) AS DECIMAL)) as number FROM t_quotation WHERE YEAR(qt_tgl)='.$date->format('Y').'  AND MONTH(qt_tgl)='.$date->format('n'));
				//dd($referensi);
				$number = $referensi[0]->number + 1;
				$str_ref = "SO-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');

                Quotation::where('qt_no',$qt_no)->update(['posting'=>1,'so_no'=>$str_ref]);
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

    public function sjhistory(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = SJ::distinct()->where('so_no',$data['so_no'])->orderBy('sj_tgl')->get(['id','inv_no','sj_no','sj_tgl','so_no']);
        return Response::json($bpb);
    }

    public function sjdet(Request $request){
        $data = json_decode($request->getContent(), true);
        $bpb = SJD::where('sj_id',$data['sj_id'])
                    ->with('itembarang')
                    ->orderBy('kode_perk')->get();
        return Response::json($bpb);
    }

    public function sjbyno($sj_no){
        $sj = SJ::where('sj_no',$sj_no)->first();
        $bpb = SJD::where('sj_id',$sj->id)
                    ->with('itembarang')
                    ->with('sj.sales')
                    ->orderBy('kode_perk')->get();
        return Response::json($bpb);
    }

    public function returlist() {
        $data = DB::table('v_retur')->get();
        return Response::json($data);
    }

    public function retur(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        DB::transaction(function () use ($data) {
            $search  = array(',','.');
            $replace = array('','.');
            $po_inp = array();
            $ret_inp['ret_no'] = $data['formdata']['referensi'];
            $ret_inp['ret_tgl'] = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
            $ret_inp['cust_id'] = $data['formdata']['cust_id'];
            $ret_inp['inv_no'] = $data['formdata']['inv_no'];
            $ret_inp['sj_no'] = $data['formdata']['sj_no'];
            $ret = Retur::create($ret_inp);
            foreach($data['formdata']['datatrans'] as $jurnal){
                $qty = str_replace($search,$replace,$jurnal['qty_retur']);
                if($qty == 0)
                    continue;
                $bpbd_inp['ret_id'] = $ret->id;
                $bpbd_inp['item_id'] = $jurnal['item_id'];
                $bpbd_inp['kode_perk'] = $jurnal['kode_perk'];
                $bpbd_inp['kode_barang'] = $jurnal['kode_barang'];
                $bpbd_inp['kode2'] = $jurnal['kode2'];
                $bpbd_inp['nama_barang2'] = $jurnal['nama_barang2'];
                $bpbd_inp['size'] = $jurnal['size'];
                $bpbd_inp['qty_retur'] = str_replace($search,$replace,$jurnal['qty_retur']);
                $bpbd_inp['harga'] = $jurnal['harga'];
                $bpbd_inp['discount'] = $jurnal['discount'];
                $bpbd_inp['satuan'] = $jurnal['satuan'];
                $bpbd_inp['sjd_id'] = $jurnal['id'];
                ReturD::create($bpbd_inp);

                $hpp = explode("|",$jurnal['hpp']);
                $harga_satuan = explode("@",$hpp[0]);

                $stock = new Invent();
                $stock->tgl = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
                $stock->kode_barang = $jurnal['kode_perk']."_".$jurnal['kode_barang'];
                $stock->item_id = $jurnal['item_id'];
                $stock->status = 'IN';
                $stock->qty = str_replace($search,$replace,$jurnal['qty_retur']);
                $stock->harga = $harga_satuan[1];
                $stock->ref = $data['formdata']['referensi'];
                $stock->ref_harga = $jurnal['hpp'];
                $stock->cust_id = $data['formdata']['cust_id'];
                $stock->save();
            }

        }, 5);

    }

    public function pdf($id,$generate=0){
        $jurnalH = Quotation::where('qt_no',$id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpb_id = $jurnalH->id;
        $today = date('d-m-Y');
        $ref = $jurnalH['qt_no'];
        $jurnalD = Quod::where('qt_id',$bpb_id)->orderBy('kode_perk')->get();

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

        $html =  view('jurnal.qt',  compact('jurnalH','ref','jurnalD','modname','jenis'));
        Storage::disk('local')->put('table_quot.html', $html);
         PDF::AddPage('P','A4');
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('bpb_'.$ref.'.pdf');


    }
}

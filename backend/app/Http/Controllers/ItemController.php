<?php

namespace App\Http\Controllers;

use App\Item;
use App\Perkiraan;
use App\Cutting;
use App\CuttingD;
use App\Produksi;
use App\ProduksiD;
use App\SJ;
use App\SJD;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreItem;
use App\Invent;
use App\Quod;
use Illuminate\Support\Facades\Auth;


class ItemController extends Controller
{
    var $username = '';

    public function index()
    {
        //filter jenis dan tahun

        /* $query_item = Item::with(['stock'=> function($query){
            $query->select(DB::raw('item_id,SUM(qty-used) AS stock'))
            ->where('qty','<>','used')
            ->where('status','IN')
            ->groupBy('kode_barang')->value('stock');
            }])->get();
        //dd($query_item->flatten(1));
        return Response::json($query_item->flatten()); */

        $q_item = Item::get()->map(function($item){
            $closing = DB::table('t_closing')->selectRaw('YEAR(closing_year) AS closing_year')->first();
           $tahun = session('thnValuta');
           $item['stock'] = Invent::selectRaw('IFNULL(SUM(qty-used),0) AS sisa')->where('item_id',$item->id)
                            ->where('qty','<>','used')
                            ->where('status','IN')
                            ->whereYear('tgl',$tahun)
                            ->first();
           return $item;
        })->all();
        return Response::json($q_item);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItem $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $data_inp['kode_perk'] = $data['formdata']['kode_perk'];
            $data_inp['kode_barang'] = $data['formdata']['kode_barang'];
            $data_inp['uraian'] = $data['formdata']['uraian'];
            $data_inp['satuan'] = $data['formdata']['satuan'];
            $data_inp['use_sn'] = $data['formdata']['use_sn'];
            if($data['formdata']['isEdit']) {
                Item::where('id',$data['formdata']['prevReferensi'])->update($data_inp);
            }
            else{
                Item::create($data_inp);
            }

        }, 5);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Jurnal  $jurnal
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        return Response::json($item);
    }

    public function itemlist(){
        //DB::enableQueryLog();
        $q_item = Perkiraan::select(DB::raw('kode_perk, CONCAT(RPAD(kode_perk,15,"="),"+",nama_perk) as nama,level'))->where('parent','15')
                        ->with(['subperkiraan' => function($query){
                            $query->select(DB::raw('kode_perk, CONCAT(RPAD(kode_perk,15,"="),"+",nama_perk) as nama,parent,level'));
                        },'subperkiraan.itembarang' => function($query){
                            $query->select(DB::raw('id as item_id, id,CONCAT(RPAD(CONCAT(kode_perk,"_",kode_barang),30,"="),"+",uraian) as nama,kode_perk,satuan,CONCAT(kode_perk,"_",kode_barang) as kode_barang, stock_controll'))->orderBy('kode_barang','asc');
                        }])->get()->map(function($item){
                            $item->subperkiraan->map(function($itemsub){
                                $itemsub->itembarang->map(function($itembarang){
                                    //print_r($itembarang);
                                    $closing = DB::table('t_closing')->selectRaw('YEAR(closing_year) AS closing_year')->first();
                                    $tahun = session('thnValuta');
                                    if($itembarang->stock_controll == 1){
                                        $itembarang['stock'] = Invent::selectRaw('IFNULL(SUM(qty-used),0) AS sisa')->where('item_id',$itembarang->id)
                                            ->where('qty','<>','used')
                                            ->where('status','IN')
                                            ->whereYear('tgl',$tahun)
                                            ->first();
                                        return $itembarang;
                                    }
                                    else{
                                        $itembarang['stock']['sisa'] = 1;
                                        return $itembarang;
                                    }

                                });
                            return $itemsub;
                            });
                        return $item;
                        })->all();
        return Response::json($q_item);

        //$query_dump = DB::getQueryLog();
        //print_r($query_dump);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Jurnal  $jurnal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Jurnal $jurnal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Jurnal  $jurnal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Jurnal $jurnal)
    {
        //
    }
    public function hitung_saldo_awal(){
        DB::transaction(function () {
            $tahun = session('thnValuta');
            $tahun_prev = $tahun - 1;
            $tgl_saldo=$tahun.'-01-01';
            DB::statement('DROP TABLE IF EXISTS stok_closing_'.$tahun_prev);
            DB::statement("CREATE TABLE stok_closing_".$tahun_prev." AS SELECT t1.*,(SELECT IFNULL(SUM(t2.qty),0) FROM t_invent AS t2 WHERE t2.status='out' AND t2.used=t1.`id` AND YEAR(t2.tgl)=".$tahun_prev.") AS dipakai
                            FROM t_invent AS t1 WHERE t1.status='in' AND YEAR(t1.tgl)=".$tahun_prev);
            DB::statement('DROP TABLE IF EXISTS stock_awal_'.$tahun);
            DB::statement("CREATE TABLE stock_awal_".$tahun." AS
                            SELECT item_id,kode_barang,'".$tgl_saldo."' AS tgl,'IN' AS `status`,'STOK AWAL ".$tahun."' AS ref,(qty-dipakai) AS sisa,harga,cust_id FROM stok_closing_".$tahun_prev." WHERE (qty - dipakai) > 0");
            DB::statement("DELETE FROM t_invent WHERE ref='STOK AWAL ".$tahun."'");
            DB::statement("INSERT INTO t_invent (item_id,kode_barang,tgl,STATUS,ref,qty,harga,cust_id)
                            SELECT item_id,kode_barang,tgl,STATUS,ref,sisa,harga,cust_id FROM stock_awal_2024 WHERE sisa > 0");

        }, 5);
    }

    public function hitung_ulang_stock(){
        //DB::transaction(function () {
            $closing = DB::table('t_closing')->selectRaw('YEAR(closing_year) AS closing_year, closing_month')->first();
            $tahun = session('thnValuta');
            $closingMonth = $closing && isset($closing->closing_month) ? (int)$closing->closing_month : 0;

            // Hapus hanya transaksi setelah bulan closing pada tahun berjalan
            DB::table('t_invent')
                ->where('status','out')
                ->whereYear('tgl',$tahun)
                ->whereRaw('MONTH(tgl) > ?', [$closingMonth])
                ->delete();
            DB::table('t_invent')
                ->where('ref','like','CT-%')
                ->whereYear('tgl',$tahun)
                ->whereRaw('MONTH(tgl) > ?', [$closingMonth])
                ->delete();
            DB::table('t_invent')
                ->where('ref','like','PD-%')
                ->whereYear('tgl',$tahun)
                ->whereRaw('MONTH(tgl) > ?', [$closingMonth])
                ->delete();

            // Recalculate used for all IN rows in the year based on remaining OUT rows (up to closing month)
            DB::statement("UPDATE t_invent t1 SET used = COALESCE((SELECT SUM(t2.qty) FROM t_invent t2 WHERE t2.status='out' AND t2.used=t1.id),0) WHERE t1.status='in' AND YEAR(t1.tgl)=$tahun");

            DB::table('hitungstock_temp')->truncate();
            DB::statement("
                INSERT INTO hitungstock_temp (tgl, doc_no, doc_id, jenis, priority)
                SELECT sj_tgl, sj_no, id, 'sj', 3 FROM t_sj WHERE YEAR(sj_tgl)=$tahun AND MONTH(sj_tgl) > $closingMonth
                UNION ALL
                SELECT ct_tgl, ct_no, id, 'ct', 1 FROM t_cutting WHERE YEAR(ct_tgl)=$tahun AND MONTH(ct_tgl) > $closingMonth
                UNION ALL
                SELECT prod_tgl, prod_no, id, 'prod', 2 FROM t_produksi WHERE YEAR(prod_tgl)=$tahun AND MONTH(prod_tgl) > $closingMonth
            ");

            $results = DB::select("SELECT * FROM hitungstock_temp ORDER BY tgl, priority");
            foreach ($results as $row){
                $jenis = $row->jenis;
                switch($jenis){
                    case 'ct':
                        $this->koreksi_ct($row->doc_id);
                        break;
                    case'prod':
                        $this->koreksi_prod($row->doc_id);
                        break;
                    case 'sj':
                        $this->koreksi_sj($row->doc_id);
                        break;
                }

            }

        //}, 5);
    }

    public function koreksi_ct($id){
        DB::transaction(function () use ($id){
            $closing = DB::table('t_closing')->selectRaw('YEAR(closing_year) AS closing_year')->first();
            $tahun = session('thnValuta');
            $q_data = DB::table('t_cutting_item as d')
                        ->join('t_cutting as h','h.id','=','d.ct_id')
                        //->where('d.qty','<',0)
                        ->where('h.id',$id)
                        ->orderBy('d.qty','asc')
                        ->get();
            $tot_harga = 0;
            //$tot_qty_out = 0;
            $prev_ct = "";
            $hasil_banyak = false;
            foreach($q_data as $it){
                $ct_id = $it->ct_id;
                //check hasil cutting
                $q_hasil = DB::table('t_cutting_item as d')->where('ct_id',$ct_id)->where('qty','>','0')->count();
                $q_hasil_fp_qty = DB::table('t_cutting_item as d')->selectRaw('SUM(qty*fp) AS qty_fp')
                                    ->where('ct_id',$ct_id)
                                    ->where('qty','>','0')
                                    ->first();
                $tot_hasil_prod = $q_hasil_fp_qty->qty_fp;
                if($q_hasil > 1)
                    $hasil_banyak = true;
                if($prev_ct != $ct_id){
                    $tot_harga = 0;
                    //$tot_qty_out = 0;
                    $hasil_banyak = false;
                }

                $item_id = $it->item_id;
                $kode_perk = $it->kode_perk;
                $kode_barang = $it->kode_barang;
                $ct_tgl = $it->ct_tgl;
                $ct_no = $it->ct_no;
                $qty = $it->qty;
                $fp = $it->fp;

                if($qty==0)
                    continue;
                if($qty < 0){
                    $qty = -1*$qty;
                    $jenis='OUT';
                    $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                            WHERE qty <> used and status='IN' and year(tgl)=:year and item_id=:item_id order by tgl ASC",['year'=>$tahun,'item_id'=>$item_id]);

                    $repeat_loop = TRUE;
                    $sisa_pakai = $qty;
                    $str_harga = "";

                    $j=1;
                    foreach($stock as $stk){
                        if($j>1)
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
                        $stock->tgl = $ct_tgl;
                        $stock->kode_barang = $kode_perk."_".$kode_barang;
                        $stock->status = 'OUT';
                        $stock->qty = $qty_pakai;
                        $stock->harga = $stk->harga;
                        $stock->ref = $ct_no;
                        $stock->ref_harga = $stk->ref;
                        $stock->item_id = $item_id;;
                        $stock->used = $stk->id;
                        $stock->save();
                        $tot_harga = $tot_harga + ($stk->harga * $qty_pakai);
                        //$tot_qty_out = $tot_qty_out + $fp*$qty_pakai;
                        //--update used in stock--
                        $inv = Invent::find($stk->id);
                        $inv_upd['used'] = $used;
                        $inv->update($inv_upd);

                        if(!$repeat_loop){
                            break;
                        }
                        $j++;
                    }
                    //$tot_harga = $tot_harga/$j;
                    //$tot_qty_out = $tot_qty_out/$j;

                    //--update harga di permintaan detail--
                    $permintaan = CuttingD::where('ct_id',$ct_id)->where('item_id',$item_id)->update(['harga'=>$str_harga]);

                }
                else{
                    $harga_sisa = 0;
                    //echo "FP:".$fp."ct_id".$ct_id."item_id:".$item_id."<br/>";
                    if($hasil_banyak){
                        $harga = $tot_harga*$fp/$tot_hasil_prod;
                        /* $harga = $fp*($tot_harga / ($tot_qty_out));
                        if($tot_qty_out - $tot_hasil_prod > 0){
                            $harga_sisa = ($tot_qty_out - $tot_hasil_prod)*($tot_harga/$tot_qty_out);
                            $harga_sisa = $fp*$harga_sisa/$tot_hasil_prod;
                        }
                        $harga = $harga + $harga_sisa; */
                    }
                    else{
                        $harga = $tot_harga / $qty;
                    }
                    $jenis='IN';
                    $item = Item::where('id',$item_id)->first();
                    $stock = new Invent();
                    $stock->tgl = $ct_tgl;
                    $stock->kode_barang = $kode_perk."_".$kode_barang;
                    $stock->item_id = $item_id;
                    $stock->status = 'IN';
                    $stock->qty = $qty;
                    $stock->harga = $harga;
                    $stock->ref = $ct_no;
                    $stock->ref_harga = "harga:".$tot_harga."fp:".$fp;
                    $stock->save();

                    CuttingD::where('ct_id',$ct_id)->where('item_id',$item_id)->update(['harga'=>$harga]);
                }

                $prev_ct = $it->ct_id;
            }
        }, 5);
    }

    public function koreksi_prod($id){
        DB::transaction(function () use ($id){
            $tahun = session('thnValuta');
            $q_data = DB::table('t_produksi_item as d')
                        ->select('d.prod_id','d.item_id','d.qty','d.kode_perk','d.kode_barang','h.prod_tgl','h.prod_no','q.cust_id','c.nick','q.so_no')
                        ->join('t_produksi as h','h.id','=','d.prod_id')
                        ->join('t_wo as w','w.id','=','h.wo_id')
                        ->join('t_quotation as q','q.id','=','w.qt_id')
                        ->join('t_customer as c','c.id','=','q.cust_id')
                        ->where('h.id',$id)
                        ->orderBy('d.qty','asc')
                        ->get();
            $tot_harga = 0;
            $prev_ct = "";
            foreach($q_data as $it){
                $ct_id = $it->prod_id;
                if($prev_ct != $ct_id)
                    $tot_harga = 0;
                $item_id = $it->item_id;
                $kode_perk = $it->kode_perk;
                $kode_barang = $it->kode_barang;
                $ct_tgl = $it->prod_tgl;
                $ct_no = $it->prod_no;
                $so_no = $it->so_no;
                $nick = $it->nick;
                $cust_id = $it->cust_id;
                $qty = $it->qty;
                if($qty==0)
                    continue;
                if($qty < 0){
                    $qty = -1*$qty;
                    $jenis='OUT';
                    $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                            WHERE qty <> used and status='IN' and year(tgl)=:year and item_id=:item_id order by tgl ASC",['year'=>$tahun,'item_id'=>$item_id]);

                    $repeat_loop = TRUE;
                    $sisa_pakai = $qty;
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
                        $stock->tgl = $ct_tgl;
                        $stock->kode_barang = $kode_perk."_".$kode_barang;
                        $stock->status = 'OUT';
                        $stock->qty = $qty_pakai;
                        $stock->harga = $stk->harga;
                        $stock->ref = $ct_no;
                        $stock->ref_harga = $stk->ref;
                        $stock->item_id = $item_id;
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
                    $permintaan = ProduksiD::where('prod_id',$ct_id)->where('item_id',$item_id)->update(['harga'=>$str_harga]);

                }
                else{
                    $harga = $tot_harga / $qty;
                    $jenis='IN';
                    $item = Item::where('id',$item_id)->first();
                    $stock = new Invent();
                    $stock->tgl = $ct_tgl;
                    $stock->kode_barang = $kode_perk."_".$kode_barang;
                    $stock->item_id = $item_id;
                    $stock->status = 'IN';
                    $stock->qty = $qty;
                    $stock->harga = $harga;
                    $stock->ref_harga = "harga:".$tot_harga;
                    $stock->keterangan = $so_no."|".$nick;
                    $stock->cust_id = $cust_id;
                    $stock->ref = $ct_no;
                    $stock->save();
                    ProduksiD::where('prod_id',$ct_id)->where('item_id',$item_id)->update(['harga'=>$harga]);
                }

                $prev_ct = $it->prod_id;
            }
        }, 5);
    }

    public function koreksi_sj($id){
        DB::transaction(function () use ($id){
            $tahun = session('thnValuta');
            $q_data = DB::table('t_sjd as d')
                        ->select('d.id as sjd_id','d.sj_id','d.item_id','d.qd_id','d.kode_perk','d.kode_barang',
                                'd.qty_kirim','h.sj_tgl','h.sj_no','h.so_no','q.id','q.cust_id','c.nick')
                        ->join('t_sj as h','h.id','=','d.sj_id')
                        ->join('t_quotation as q','h.so_no','=','q.so_no')
                        ->join('t_customer as c','q.cust_id','=','c.id')
                        ->where('h.id',$id)
                        ->orderBy('d.id','asc')
                        ->get();
            //dd($q_data);
            foreach($q_data as $it){
                $ct_id = $it->sjd_id;
                $item_id = $it->item_id;
                $qd_id = $it->qd_id;
                $kode_perk = $it->kode_perk;
                $kode_barang = $it->kode_barang;
                $ct_tgl = $it->sj_tgl;
                $ct_no = $it->sj_no;
                $so_no = $it->so_no;
                $qty = $it->qty_kirim;
                $qt_id = $it->id;
                $cust_id = $it->cust_id;
                $nick = $it->nick;
                $keterangan = $so_no."|".$nick;
                list($year_sj,$month_sj,$day_sj) = explode("-",$ct_tgl);
                $q_item = DB::table('t_item')->where('id',$item_id)->first();
                if($q_item->stock_controll==0)
                    continue;
                else{
                if($qty==0)
                    continue;
                else{
                    //check production first--
                    //1. get qt_id from t_quotation using so_no
                    $wo_id = 0;
                    $prod_id = 0;
                    if($qd_id != 0)
                        $q_quotD = Quod::where('qt_id',$qt_id)->where('id',$qd_id)->first();
                    else
                        $q_quotD = Quod::where('qt_id',$qt_id)->where('item_id',$item_id)->orderBy('id','ASC')->first();
                    //dd($q_quotD);
                    if(!is_null($q_quotD)){
                        $wo_id = $q_quotD->wo_id;
                        $prod_id = $q_quotD->prod_id;
                    }
                    if($wo_id != 0){
                        if($prod_id != 0){
                            $q_prod = Produksi::where('id',$prod_id)->first();
                            $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and status='IN' and year(tgl)=:year and item_id=:item_id and cust_id=:cust_id and ref=:prod_id
                                order by tgl,id ASC",['year'=>$year_sj,'item_id'=>$item_id,'cust_id'=>$cust_id,'prod_id'=>$q_prod->prod_no]);
                        }
                        else{
                            $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and status='IN' and year(tgl)=:year and  item_id=:item_id and cust_id=:cust_id and keterangan=:keterangan
                                order by tgl,id ASC",['year'=>$year_sj,'item_id'=>$item_id,'cust_id'=>$cust_id,'keterangan'=>$keterangan]);
                        }

                        $repeat_loop = TRUE;
                        $sisa_pakai = $qty;
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
                            $stock->tgl = $ct_tgl;
                            $stock->kode_barang = $kode_perk."_".$kode_barang;
                            $stock->status = 'OUT';
                            $stock->qty = $qty_pakai;
                            $stock->harga = $stk->harga;
                            $stock->ref = $ct_no;
                            $stock->ref_harga = $stk->ref;
                            $stock->keterangan = $so_no."|".$nick;
                            $stock->item_id = $item_id;
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
                                WHERE qty <> used and status='IN' and year(tgl)=:year and item_id=:item_id and cust_id=:cust_id
                                order by tgl,id ASC",['year'=>$year_sj,'item_id'=>$item_id,'cust_id'=>$cust_id]);
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
                                $stock->tgl = $ct_tgl;
                                $stock->kode_barang = $kode_perk."_".$kode_barang;
                                $stock->status = 'OUT';
                                $stock->qty = $qty_pakai;
                                $stock->harga = $stk->harga;
                                $stock->ref = $ct_no;
                                $stock->ref_harga = $stk->ref;
                                $stock->keterangan = $so_no."|".$nick;
                                $stock->item_id = $item_id;
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
                                WHERE qty <> used and status='IN' and year(tgl)=:year and item_id=:item_id and cust_id=0
                                order by tgl,id ASC",['year'=>$year_sj,'item_id'=>$item_id]);
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
                                $stock->tgl = $ct_tgl;
                                $stock->kode_barang = $kode_perk."_".$kode_barang;
                                $stock->status = 'OUT';
                                $stock->qty = $qty_pakai;
                                $stock->harga = $stk->harga;
                                $stock->ref = $ct_no;
                                $stock->ref_harga = $stk->ref;
                                $stock->keterangan = $so_no."|".$nick;
                                $stock->item_id = $item_id;
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

                            //--insert kartu stock
                            $stock = new Invent();
                            $stock->tgl = $ct_tgl;
                            $stock->kode_barang = $kode_perk."_".$kode_barang;
                            $stock->status = 'OUT';
                            $stock->qty = $sisa_pakai;
                            $stock->harga = 0;
                            $stock->ref = $ct_no;
                            $stock->ref_harga ='';
                            $stock->keterangan = $so_no."|".$nick;
                            $stock->item_id = $item_id;
                            $stock->used = 0;
                            $stock->save();
                        }
                        //update sjd
                        $permintaan = SJD::where('id',$ct_id)->update(['hpp'=>$str_harga]);

                    }
                    else{  //if not production

                        $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and cust_id=0 and status='IN' and year(tgl)=:year and item_id=:item_id order by tgl ASC",['year'=>$year_sj,'item_id'=>$item_id]);

                        $repeat_loop = TRUE;
                        $sisa_pakai = $qty;
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
                            $stock->tgl = $ct_tgl;
                            $stock->kode_barang = $kode_perk."_".$kode_barang;
                            $stock->status = 'OUT';
                            $stock->qty = $qty_pakai;
                            $stock->harga = $stk->harga;
                            $stock->ref = $ct_no;
                            $stock->ref_harga = $stk->ref;
                            $stock->keterangan = $so_no."|".$nick;;
                            $stock->item_id = $item_id;
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
                                WHERE qty <> used and status='IN' and year(tgl)=:year and item_id=:item_id and cust_id=:cust_id
                                order by tgl,id ASC",['year'=>$year_sj,'item_id'=>$item_id,'cust_id'=>$cust_id]);
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
                                $stock->tgl = $ct_tgl;
                                $stock->kode_barang = $kode_perk."_".$kode_barang;
                                $stock->status = 'OUT';
                                $stock->qty = $qty_pakai;
                                $stock->harga = $stk->harga;
                                $stock->ref = $ct_no;
                                $stock->ref_harga = $stk->ref;
                                $stock->keterangan = $so_no."|".$nick;
                                $stock->item_id = $item_id;
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

                            //--insert kartu stock
                            $stock = new Invent();
                            $stock->tgl = $ct_tgl;
                            $stock->kode_barang = $kode_perk."_".$kode_barang;
                            $stock->status = 'OUT';
                            $stock->qty = $sisa_pakai;
                            $stock->harga = 0;
                            $stock->ref = $ct_no;
                            $stock->ref_harga ='';
                            $stock->keterangan = $so_no."|".$nick;
                            $stock->item_id = $item_id;
                            $stock->used = 0;
                            $stock->save();
                        }
                        //--update harga di permintaan detail--
                        $permintaan = SJD::where('id',$ct_id)->where('item_id',$item_id)->update(['hpp'=>$str_harga]);
                    }
                }
            }
            }
        }, 5);
    }

}

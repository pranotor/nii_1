<?php

namespace App\Http\Controllers;

use App\Bpp;
use App\Bppd;
use App\Jurnal;
use App\Invent;
use App\Item;
use App\Tandatangan;
use App\Perkiraan;
use App\Penggunaan;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreBpp;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Arr;
use App\Traits\Jurnaldata;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class BppController extends Controller
{
    use Jurnaldata;
    var $username = '';
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
        return Response::json(bpp::get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBpp $request)
    {
        $data = json_decode($request->getContent(), true);
        if($data['formdata']['jenis']=='bppposting'){
            DB::transaction(function () use ($data) {
                $jenis = 5;
                $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
                $referensi = DB::select('SELECT MAX(CAST(SUBSTR(referensi,1,4) AS DECIMAL)) as number FROM jurnal WHERE YEAR(tanggal)='.$date->format('Y').' AND jenis='.$jenis.' AND MONTH(tanggal)='.$date->format('n'));
				$number = $referensi[0]->number + 1;
				$str_ref = str_pad($number, 4, "0", STR_PAD_LEFT).".".$jenis.".".$date->format('m').".".$date->format('y');

                foreach($data['formdata']['datajurnal'] as $jurnal){
                    $jurnal_inp = array();
                    $jurnal_inp['kode'] = $jurnal['kode'];
                    $jurnal_inp['debet'] = str_replace(',','',$jurnal['debet']);
                    $jurnal_inp['kredit'] = str_replace(',','',$jurnal['kredit']);
                    $jurnal_inp['jenis'] = $jenis;
                    $jurnal_inp['tanggal'] = $date->format('Y-m-d');
                    $jurnal_inp['referensi'] = $str_ref;
                    $jurnal_inp['uraian'] = $data['formdata']['uraian']." (".$data['formdata']['prevReferensi'].", ".$data['formdata']['tanggal'].")";
                    $jurnal_inp['unit'] = '-';
                    $jurnal_inp['rekanan'] = '-';
                    $jurnal_inp['document'] = $data['formdata']['prevReferensi'];
                    Jurnal::create($jurnal_inp);
                }

                Bpp::where('bpp_no',$data['formdata']['prevReferensi'])->update(['posting'=>1]);
            }, 5);
        }
        else{
            DB::transaction(function () use ($data) {
                if($data['formdata']['isEdit']) {
                    //delete
                    $bpp = Bpp::where('bpp_no',$data['formdata']['prevReferensi'])->first();
                    $bpp_id = $bpp->id;
                    $bpp->delete();
                    bppd::where('bpp_id',$bpp_id)->delete();

                    //koreksi stok
                    $rev_stock = DB::select("SELECT * FROM `t_invent`
                    WHERE ref=:ref and status='OUT' order by id ASC",['ref'=>$data['formdata']['prevReferensi']]);

                    foreach($rev_stock as $st){
                        $item_koreksi = (int)$st->used;
                        $qty_koreksi = $st->qty;

                        $inv = Invent::find($item_koreksi);
                        $inv_koreksi['used'] = $inv->used - $qty_koreksi;
                        $inv->update($inv_koreksi);
                    }

                    Invent::where('ref',$data['formdata']['prevReferensi'])->delete();

                }
                $search  = array('.',',');
                $replace = array('','.');
                $bpp_inp = array();
                $kegunaan = Penggunaan::find($data['formdata']['guna_id']);
                $date = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
                $bpp_inp['tanggal'] = $date->format('Y-m-d');
                $bpp_inp['bpp_no'] = $data['formdata']['referensi'];
                $bpp_inp['guna_id'] = $data['formdata']['guna_id'];
                $bpp_inp['uraian'] = $data['formdata']['uraian'];
                $bpp_inp['pengelola'] = $data['formdata']['pengelola'];
                $bpp_inp['dikirim'] = $data['formdata']['dikirim'];
                $bpp_inp['pemohon_nik'] = $data['formdata']['pemohon_nik'];
                $bpp_inp['pemohon'] = $data['formdata']['pemohon'];
                $bpp_inp['pemohon_jabatan'] = $data['formdata']['pemohon_jabatan'];
                $bpp_inp['disetujui'] = $data['formdata']['disetujui'];
                $bpp_inp['disetujui_nik'] = $data['formdata']['disetujui_nik'];
                $bpp_inp['disetujui_jabatan'] = $data['formdata']['disetujui_jabatan'];
                $bpp_inp['mengeluarkan'] = $data['formdata']['mengeluarkan'];
                $bpp_inp['mengeluarkan_nik'] = $data['formdata']['mengeluarkan_nik'];
                $bpp_inp['mengeluarkan_jabatan'] = $data['formdata']['mengeluarkan_jabatan'];
                $bpp_inp['penerima'] = $data['formdata']['penerima'];
                $bpp_inp['penerima_nik'] = $data['formdata']['penerima_nik'];
                $bpp_inp['penerima_jabatan'] = $data['formdata']['penerima_jabatan'];
                $bpp_inp['pembuat'] = $data['formdata']['opr'];
                $bpp = Bpp::create($bpp_inp);
                $data['bpp_id'] = $bpp->id;

                foreach($data['formdata']['datatrans'] as $jurnal){
                    $arr_barang_perk = explode("_",$jurnal['kode_barang']);
                    $bppd_inp = array();
                    $bppd_inp['bpp_id'] = $data['bpp_id'];
                    $bppd_inp['item_id'] = $jurnal['item_id'];
                    $bppd_inp['kode_perk'] = $arr_barang_perk[0];
                    $bppd_inp['kode_barang'] = $arr_barang_perk[1];
                    $bppd_inp['qty_pesan'] = $jurnal['qty_pesan'];
                    $bppd_inp['qty_terima'] = $jurnal['qty_terima'];
                    $bppd = Bppd::create($bppd_inp);


                    $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                            WHERE qty <> used and status='IN' and kode_barang=:kode order by tgl ASC",['kode'=>$jurnal['kode_barang']]);

                    $repeat_loop = TRUE;
                    $sisa_pakai = $jurnal['qty_terima'];
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

                        $today = $date->format('Y-m-d');
                        //--insert kartu stock
                        $stock = new Invent();
                        $stock->tgl = $today;
                        $stock->kode_barang = $jurnal['kode_barang'];
                        $stock->status = 'OUT';
                        $stock->qty = $qty_pakai;
                        $stock->harga = $stk->harga;
                        $stock->ref = $data['formdata']['referensi'];
                        $stock->ref_harga = $stk->ref;
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
                    //--update harga di permintaan detail--
                    $permintaan = Bppd::find($bppd->id);
                    $permintaan_upd['harga'] = $str_harga;
                    $permintaan->update($permintaan_upd);
                }

            }, 5);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //DB::enableQueryLog();
        //return Response::json(bpp::where('bpp_no',$id)->with(['bppd.itembarang'])->get());
        $collection = bpp::where('bpp_no',$id)->with(['bppd' => function($query){
                $query->select(DB::raw("id,id as bppd_id,bpp_id,item_id,format(qty_pesan,2,'en_EN') as qty_pesan,format(qty_terima,2,'en_EN') as qty_terima,harga"));
            }
            ,'bppd.itembarang' => function($query){
                $query->select(DB::raw('id,kode_perk,satuan,CONCAT(kode_perk,"-",kode_barang) as kode_barang, uraian as nama_barang'));
        }])->get()->toArray();
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
                if($key == 'bppd'){
                    $j=0;
                    foreach($value as $f){
                        //print_r($f);
                        foreach($f as $k1=>$v1){
                            if($k1 != 'itembarang')
                                $arrbppd[$i]['datatrans'][$j][$k1] = $v1;
                            else{
                                foreach($v1 as $k2=>$v2){
                                    $arrbppd[$i]['datatrans'][$j][$k2] = $v2;
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
            $bpp = Bpp::where('bpp_no',$id)->first();
            //dd($id);
            Bpp::where('bpp_no',$id)->delete();
            $bpp_id = $bpp->id;
            Bppd::where('bpp_id',$bpp_id)->delete();

            //koreksi stok
            $rev_stock = DB::select("SELECT * FROM `t_invent`
            WHERE ref=:ref and status='OUT' order by id ASC",['ref'=>$bpp->bpp_no]);

            foreach($rev_stock as $st){
                $item_koreksi = (int)$st->used;
                $qty_koreksi = $st->qty;

                $inv = Invent::find($item_koreksi);
                $inv_koreksi['used'] = $inv->used - $qty_koreksi;
                $inv->update($inv_koreksi);
            }

            Invent::where('ref',$id)->delete();
        }, 5);
    }

    public function unposting(Request $request)
    {
        DB::transaction(function () use ($request) {
            $bpp_no = $request->referensi;
            $data_jurnal = Jurnal::where('document',$bpp_no)->where('jenis',5)->first();
            //dd($data_jurnal);
            if(!is_null($data_jurnal)){
                $data['referensi'] = $data_jurnal->referensi;
                $this->deletejurnal($data);
            }
            Bpp::where('bpp_no',$bpp_no)->update(['posting'=>0]);
        }, 5);
    }

    public function bpplist(Request $request){
        $data = json_decode($request->getContent(), true);
        //$data['THN'] = 2021;
        //$data['bppmode'] = "ss";
        $bpp = Bpp::whereYear('tanggal',$data['THN']);

        /* if($data['bppmode']=='posting')
            return Response::json($bpp->where('posting',0)->with('status')->get());

        else */
            return Response::json($bpp->with(['status','guna'])->get());
    }

    public function bppjurnal($ref){
        $jurnal = DB::select("SELECT b.kode_barang,sum(b.qty*b.harga) as kredit,
                        i.kode_perk as kode,r.nama_perk, 0 as debet
                        FROM `t_invent` as b inner join t_item as i on b.item_id = i.id
                        inner join rekening as r on r.kode_perk = i.kode_perk
                        WHERE ref=:ref group by item_id",['ref'=>$ref]);
        //dd($jurnal);
        return Response::json($jurnal);
    }

    public function pdf($id,$generate=0){
        $jurnalH = Bpp::where('bpp_no',$id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpp_id = $jurnalH->id;
        $today = date('d-m-Y');
        $ref = $jurnalH['bpp_no'];
        $jurnalD = Bppd::with('itembarang')->where('bpp_id',$bpp_id)->orderBy('kode_perk')->get();
        //dd($jurnalD);

        if($this->nama_lembaga != '' && $this->logo_lembaga != ''){
            // Custom Header
            PDF::setHeaderCallback(function($pdf) {
                $img_file = K_PATH_IMAGES."/".$this->logo_lembaga;
                //$pdf->Cell(0, 0, 'TEST lOKASI '.$img_file, 1, 1, 'C', 0, '', 0);
                    $pdf->setY(5);
                    $pdf->SetFont('cid0jp', '', 14);
                    $html = '<table cellspacing="0" cellpadding="1" border="0"><tr><td rowspan="3" width="15%"><img src="'.$img_file.'"/></td>
                         <td width="88%">&nbsp;&nbsp;&nbsp;<span style="font-size: 12px;">'.$this->pemkot.'</span></td></tr>
                         <tr><td>&nbsp;&nbsp;&nbsp;<span style="font-size: 14px; font-weight:bold">'.$this->nama_lembaga.'</span></td></tr>
                         <tr><td>&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'.$this->alamat_lembaga.'</span></td></tr></table>';
                    $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
            });
        }
        PDF::setFooterCallback(function($pdf){
            $pdf->SetY(-15);
            // Set font
            $pdf->SetFont('helvetica', 'I', 8);
            // Page number
            $pdf->Cell(0, 10, 'Halaman '.$pdf->getAliasNumPage().' dari '.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        });
        PDF::SetFont('helvetica', '', 8,'', 'false');
        PDF::SetTitle('BUKTI JURNAL)');
        PDF::SetMargins(5,30, 5);
        $auto_page_break = PDF::getAutoPageBreak();
        $modname = get_class($this);

        $html =  view('jurnal.bpp',  compact('jurnalH','ref','jurnalD','modname','jenis'));
        Storage::disk('local')->put('table_bpp.html', $html);
        PDF::AddPage('P','F4');
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('bpp_'.$ref.'.pdf');
    }

    public function koreksi(Request $request)
    {
		//--query mintad--
		$det_bpp = Bppd::orderBy('id','asc')->get();
		$i=0;
        foreach($det_bpp as $bppd) {
			//dd($bppd->bpp_id);
			$bpp_head = Bpp::find($bppd->bpp_id);
			//dd($bpp_head->tgl);
            $stock = DB::select("SELECT id,kode_barang,harga,qty,used,ref,(qty-used) as sisa FROM `t_invent`
                                WHERE qty <> used and status='IN' and kode_barang=:kode order by tgl ASC",['kode'=>$bppd->kode_perk."-".$bppd->kode_barang]);
            //dd($stock);
            $repeat_loop = TRUE;
            $sisa_pakai = $bppd->qty_terima;
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
                $item = Item::where('kode_perk',$bppd->kode_perk)->where('kode_barang',$bppd->kode_barang)->first();
                //dd($item);
				$stock = new Invent();
                $stock->tgl = $bpp_head->tanggal;
                $stock->kode_barang = $bppd->kode_perk."-".$bppd->kode_barang;
                $stock->status = 'OUT';
                $stock->qty = $qty_pakai;
                $stock->harga = $stk->harga;
                $stock->ref = $bpp_head->bpp_no;
                $stock->ref_harga = $stk->ref;
                $stock->used = $stk->id;
                $stock->item_id = $item->id;
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
            //--update harga di permintaan detail--
            $permintaan = Bppd::find($bppd->id);
            $permintaan_upd['harga'] = $str_harga;
            $permintaan->update($permintaan_upd);
            $i++;
        }
        //return redirect('bpp');
    }
}

<?php

namespace App\Http\Controllers;

use App\Jurnal;
use App\Po;
use App\Pod;
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


class PoController extends Controller
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBpb $request)
    {
        $data = json_decode($request->getContent(), true);
        if($data['formdata']['jenis']=='poposting'){
            DB::transaction(function () use ($data) {
                $search  = array('.',',');
                $replace = array('','.');
                $data_trans = array();
                $data_dvud = array();
                $jenis_jurnal = 1;
                $date = Carbon::createFromFormat('Y-m-d', $data['formdata']['tanggal']);
                $ref_dvud = $this->getnumber($jenis_jurnal,$date);
                $total_harga = 0;
                $uraian = $data['formdata']['uraian']." ( ".$data['formdata']['prevReferensi']." )";
                foreach($data['formdata']['datatrans'] as $jurnal){
                    $bpbd_id = $jurnal['bpbd_id'];
                    $arr_barang_perk = explode("_",$jurnal['kode_barang']);
                    $harga = str_replace($search,$replace,$jurnal['harga']);
                    $qty = str_replace($search,$replace,$jurnal['qty_pesan']);
                    $bpbd = Bpbd::find($bpbd_id);
                    $bpbd->qty_pesan = str_replace($search,$replace,$jurnal['qty_pesan']);
                    $bpbd->qty_terima = str_replace($search,$replace,$jurnal['qty_pesan']);
                    $bpbd->harga = str_replace($search,$replace,$jurnal['harga']);
                    $bpbd->save();

                    //--insert kartu stock
                    $item = Item::where('kode_perk',$arr_barang_perk[0])->where('kode_barang',$arr_barang_perk[1])->first();
                    $stock = new Invent();
                    $stock->tgl = $data['formdata']['tanggal'];
                    $stock->kode_barang = $jurnal['kode_barang'];
                    $stock->item_id = $item->id;
                    $stock->status = 'IN';
                    $stock->qty = $qty;
                    $stock->harga = $harga;
                    $stock->ref = $data['formdata']['prevReferensi'];
                    $stock->save();
                    $total_harga = $total_harga + $qty*$harga;
                    $datajurnal = array('kode'=>$arr_barang_perk[0],'referensi'=>$ref_dvud,
                                  'rekanan'=>$data['formdata']['rekanan'],
                                  'uraian'=>$uraian,
                                  'jenis'=>$jenis_jurnal,
                                  'tanggal'=> $data['formdata']['tanggal'],
                                  'opr'=>$data['formdata']['opr'],'debet'=>$qty*$harga,'kredit'=>'0.00');
                    array_push($data_trans,$datajurnal);
                    //[{"id":290,"bpbd_id":"2","bpb_id":"2","item_id":"290","qty_pesan":"100,00","harga":"0.00","catatan":null,"kode_perk":"15.01.10","satuan":"kg","kode_barang":"15.01.10-01001","nama_barang":"Kaporit"}]
                    //{"kode":"12.01.11","referensi":"0002.6.03.21","unit":"0101","rekanan":"","uraian":"tetwet","jenis":6,"tanggal":"2021-03-16","opr":"Pranoto","id":1,"debet":"50.000.000,00","kredit":"0,00"}
                }
                //create dvud
                $coa_utang = Param::where('param_kode','80018')->first();
                $datajurnal = array('kode'=>$coa_utang->param_value,'referensi'=>$ref_dvud,
                                  'rekanan'=>$data['formdata']['rekanan'],
                                  'uraian'=>$uraian,
                                  'jenis'=>$jenis_jurnal,
                                  'tanggal'=> $data['formdata']['tanggal'],
                                  'opr'=>$data['formdata']['opr'],'kredit'=>$total_harga,'debet'=>'0.00');
                array_push($data_trans,$datajurnal);
                $data_dvud['formdata']['datatrans'] = $data_trans;
                $data_dvud['formdata']['isEdit'] = false;
                $data_dvud['formdata']['tanggal'] = $data['formdata']['tanggal'];
                $data_dvud['formdata']['referensi'] = $ref_dvud;
                $data_dvud['formdata']['uraian'] = $uraian;
                $data_dvud['formdata']['unit'] = $data['formdata']['unit'];
                $data_dvud['formdata']['rekanan'] = $data['formdata']['rekanan'];
                $data_dvud['formdata']['jenis'] = $jenis_jurnal;
                $data_dvud['formdata']['opr'] = $data['formdata']['opr'];
                $data_dvud['formdata']['document'] = $data['formdata']['prevReferensi'];
                $this->savejurnal($data_dvud); //trait Jurnaldata
                Bpb::where('bpb_no',$data['formdata']['prevReferensi'])->update(['posting'=>1]);
            }, 5);
        }
        else{
            DB::transaction(function () use ($data) {
                if($data['formdata']['isEdit']) {
                    //delete
                    $bpb = Po::where('po_no',$data['formdata']['prevReferensi'])->first();
                    $bpb_id = $bpb->id;
                    $bpb->delete();
                    Pod::where('po_id',$bpb_id)->delete();
                }
                $search  = array('.',',');
                $replace = array('','.');
                $po_inp = array();
                $po_inp['po_tgl'] = $data['formdata']['tanggal'];
                $po_inp['po_no'] = $data['formdata']['referensi'];
                $po_inp['rekanan'] = $data['formdata']['rekanan'];
                $po_inp['opr'] = $data['formdata']['opr'];
                $po = Po::create($po_inp);
                $data['po_id'] = $po->id;

                foreach($data['formdata']['datatrans'] as $jurnal){
                    $arr_barang_perk = explode("_",$jurnal['kode_barang']);
                    $bpbd_inp = array();
                    $bpbd_inp['po_id'] = $data['po_id'];
                    $bpbd_inp['item_id'] = $jurnal['item_id'];
                    $bpbd_inp['kode_perk'] = $arr_barang_perk[0];
                    $bpbd_inp['kode_barang'] = $arr_barang_perk[1];
                    $bpbd_inp['nama_barang2'] = $jurnal['nama2'];
                    $bpbd_inp['qty_pesan'] = str_replace($search,$replace,$jurnal['qty_pesan']);

                    Pod::create($bpbd_inp);
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
        //return Response::json(Bpb::where('bpb_no',$id)->with(['bpbd.itembarang'])->get());
        $collection = Bpb::where('bpb_no',$id)->with(['bpbd' => function($query){
                $query->select(DB::raw("id,id as bpbd_id,bpb_id,item_id,format(qty_pesan,2,'de_DE') as qty_pesan,harga,catatan"));
            }
            ,'bpbd.itembarang' => function($query){
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
                if($key == 'bpbd'){
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
            $bpp = Bpb::where('bpb_no',$id)->first();
            //dd($id);
            Bpb::where('bpb_no',$id)->delete();
            $bpb_id = $bpp->id;
            Bpbd::where('bpb_id',$bpb_id)->delete();
            Invent::where('ref',$id)->delete();
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
        $jurnalH = Po::where('po_no',$id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpb_id = $jurnalH->id;
        $today = date('d-m-Y');
        $ref = $jurnalH['po_no'];
        $jurnalD = Pod::where('po_id',$bpb_id)->orderBy('kode_perk')->get();

        if($this->nama_lembaga != '' && $this->logo_lembaga != ''){
            // Custom Header
                PDF::setHeaderCallback(function($pdf) {
                    $img_file = K_PATH_IMAGES."/".$this->logo_lembaga;
                    //$pdf->Cell(0, 0, 'TEST lOKASI '.$img_file, 1, 1, 'C', 0, '', 0);
                    if ($pdf->getPage() == 1 || $pdf->getPage() == 2){
                        $pdf->setY(5);
                        $pdf->SetFont('cid0jp', '', 14);
                        $html = '<table cellspacing="0" cellpadding="1" border="0"><tr><td rowspan="2" width="10%"><img src="'.$img_file.'"/></td>
                             <td width="88%">&nbsp;&nbsp;&nbsp;<span style="font-size: 20px; font-weight:bold">'.$this->nama_lembaga.'</span></td></tr>
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

        $html =  view('jurnal.po',  compact('jurnalH','ref','jurnalD','modname','jenis'));
        //Storage::disk('local')->put('table_bpb.html', $html);
         PDF::AddPage('L','F4');
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('bpb_'.$ref.'.pdf');


    }
}

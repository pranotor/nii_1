<?php

namespace App\Http\Controllers;

use App\Jurnal;
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
use App\Pod;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Arr;
use App\Traits\Jurnaldata;
use App\Traits\Refno;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class WoController extends Controller
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
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);

            DB::transaction(function () use ($data) {
                if($data['formdata']['isEdit']) {
                    //delete
                    $bpb = Bpb::where('bpb_no',$data['formdata']['prevReferensi'])->first();
                    $bpb_id = $bpb->id;
                    $bpb->delete();
                    Bpbd::where('bpb_id',$bpb_id)->delete();
                }
                $search  = array('.',',');
                $replace = array('','.');
                $bpb_inp = array();
                $bpb_inp['wo_tgl'] = date('Y-m-d');
				$jenis_jurnal = 'wo';
                $date = Carbon::createFromFormat('Y-m-d', $bpb_inp['wo_tgl']);
                $ref_wo = $this->getnumber($jenis_jurnal,$date);
                $bpb_inp['wo_no'] = $data['formdata']['referensi'];
                $bpb_inp['qt_id'] = $data['formdata']['qt_id'];
                $bpb_inp['opr'] = $data['formdata']['opr'];
                $bpb = Wo::create($bpb_inp);
                $data['wo_id'] = $bpb->id;

                foreach($data['formdata']['datatransprod'] as $jurnal){
                    $bpbd_inp = array();
                    $bpbd_inp['wo_id'] = $data['wo_id'];
                    $bpbd_inp['item_id'] = $jurnal['item_id'];
                    $bpbd_inp['kode_perk'] = $jurnal['kode_perk'];
					$arr_barang_perk = explode("_",$jurnal['kode_barang']);
                    $bpbd_inp['kode_barang'] = $arr_barang_perk[1];
                    $qty = str_replace($search,$replace,$jurnal['qty_pesan']);
                    $bpbd_inp['qty'] = str_replace($search,$replace,$jurnal['qty_pesan']);

                    Wod::create($bpbd_inp);
                }

				foreach($data['formdata']['selected'] as $jurnal){
                    $quod_id = $jurnal['id'];
					Quod::where('id',$quod_id)->update(['wo_id'=>$data['wo_id']]);
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

	public function wolist(Request $request){
        $data = json_decode($request->getContent(), true);
        //$bpb = Wo::whereYear('wo_tgl',$data['THN']);
        $bpb = Wo::with('quotation','quotation.qcustomer');
        return Response::json($bpb->get());
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

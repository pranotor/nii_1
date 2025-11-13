<?php

namespace App\Http\Controllers;

use App\Fp;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreBpb;
use App\Http\Requests\StorePo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class NomorFpController extends Controller
{


    public function __construct()
    {
		//
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Fp::get());
    }

    public function list_available()
    {
        return Response::json(Fp::select(DB::raw('kode_depan,no_fp,concat(kode_depan,".",no_fp) as no_seri'))->where('status',0)->orderBy('no_fp')->get());
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
            $kode_depan = $data['formdata']['kode_depan'];
            $no_awal = $data['formdata']['no_awal'];
            $no_akhir = $data['formdata']['no_akhir'];
            for($i=$no_awal;$i<=$no_akhir;$i++){
                $fp_data['no_fp'] = $i;
                $fp_data['kode_depan'] = $kode_depan;
                Fp::create($fp_data);
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

    public function getPO($id){
        $jurnalH = Po::where('po_no',$id)->first();
        //dd($jurnalH);
        $jenis = 0;
        $bpb_id = $jurnalH->id;
        $today = date('d-m-Y');
        $ref = $jurnalH['po_no'];
        $jurnalD = Pod::select(DB::raw("id,id as pod_id,po_id,item_id,format(qty_pesan-tot_kirim,2,'de_DE') as qty_pesan,format(harga,2,'de_DE') as harga"))
                    ->with('itembarang')->where('po_id',$bpb_id)->orderBy('kode_perk')->get();
        return Response::json($jurnalD);
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

        $html =  view('jurnal.po',  compact('jurnalH','ref','jurnalD','modname','jenis'));
        //Storage::disk('local')->put('table_bpb.html', $html);
         PDF::AddPage('p','a4');
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('bpb_'.$ref.'.pdf');


    }
}

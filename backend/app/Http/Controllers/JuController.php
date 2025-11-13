<?php

namespace App\Http\Controllers;

use App\Jurnal;
use App\ViewJurnal;
use App\Param;
use App\Tandatangan;
use App\Dvud;
use App\Voucherbayar;
use App\Drd;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreJurnal;
use Illuminate\Support\Facades\Auth;
use App\Traits\Jurnaldata;
use PDF;


class JuController extends Controller
{
    var $username = '';
    use Jurnaldata;
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
                case 'PRINT VOUCHER':
                    $this->voucher = $lbg->param_value;
                    break;
                case 'PRINT KUITANSI':
                    $this->kuitansi = $lbg->param_value;
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
        //filter jenis dan tahun

        return Response::json(ViewJurnal::get()->orderBy('tanggal'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJurnal $request)
    {
        
        $data = json_decode($request->getContent(), true);
        $this->savejurnal($data); //trait Jurnaldata
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Jurnal  $jurnal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Response::json(Jurnal::where('referensi',$id)->get());
    }

    public function jurnallist(Request $request){
        $data = json_decode($request->getContent(), true);
        //print_r($data);
        //return Response::json(ViewJurnal::whereYear('tanggal',$data['THN'])->where('jenis',$data['jenis'])->get());
    
        $pagenum = $data['pagenum'];
	    $pagesize = $data['pagesize'];
	    $start = $pagenum * $pagesize;
        //dd($start);
       
	    $query_count = ViewJurnal::whereYear('tanggal',$data['THN'])->where('jenis',$data['jenis']);
        $query = ViewJurnal::whereYear('tanggal',$data['THN'])->where('jenis',$data['jenis'])->orderBy('tanggal','desc')->offset($start)->limit($pagesize); 
          
        
	// filter data.
        if (isset($data['filterscount']))
        {
            $filterscount = $data['filterscount'];
            
            if ($filterscount > 0)
            {
                //$where = " WHERE (";
                $where = " (";
                $tmpdatafield = "";
                $tmpfilteroperator = "";
                for ($i=0; $i < $filterscount; $i++)
                {
                    // get the filter's value.
                    $filtervalue = $data["filtervalue" . $i];
                    // get the filter's condition.
                    $filtercondition = $data["filtercondition" . $i];
                    // get the filter's column.
                    $filterdatafield = $data["filterdatafield" . $i];
                    // get the filter's operator.
                    $filteroperator = $data["filteroperator" . $i];
                    
                    if ($tmpdatafield == "")
                    {
                        $tmpdatafield = $filterdatafield;			
                    }
                    else if ($tmpdatafield <> $filterdatafield)
                    {
                        $where .= ")AND(";
                    }
                    else if ($tmpdatafield == $filterdatafield)
                    {
                        if ($tmpfilteroperator == 0)
                        {
                            $where .= " AND ";
                        }
                        else $where .= " OR ";	
                    }

                    if($filterdatafield=='tanggal'){
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $filtervalue);
                        $filtervalue = $date->format('Y-m-d');
                    }
                    
                    // build the "WHERE" clause depending on the filter's condition, value and datafield.
                    switch($filtercondition)
                    {
                        case "CONTAINS":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";
                            break;
                        case "DOES_NOT_CONTAIN":
                            $where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
                            break;
                        case "EQUAL":
                            $where .= " " . $filterdatafield . " = '" . $filtervalue ."'";
                            break;
                        case "NOT_EQUAL":
                            $where .= " " . $filterdatafield . " <> '" . $filtervalue ."'";
                            break;
                        case "GREATER_THAN":
                            $where .= " " . $filterdatafield . " > '" . $filtervalue ."'";
                            break;
                        case "LESS_THAN":
                            $where .= " " . $filterdatafield . " < '" . $filtervalue ."'";
                            break;
                        case "GREATER_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " >= '" . $filtervalue ."'";
                            break;
                        case "LESS_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " <= '" . $filtervalue ."'";
                            break;
                        case "STARTS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
                            break;
                        case "ENDS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
                            break;
                    }
                                    
                    if ($i == $filterscount - 1)
                    {
                        $where .= ")";
                    }
                    
                    $tmpfilteroperator = $filteroperator;
                    $tmpdatafield = $filterdatafield;			
                }
                // build the query.
                //$q_count = DB::select($query)->count();
                //dd($q_count);
                /* $query = "SELECT t.asset_id,t.kode,t.kode_asset,t.uraian,t.harga_beli,t.nilai_buku,r.nama_perk FROM t_asset as t 
                            INNER JOIN rekening as r ON t.kode = r.kode_perk ".$where." LIMIT $start, $pagesize";	 */	
                $query_count = ViewJurnal::whereYear('tanggal',$data['THN'])->where('jenis',$data['jenis'])->whereRaw($where);
                $query = ViewJurnal::whereYear('tanggal',$data['THN'])->where('jenis',$data['jenis'])->whereRaw($where)->orderBy('tanggal','desc')->offset($start)->limit($pagesize);                	
            }
        }
	
        $result = $query->get();
        $total_rows = $query_count->count();
        //dd($total_rows);
        $orders = null;
	    // get data and store in a json array

    foreach($result as $row){
        $orders[] = array(
			'tanggal' => $row->tanggal,
			'referensi' => $row->referensi,
			'uraian' => $row->uraian,
			'debet' => $row->debet,
			'kredit' => $row->kredit,
			'opr' => $row->opr,
			'jenis' => $row->jenis,
			'voucher' => $row->voucher,
			'document' => $row->document
		  );
    }
    if($total_rows){
        $data= array(
            'TotalRows' => $total_rows,
            'Rows' => $orders
         );
    }
    else
        $data = [];
	return json_encode($data);
    
    }

    public function jurnaldelete(Request $request){
        $data = json_decode($request->getContent(), true);
        $this->deletejurnal($data);//trait Jurnaldata
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

    public function pdf($id,$generate=0){
        $jurnalH = Jurnal::where('referensi',$id)->first();
        $jenis = $jurnalH['jenis'];
        $today = date('d-m-Y');
        $ref = $jurnalH['referensi'];
        //$jurnalD = Jurnal::where('referensi',$id)->groupBy('kode')->orderBy('debet','desc')->orderBy('kode')->get();
        $jurnalD = Jurnal::select('kode', DB::raw('SUM(debet) as debet, SUM(kredit) as kredit'))->where('referensi',$ref)->groupBy('kode')->orderBy('kode','asc')->get();
       
        
        if($this->nama_lembaga != '' && $this->logo_lembaga != ''){
            // Custom Header
                PDF::setHeaderCallback(function($pdf) {
                    $img_file = K_PATH_IMAGES."/".$this->logo_lembaga;
                    //$pdf->Cell(0, 0, 'TEST lOKASI '.$img_file, 1, 1, 'C', 0, '', 0);
                    //if ($pdf->getPage() == 1 || $pdf->getPage() == 2){
                    if ($pdf->getPage() <= $this->voucher){
                        $pdf->setY(5);
                        $pdf->SetFont('cid0jp', '', 14);
                        $html = '<table cellspacing="0" cellpadding="1" border="0"><tr><td rowspan="3" width="15%"><img src="'.$img_file.'"/></td>
                             <td width="88%">&nbsp;&nbsp;&nbsp;<span style="font-size: 12px;">'.$this->pemkot.'</span></td></tr>
                             <tr><td>&nbsp;&nbsp;&nbsp;<span style="font-size: 14px; font-weight:bold">'.$this->nama_lembaga.'</span></td></tr>
                             <tr><td>&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'.$this->alamat_lembaga.'</span></td></tr></table>';
                        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
                    }
                });
        }
        PDF::SetFont('cid0jp', '', 8,'', 'false');
        PDF::SetTitle('BUKTI JURNAL)');
        PDF::SetMargins(5,28, 5,true);
        PDF::SetAutoPageBreak(TRUE, 0);
        $auto_page_break = PDF::getAutoPageBreak();
        $modname = get_class($this);
        if($jenis == 1){
            $bayar = Voucherbayar::where('no_vcr',$ref)->with(['jbk' => function($q){
                $q->where('kredit','<>','0')->with('coa');
            }])->get();
            $str_cek = $str_tgl = $str_kode = '';
            //dd($bayar);
            if(!empty($bayar)){
                $arr_cek = array();
                $arr_tgl = array();
                $arr_kode = array();
                foreach($bayar as $row_bayar){
                    array_push($arr_cek,$row_bayar->no_cheq);
                    array_push($arr_tgl,\Carbon\Carbon::parse($row_bayar->tgl_bayar)->translatedFormat('j F Y'));
                    foreach($row_bayar->jbk as $row_jbk){
                        array_push($arr_kode,$row_jbk->coa->nama_perk);
                    }
                }
                /* $str_cek = implode(", ",$arr_cek);
                $str_tgl = implode(", ",$arr_tgl);
                $str_kode = implode(", ",$arr_kode); */
            }
            $html =  view('jurnal.dvud',  compact('jurnalH','ref','jurnalD','modname','jenis','str_cek','str_tgl','str_kode','arr_cek','arr_tgl','arr_kode'));
            $ALAMAT = $this->alamat_lembaga;
            $LEMBAGA = $this->nama_lembaga;
            $LOGO = $this->logo_lembaga;
            $KOTKAB = $this->kotkab;
            $PEMKOT = $this->pemkot;
            $kuitansi =  view('jurnal.kuitansi',  compact('jurnalH','ref','jurnalD','LEMBAGA','ALAMAT','LOGO','PEMKOT','KOTKAB','modname','jenis'));
            
            // disable auto-page-break
            for($i=1;$i<=$this->voucher;$i++){
                PDF::AddPage('P','F4');
                PDF::writeHTML($html, true, false, true, false, '');
            }
            /* PDF::AddPage('P','F4');
            PDF::writeHTML($html, true, false, true, false, '');
            PDF::AddPage('P','F4');
            PDF::writeHTML($html, true, false, true, false, '');  */

            PDF::SetMargins(2,10, 5,true);
            PDF::AddPage('P','F4');
            for($i=1;$i<=$this->kuitansi;$i++){
                PDF::writeHTML($kuitansi, true, false, true, false, '');
            }
            
            /* PDF::writeHTML($kuitansi, true, false, true, false, '');
            PDF::writeHTML($kuitansi, true, false, true, false, ''); */
            PDF::Output('voucher_'.$ref.'.pdf');
        }
        else{
            $html =  view('jurnal.general',  compact('jurnalH','ref','jurnalD','modname','jenis'));
            PDF::AddPage('P','F4');
            PDF::writeHTML($html, true, false, true, false, '');
            PDF::Output('jurnal_'.$ref.'.pdf');
        }    
        
        
    }
    
}

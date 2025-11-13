<?php

namespace App\Http\Controllers;

use App\Aktiva;
use App\Rekaktiva;
use App\AktivaTrans;
use App\AktivaSusut;
use App\Param;
use App\Perkiraan;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreAktiva;
use App\Traits\Jurnaldata;
use App\Traits\Refno;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Arr;
use Carbon\Carbon;


class AktivaController extends Controller
{
    use Jurnaldata;
    use Refno;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $pagenum = $data['pagenum'];
	    $pagesize = $data['pagesize'];
	    $start = $pagenum * $pagesize;
        //dd($start);
       
	   /*  $query = "SELECT t.asset_id,t.kode,t.kode_asset,t.uraian,t.harga_beli,t.nilai_buku,r.nama_perk FROM t_asset as t 
                  INNER JOIN rekening as r ON t.kode = r.kode_perk LIMIT $start, $pagesize"; */
        $query_count = DB::table('t_asset as a')->join('rekening as r','r.kode_perk','=','a.kode')->where('is_aktiva',1);
        $query = DB::table('t_asset as a')->join('rekening as r','r.kode_perk','=','a.kode')->where('is_aktiva',1)->where('is_deleted',0)->offset($start)->limit($pagesize); 
          
        
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
                $query_count = DB::table('t_asset as a')->join('rekening as r','r.kode_perk','=','a.kode')->where('is_aktiva',1)->whereRaw($where);
                $query = DB::table('t_asset as a')->join('rekening as r','r.kode_perk','=','a.kode')->where('is_aktiva',1)->whereRaw($where)->offset($start)->limit($pagesize);                	
            }
        }
	
        $result = $query->get();
        $total_rows = $query_count->count();
        //dd($total_rows);
        $orders = null;
	    // get data and store in a json array

    foreach($result as $row){
        $orders[] = array(
			'asset_id' => $row->asset_id,
			'kode' => $row->kode,
			'kode_asset' => $row->kode_asset,
			'uraian' => $row->uraian,
			'harga_beli' => $row->harga_beli,
			'nilai_buku' => $row->nilai_buku,
			'nama_perk' => $row->nama_perk
		  );
    }
    $data= array(
       'TotalRows' => $total_rows,
	   'Rows' => $orders
	);
	return json_encode($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAktiva $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $search  = array('.',',');
            $replace = array('','.');
            $data_inp = array();
            $tanggal = Carbon::createFromFormat('d-m-Y', $data['formdata']['tanggal']);
            $tgl_peroleh = $tanggal->format('Y-m-d');
            $tgl_habis = $tanggal->addYears($data['formdata']['masa']);
            $data_inp['tanggal'] = $tgl_peroleh;
            $data_inp['nmr_bukti'] = $data['formdata']['nmr_bukti'];
            $data_inp['kode'] = $data['formdata']['kode'];
            $data_inp['kode_asset'] = $data['formdata']['kode_asset'];
            $data_inp['uraian'] = $data['formdata']['uraian'];
            $data_inp['satuan'] = $data['formdata']['satuan'];
            $data_inp['jumlah'] = str_replace($search,$replace,$data['formdata']['jumlah']);
            $data_inp['harga_unit'] = str_replace($search,$replace,$data['formdata']['harga_unit']);
            $data_inp['harga_beli'] = str_replace($search,$replace,$data['formdata']['harga_beli']);
            $data_inp['nilai_buku'] = str_replace($search,$replace,$data['formdata']['harga_beli']);
            $data_inp['gol'] = $data['formdata']['gol'];
            //kayanya harus ambil dari table tarif
            $data_inp['tarif'] = $data['formdata']['tarif']/100;
            $data_inp['masa'] = $data['formdata']['masa'];
            $data_inp['tgl_habis'] = $tgl_habis->format('Y-m-d');
            $data_inp['is_aktiva'] = 1;
            
            //$data_inp['opr'] = $data['formdata']['opr'];
            if($data['formdata']['isEdit']) {
                Aktiva::where('asset_id',$data['formdata']['prevReferensi'])->update($data_inp);
            }
            else
                $aktiva = Aktiva::create($data_inp);
            
        }, 5);  
    }

    public function koreksi(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $search  = array('.',',');
            $replace = array('','.');
            $data_inpA = array();
            $data_inp = array();
            $tanggal = Carbon::createFromFormat('d-m-Y', $data['formdata']['tgl_perubahan']);
            $asset_id = $data['formdata']['asset_id'];
            $harga_sebelum = str_replace($search,$replace,$data['formdata']['harga_sebelum']);
            $harga_perubahan = str_replace($search,$replace,$data['formdata']['harga_perubahan']);
            $nilai_perubahan = str_replace($search,$replace,$data['formdata']['nilai_perubahan']);

            $aktiva = DB::table('t_asset as a')
                       ->where('is_aktiva',1)
                       ->where('asset_id',$asset_id)
                       ->join('t_tarifsusut as t', 'a.gol','=','t.gol' )
                       ->select('a.posting','a.tanggal','a.kode','a.kode_asset','a.asset_id','a.tgl_habis','a.harga_beli','a.nilai_buku','a.akum_susut','a.gol','a.masa','t.tarif','t.jenis')
                       ->first();
            //dd($aktiva);           
            if(!$aktiva->posting){
                abort(423,'Aktiva belum pernah disusutkan, silahkan langsung edit harga...');
            }
            if($harga_perubahan > $harga_sebelum){ //koreksi naik
                $data_inpA['naik'] = $nilai_perubahan;
                $data_inpA['tgl_naik'] = $tanggal;
                $data_inpA['koreksi'] = 1;
                $kode_trans = 400;
            }

            elseif($harga_perubahan < $harga_sebelum){
                $data_inpA['turun'] = $nilai_perubahan;
                $data_inpA['tgl_turun'] = $tanggal;
                $data_inpA['koreksi'] = 1;
                $kode_trans = 500;
            }

            //MAX BULAN
            $sql_max = AktivaTrans::where('asset_id',$asset_id)->orderBy('id','desc')->limit(1)->first();
            if(isset($sql_max->tanggal))
                $tgl_max = $sql_max->tanggal;
            else
                $tgl_max = $tanggal;
            //AktivaTrans::where('asset_id',$asset_id)->where('kode_trans',300)->update(['kode_trans'=>400]);

            $tgl_peroleh = $aktiva->tanggal;
            $tgl_habis = $aktiva->tgl_habis;
            $kode_asset = $aktiva->kode_asset;
            $kode = $aktiva->kode;
            $masa = $aktiva->masa;
            $jenis = $aktiva->jenis;
            $tarif = ($aktiva->tarif)/100;
            $harga_peroleh = $harga_perubahan;
            $harga_beli = $harga_perubahan;
            $harga_beli_lalu = $aktiva->harga_beli;
            $nilai_buku_lalu = $aktiva->nilai_buku;
            $akum_susut_lalu = $aktiva->akum_susut;
            //--perulangan masa tahun
            $tgl = date("Y-m-t", strtotime($tgl_peroleh));
            $tgl_akhir = date("Y-m-t", strtotime($tgl_habis));
            $akum_susut = 0;
            $biaya_susut = 0;
            switch ($jenis) {
                case 'SLN': 
                    while($tgl <= $tgl_akhir && $tgl <= $tgl_max){
                        $bulan_susut = Carbon::parse($tgl)->format('m');
                        $tahun_susut = Carbon::parse($tgl)->format('Y');
                        $biaya_susut = 1/12*$tarif*$harga_beli;
                        $akum_susut = $akum_susut + $biaya_susut;
                        $data_inp['tanggal'] = $tgl;
                        $data_inp['tahun'] = $tahun_susut;
                        $data_inp['bulan'] = $bulan_susut;
                        $data_inp['asset_id'] = $asset_id;
                        $data_inp['kode'] = $kode;
                        $data_inp['kode_asset'] = $kode_asset;
                        $data_inp['harga_perolehan'] = $harga_peroleh;
                        $data_inp['beban_susut'] = $biaya_susut;
                        $data_inp['akum_susut'] = $akum_susut;
                        $data_inp['nilai_trans'] = $biaya_susut;
                        $data_inp['kode_trans'] = 400;
                        AktivaTrans::create($data_inp);
                        
                        $harga_peroleh = $harga_peroleh - $biaya_susut;
                        if($harga_peroleh < 2)
                            break;
                        $date = date_create($tgl);
                        date_add($date, date_interval_create_from_date_string('1 day'));
                        $tgl = date_format($date, 'Y-m-t');
                    }
                    break;
                case 'DB':
                    while($tgl <= $tgl_akhir && $tgl <= $tgl_max){
                        $bulan_susut = Carbon::parse($tgl)->format('m');
                        $tahun_susut = Carbon::parse($tgl)->format('Y');
                        $biaya_susut = 1/12*$tarif*$harga_peroleh;
                        $akum_susut = $akum_susut + $biaya_susut;
                        $data_inp['tanggal'] = $tgl;
                        $data_inp['tahun'] = $tahun_susut;
                        $data_inp['bulan'] = $bulan_susut;
                        $data_inp['asset_id'] = $asset_id;
                        $data_inp['kode'] = $kode;
                        $data_inp['kode_asset'] = $kode_asset;
                        $data_inp['harga_perolehan'] = $harga_peroleh;
                        $data_inp['beban_susut'] = $biaya_susut;
                        $data_inp['akum_susut'] = $akum_susut;
                        $data_inp['nilai_trans'] = $biaya_susut;
                        $data_inp['kode_trans'] = 400;
                        AktivaTrans::create($data_inp);

                        $harga_peroleh = $harga_peroleh - $biaya_susut;
                        if($harga_peroleh < 2)
                            break;
                        $date = date_create($tgl);
                        date_add($date, date_interval_create_from_date_string('1 day'));
                        $tgl = date_format($date, 'Y-m-t');
                    }
                    break;
            }
            $data_inpA['nilai_buku_lalu'] = $nilai_buku_lalu;
            $data_inpA['akum_susut_lalu'] = $akum_susut_lalu;
            $data_inpA['nilai_buku'] = $harga_peroleh;
            $data_inpA['akum_susut'] = $akum_susut;
            $data_inpA['susut_ini'] = $biaya_susut;
            Aktiva::where('asset_id',$asset_id)->update($data_inpA);
        }, 5);  
    }

    public function hapus(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $search  = array('.',',');
            $replace = array('','.');
            $data_inpA = array();
            $data_inp = array();
            $tanggal = Carbon::createFromFormat('d-m-Y', $data['formdata']['tgl_perubahan']);
            $asset_id = $data['formdata']['asset_id'];
            $alasan_hapus = $data['formdata']['uraian'];

            $aktiva = DB::table('t_asset as a')
                       ->where('is_aktiva',1)
                       ->where('asset_id',$asset_id)
                       ->join('t_tarifsusut as t', 'a.gol','=','t.gol' )
                       ->select('a.posting','a.tanggal','a.kode','a.kode_asset','a.asset_id','a.tgl_habis','a.harga_beli','a.nilai_buku','a.akum_susut','a.gol','a.masa','t.tarif','t.jenis')
                       ->first();
            //dd($aktiva);           
            if(!$aktiva->posting){
                abort(423,'Aktiva belum pernah disusutkan, silahkan langsung edit harga...');
            }
          
            $kode = $aktiva->kode;
            
            $harga_beli = $aktiva->harga_beli;
            $nilai_buku_lalu = $aktiva->nilai_buku;
            $akum_susut_lalu = $aktiva->akum_susut;
            $nilai_perolehan = $nilai_buku_lalu + $akum_susut_lalu;
            $data_trans = array();
            
            $data_inpA['is_deleted'] = 1;
            $data_inpA['alasan_hapus'] = $alasan_hapus;
            $data_inpA['tgl_hapus'] = $tanggal->format('Y-m-d');
            $data_inpA['turun'] = $harga_beli;
            $data_inpA['tgl_turun'] = $tanggal->format('Y-m-d');
            $data_inpA['koreksi'] = 1;
            $data_inpA['nilai_buku_lalu'] = $nilai_buku_lalu;
            $data_inpA['akum_susut_lalu'] = $akum_susut_lalu;
            /* $data_inpA['nilai_buku'] = $harga_peroleh;
            $data_inpA['akum_susut'] = $akum_susut;
            $data_inpA['susut_ini'] = $biaya_susut; */
            Aktiva::where('asset_id',$asset_id)->update($data_inpA);

            $rekaset = Rekaktiva::where('kodea',$kode)->first();
            $coa_biaya = Param::where('param_kode','80019')->first();
            $jenis_jurnal = 6;
            //$date = Carbon::createFromFormat('Y-m-d', $data['formdata']['tanggal']);
            $rev_jurnal = $this->getnumber($jenis_jurnal,$tanggal);
            $uraian = "Jurnal Penghapusan ".$rekaset->namaa;
            $datajurnal = array('kode'=>$rekaset->kodep,'referensi'=>$rev_jurnal,
                        'uraian'=>$uraian,
                        'jenis'=> $jenis_jurnal,
                        'tanggal'=> $tanggal->format('Y-m-d'),
                        'opr'=>'-','debet'=>str_replace('.',',',$akum_susut_lalu),'kredit'=>'0.00');
            array_push($data_trans,$datajurnal);

            $datajurnal = array('kode'=>$coa_biaya->param_value,'referensi'=>$rev_jurnal,
                        'uraian'=>$uraian,
                        'jenis'=> $jenis_jurnal,
                        'tanggal'=> $tanggal->format('Y-m-d'),
                        'opr'=>'-','debet'=>str_replace('.',',',$nilai_buku_lalu),'kredit'=>'0.00');
            array_push($data_trans,$datajurnal);

            $datajurnal = array('kode'=>$rekaset->kodea,'referensi'=>$rev_jurnal,
                        'uraian'=>$uraian,
                        'jenis'=> $jenis_jurnal,
                        'tanggal'=> $tanggal->format('Y-m-d'),
                        'opr'=>'-','kredit'=>str_replace('.',',',$nilai_perolehan),'debet'=>'0.00');
            array_push($data_trans,$datajurnal);
            
            $data_dvud['formdata']['datatrans'] = $data_trans;
            $data_dvud['formdata']['isEdit'] = false;
            $data_dvud['formdata']['tanggal'] = $tanggal->format('Y-m-d');
            $data_dvud['formdata']['referensi'] = $rev_jurnal;
            $data_dvud['formdata']['uraian'] = $uraian;
            $data_dvud['formdata']['unit'] = '-';
            $data_dvud['formdata']['rekanan'] = '-';
            $data_dvud['formdata']['jenis'] = $jenis_jurnal;
            $data_dvud['formdata']['opr'] = '-';
            $this->savejurnal($data_dvud); //trait Jurnaldata  
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
        $aktiva = Aktiva::select(DB::raw("asset_id,tanggal,kode,kode_asset,uraian,nmr_bukti,posting,
                    gol,masa,tarif,satuan,format(jumlah,2,'de_DE') as jumlah,
                    format(harga_unit,2,'de_DE') as harga_unit,format(harga_beli,2,'de_DE') as harga_beli,format(nilai_buku,2,'de_DE') as nilai_buku"))
                    ->where('asset_id',$id)
                    ->first();
        return Response::json($aktiva);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function aktivalist(Request $request){
        $data = json_decode($request->getContent(), true);
        //print_r($data);
        return Response::json(Aktiva::with('kodeperk')->get());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dvud  $dvud
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dvud $dvud)
    {
        //
    }

    public function susutlist(Request $request){
        $data = json_decode($request->getContent(), true);
        //print_r($data);
        return Response::json(AktivaSusut::where('tahun',$data['THN'])->get());
    }

    public function susutbatal(Request $request){
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $tahun = $data['tahun'];
            $bulan = $data['bulan'];
            $referensi = $data['ref'];

            if($bulan == 1){
                $tahun_sebelum = $tahun - 1;
                $bulan_sebelum = 12;
            }
            else{
                $tahun_sebelum = $tahun;
                $bulan_sebelum = $bulan - 1;
            }
            //check apakah ini proses terakhir 
            $sql_check = AktivaSusut::where('tahun',$tahun)->max('bulan');
            if($bulan < (int)$sql_check)
                abort(422,'Hanya proses terakhir yang bisa dibatalkan...');

            //hapus asset_trans, asset_susut, jurnal
            AktivaTrans::where('kode_trans',300)->where('tahun',$tahun)->where('bulan',$bulan)->delete();
            AktivaSusut::where('tahun',$tahun)->where('bulan',$bulan)->delete();
            //posting = 0 untuk tgl_peroleh = bulan batal
            Aktiva::whereYear('tanggal',$tahun)->whereMonth('tanggal',$bulan)->update(['posting'=>0]);
            $data_del['referensi'] = $referensi;
            $this->deletejurnal($data_del);
            $sql_update = "UPDATE t_asset a INNER JOIN t_asset_trans t ON a.`asset_id` = t.`asset_id` 
                        AND t.`tahun`=".$tahun_sebelum." AND t.`bulan`=".$bulan_sebelum." 
                        SET a.`nilai_buku` = IFNULL((t.`harga_perolehan`-t.`beban_susut`),0), 
                        a.`akum_susut` = IFNULL(t.`akum_susut`,0), a.`susut_ini`= IFNULL(t.`beban_susut`,0)";
            DB::unprepared($sql_update);

            $sql_update = "UPDATE t_asset a SET a.`nilai_buku` = (a.harga_beli + a.naik - a.turun), 
                        a.`akum_susut` = 0, a.`susut_ini`= 0
                        WHERE gol <> 'td' AND asset_id NOT IN (SELECT DISTINCT asset_id FROM t_asset_trans)";
            DB::unprepared($sql_update);

        }, 5);
    }

    public function susutproses(Request $request){  //hitung sampai bulan februari
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $tgl_awal = $data['formdata']['bulan']."-01";
            $date = Carbon::createFromFormat('Y-m-d', $tgl_awal);
            $tgl_akhir_bulan = $date->format('Y-m-t');
		    $q_asset = DB::table('t_asset as a')
                       ->where('is_aktiva',1)
                       ->where('a.gol','<>','td')->orderBy('asset_id')
                       ->where('tgl_habis','>=',$tgl_awal)
                       ->where('tanggal','<=',$tgl_akhir_bulan)
                       ->where('nilai_buku','>',1)
                       ->where(function($query) use ($tgl_awal) {
                            $query->where('tgl_hapus','>=',$tgl_awal)
                                  ->orWhere('tgl_hapus', '1945-08-17');
                        })
                       ->join('t_tarifsusut as t', 'a.gol','=','t.gol' )
                       ->select('a.tanggal','a.kode','a.kode_asset','a.asset_id','a.tgl_habis','a.harga_beli','a.nilai_buku','a.akum_susut','a.gol','a.masa','t.tarif','t.jenis')
                       ->get();
            //dd($q_asset);
            $tgl_susut = Carbon::parse($tgl_awal)->format('Y-m-t');
            $bulan_susut = Carbon::parse($tgl_awal)->format('m');
            $tahun_susut = Carbon::parse($tgl_awal)->format('Y');
            $str_periode = Carbon::parse($tgl_awal)->translatedFormat('F Y');
            $tahunkecil_susut = Carbon::parse($tgl_awal)->format('y');
            $rev_jurnal = "0001.8.".str_pad($bulan_susut, 2, "0", STR_PAD_LEFT).".".$tahunkecil_susut;
            $data_trans = array();
            foreach($q_asset as $as){
                $asset_id = $as->asset_id;
                $kode_asset = $as->kode_asset;
                $kode = $as->kode;
                $jenis = $as->jenis;
                $tarif = ($as->tarif)/100;
                $harga_peroleh = $as->nilai_buku;
                $harga_beli = $as->harga_beli;
                $akum_susut = $as->akum_susut;
                
                $biaya_susut = 0;
                switch ($jenis) {
                    case 'SLN': 
                        //cek apakah akhir masa pakai..?
                        $asset_tgl_habis = Carbon::createFromFormat('Y-m-d', $as->tgl_habis);
                        $asset_tgl_akhir = $asset_tgl_habis->format('Y-m-t');
                        if($asset_tgl_akhir == $tgl_akhir_bulan)
                            $biaya_susut = $harga_peroleh-1;
                        else
                            $biaya_susut = 1/12*$tarif*$harga_beli;

                        if($as->nilai_buku < $biaya_susut)
                            $biaya_susut = $harga_peroleh-1;

                        $akum_susut = $akum_susut + $biaya_susut;
                        break;
                    case 'DB':
                        //cek apakah akhir masa pakai..?
                        $asset_tgl_habis = Carbon::createFromFormat('Y-m-d', $as->tgl_habis);
                        $asset_tgl_akhir = $asset_tgl_habis->format('Y-m-t');
                        if($asset_tgl_akhir == $tgl_akhir_bulan)
                            $biaya_susut = $harga_peroleh-1;
                        else
                            $biaya_susut = 1/12*$tarif*$harga_peroleh;
                        $akum_susut = $akum_susut + $biaya_susut;
                        break;
                }
                $data_inp['tanggal'] = $tgl_susut;
                $data_inp['tahun'] = $tahun_susut;
                $data_inp['bulan'] = $bulan_susut;
                $data_inp['asset_id'] = $asset_id;
                $data_inp['kode'] = $kode;
                $data_inp['kode_asset'] = $kode_asset;
                $data_inp['harga_perolehan'] = $harga_peroleh;
                $data_inp['beban_susut'] = $biaya_susut;
                $data_inp['akum_susut'] = $akum_susut;
                $data_inp['nilai_trans'] = $biaya_susut;
                $data_inp['kode_trans'] = 300;
                AktivaTrans::create($data_inp);

                $harga_peroleh = $harga_peroleh - $biaya_susut;

                //update nilai buku, akum susut, susut ini t_asset
                $data_update['nilai_buku'] = $harga_peroleh;
                $data_update['akum_susut'] = $akum_susut; 
                $data_update['susut_ini'] = $biaya_susut;
                $data_update['posting'] = 1;
                Aktiva::find($asset_id)->update($data_update);
            }
            
            $inp_susut['tgl_trans'] = Carbon::now()->format('Y-m-d');
            $inp_susut['tahun'] = $tahun_susut;
            $inp_susut['bulan'] = $bulan_susut;
            $inp_susut['referensi'] = $rev_jurnal;
            AktivaSusut::create($inp_susut);

            $q_biaya = DB::select('SELECT t.kode,r.`kodea`,r.`kodep`,r.`kodeb`,
                            FORMAT(SUM(beban_susut),2,"de_DE") AS susut FROM t_asset_trans AS t
                            INNER JOIN rekaset AS r ON t.`kode` = r.`kodea`
                            WHERE tahun='.$tahun_susut.' AND bulan='.$bulan_susut.' GROUP BY kode');
            
            $uraian = "Jurnal Penyusutan Asset Tetap Periode ".$str_periode;

            foreach($q_biaya as $biaya){
                $datajurnal = array('kode'=>$biaya->kodeb,'referensi'=>$rev_jurnal,
                            'uraian'=>$uraian,
                            'jenis'=>'8',
                            'tanggal'=> $tgl_susut,
                            'opr'=>'-','debet'=>$biaya->susut,'kredit'=>'0.00');
                array_push($data_trans,$datajurnal);

                $datajurnal = array('kode'=>$biaya->kodep,'referensi'=>$rev_jurnal,
                            'uraian'=>$uraian,
                            'jenis'=>'8',
                            'tanggal'=> $tgl_susut,
                            'opr'=>'-','kredit'=>$biaya->susut,'debet'=>'0.00');
                array_push($data_trans,$datajurnal);
            }
            
            $data_dvud['formdata']['datatrans'] = $data_trans;
            $data_dvud['formdata']['isEdit'] = false;
            $data_dvud['formdata']['tanggal'] = $tgl_susut;
            $data_dvud['formdata']['referensi'] = $rev_jurnal;
            $data_dvud['formdata']['uraian'] = $uraian;
            $data_dvud['formdata']['unit'] = '-';
            $data_dvud['formdata']['rekanan'] = '-';
            $data_dvud['formdata']['jenis'] = 8;
            $data_dvud['formdata']['opr'] = '-';
            $this->savejurnal($data_dvud); //trait Jurnaldata  
        }, 5);  
    }

    public function susutawal(Request $request){  //hitung sampai bulan februari
        DB::transaction(function () {
		    $q_asset = DB::table('t_asset as a')
                       ->where('is_aktiva',1)
                       ->where('a.gol','<>','td')->orderBy('asset_id')
                       ->join('t_tarifsusut as t', 'a.gol','=','t.gol' )
                       ->select('a.tanggal','a.kode','a.kode_asset','a.asset_id','a.tgl_habis','a.harga_beli','a.nilai_buku','a.gol','a.masa','t.tarif','t.jenis')
                       ->get();
            
            foreach($q_asset as $as){
                $asset_id = $as->asset_id;
                $tgl_peroleh = $as->tanggal;
                $tgl_habis = $as->tgl_habis;
                $kode_asset = $as->kode_asset;
                $kode = $as->kode;
                //$selisih_bulan = 12-$bulan_peroleh;
                //dd(intval($bulan_peroleh));
                $tgl_habis = $as->tgl_habis;
                $masa = $as->masa;
                $jenis = $as->jenis;
                $tarif = ($as->tarif)/100;
                $harga_peroleh = $as->harga_beli;
                $harga_beli = $as->harga_beli;
                //--perulangan masa tahun
                $tgl = date("Y-m-t", strtotime($tgl_peroleh));
                $tgl_akhir = date("Y-m-t", strtotime($tgl_habis));
                $akum_susut = 0;
                switch ($jenis) {
                    case 'SLN': 
                        while($tgl <= $tgl_akhir && $tgl <= '2021-02-28'){
                            $bulan_susut = Carbon::parse($tgl)->format('m');
                            $tahun_susut = Carbon::parse($tgl)->format('Y');
                            /* $biaya_susut = 1/12*$tarif*$harga_beli;
                            $akum_susut = $akum_susut + $biaya_susut; */
                            
                            $biaya_susut = 1/12*$tarif*$harga_beli;

                            if(($harga_peroleh - $biaya_susut) < 1)
                                $biaya_susut = $harga_peroleh-1;

                            $akum_susut = $akum_susut + $biaya_susut;

                            $data_inp['tanggal'] = $tgl;
                            $data_inp['tahun'] = $tahun_susut;
                            $data_inp['bulan'] = $bulan_susut;
                            $data_inp['asset_id'] = $asset_id;
                            $data_inp['kode'] = $kode;
                            $data_inp['kode_asset'] = $kode_asset;
                            $data_inp['harga_perolehan'] = $harga_peroleh;
                            $data_inp['beban_susut'] = $biaya_susut;
                            $data_inp['akum_susut'] = $akum_susut;
                            $data_inp['nilai_trans'] = $biaya_susut;
                            $data_inp['kode_trans'] = 300;
                            AktivaTrans::create($data_inp);
                            
                            $harga_peroleh = $harga_peroleh - $biaya_susut;
                            if($harga_peroleh < 2)
                                break;
                            $date = date_create($tgl);
                            date_add($date, date_interval_create_from_date_string('1 day'));
                            $tgl = date_format($date, 'Y-m-t');
                        }
                        break;
                    case 'DB':
                        while($tgl <= $tgl_akhir && $tgl <= '2021-02-28'){
                            $bulan_susut = Carbon::parse($tgl)->format('m');
                            $tahun_susut = Carbon::parse($tgl)->format('Y');
                            /* $biaya_susut = 1/12*$tarif*$harga_peroleh;
                            $akum_susut = $akum_susut + $biaya_susut; */

                            
                            if($tgl_akhir == $tgl)
                                $biaya_susut = $harga_peroleh-1;
                            else
                                $biaya_susut = 1/12*$tarif*$harga_peroleh;

                            if($harga_peroleh < $biaya_susut)
                                $biaya_susut = $harga_peroleh-1;

                            $akum_susut = $akum_susut + $biaya_susut;    

                            $data_inp['tanggal'] = $tgl;
                            $data_inp['tahun'] = $tahun_susut;
                            $data_inp['bulan'] = $bulan_susut;
                            $data_inp['asset_id'] = $asset_id;
                            $data_inp['kode'] = $kode;
                            $data_inp['kode_asset'] = $kode_asset;
                            $data_inp['harga_perolehan'] = $harga_peroleh;
                            $data_inp['beban_susut'] = $biaya_susut;
                            $data_inp['akum_susut'] = $akum_susut;
                            $data_inp['nilai_trans'] = $biaya_susut;
                            $data_inp['kode_trans'] = 300;
                            AktivaTrans::create($data_inp);

                            $harga_peroleh = $harga_peroleh - $biaya_susut;
                            if($harga_peroleh < 2)
                                break;
                            $date = date_create($tgl);
                            date_add($date, date_interval_create_from_date_string('1 day'));
                            $tgl = date_format($date, 'Y-m-t');
                        }
                        break;
                }
                
            }

        }, 5);  
    }
}

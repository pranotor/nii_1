<?php

namespace App\Traits;
use App\Dvud;
use App\Jurnal;
use App\Drd;
use App\ViewJurnal;
use App\Voucherbayar;
use App\Param;
use Illuminate\Http\Request;
use Response;
use DB;


trait Jurnaldata
{
    public function savejurnal($data){
        $query_param = Param::where('param_kode','>=','80000')->where('param_kode','<','90000')->get();
        foreach($query_param as $param){
            switch($param->param_key){
                case 'COA HUTANG USAHA':
                    $data['hutang_usaha'] = $param->param_value;
                    break;
                case 'COA HUTANG NON USAHA':
                    $data['hutang_nonusaha'] = $param->param_value;
                    break;
                case 'COA HUTANG PAJAK':
                    $data['hutang_pajak'] = $param->param_value;
                    break;
                case 'PAJAK INCLUDE JPK':
                    $data['is_pajak_count'] = $param->param_value;    
                case 'COA PERSEDIAAN KIMIA':
                    $data['d_kimia'] = $param->param_value;
                    break;
                case 'COA BAHAN INSTALLASI':
                    $data['d_installasi'] = $param->param_value;
                    break;
                case 'COA UANG MUKA KERJA':
                    $data['d_umk'] = $param->param_value;
                    break;
                case 'COA BIAYA OPRS SUMBER':
                    $data['d_sumber'] = $param->param_value;
                case 'COA BIAYA OPRS PENGOLAHAN':
                    $data['d_pengolahan'] = $param->param_value;
                case 'COA BIAYA OPRS DISTRIBUSI':
                    $data['d_distribusi'] = $param->param_value;
                case 'COA BIAYA KANTOR':
                    $data['d_kantor'] = $param->param_value;
                case 'COA BIAYA HUBLANG':
                    $data['d_hublang'] = $param->param_value;    
            }
        }
        DB::transaction(function () use ($data) {
            if($data['formdata']['isEdit']) {
                //delete
                Jurnal::where('referensi',$data['formdata']['prevReferensi'])->delete();
                if($data['formdata']['jenis']==1){
                    Dvud::where('no_vcr',$data['formdata']['prevReferensi'])->delete();
                }
                if($data['formdata']['jenis']==2){
                    Drd::where('ref_jurnal',$data['formdata']['prevReferensi'])->update(['ref_jurnal'=>$data['formdata']['referensi']]);
                }

                if($data['formdata']['jenis']==4){
                    Voucherbayar::where('ref_jurnal',$data['formdata']['prevReferensi'])->delete();
                }
            }
            $search  = array('.',',');
            $replace = array('','.');
            $d_nama_rupa = $d_kode_rupa =  $k_nama_rupa = $k_kode_rupa =  '';
            $d_kimia = $d_installasi = $d_umk = $d_sumber = $d_pengolahan = $d_distribusi = $d_kantor = $d_hublang = $d_nom_rupa = '0';
		    $d_kode_rupa = '';
            $k_usaha = $k_nonUsaha = $k_pajak = $d_kimia = $d_UMK = $d_instalasi = $d_opSumber = $d_opOlahan = $d_opDistribusi = $d_kantor = $d_hublang = 0;
            $d_usaha = $d_nonUsaha = $d_pajak = $d_nom_rupa = $k_nom_rupa = 0;
            foreach($data['formdata']['datatrans'] as $jurnal){
                $jurnal['debet'] = str_replace($search,$replace,$jurnal['debet']); 
                $jurnal['kredit'] = str_replace($search,$replace,$jurnal['kredit']); 
                $jurnal['tanggal'] = $data['formdata']['tanggal'];
                $jurnal['referensi'] = $data['formdata']['referensi'];
                $jurnal['uraian'] = $data['formdata']['uraian'];
                $jurnal['unit'] = $data['formdata']['unit'];
                $jurnal['rekanan'] = $data['formdata']['rekanan'];
                if(isset($data['formdata']['document'])){
                    $jurnal['document'] = $data['formdata']['document'];
                }
                if(isset($data['formdata']['no_cheq'])){
                    $jurnal['document'] = $data['formdata']['no_cheq'];
                }
                Jurnal::create($jurnal);

                $reksub = substr($jurnal['kode'], 0,5);
                switch ($reksub){
                    case $data['hutang_usaha']:
                        $k_usaha = $k_usaha + $jurnal['kredit'];
                        $d_usaha = $d_usaha + $jurnal['debet'];
                        break;
                    case $data['hutang_nonusaha']:
                        $k_nonUsaha = $k_nonUsaha + $jurnal['kredit'];
                        $d_nonUsaha = $d_nonUsaha + $jurnal['debet'];
                        break;
                    case $data['hutang_pajak']:
                        $k_pajak = $k_pajak + $jurnal['kredit'];
                        $d_pajak = $d_pajak + $jurnal['debet'];
                        break;
                    case $data['d_kimia']:
                        $d_kimia = $d_kimia + $jurnal['debet'];
                        break;
                    case $data['d_installasi']:
                        $d_installasi = $d_installasi + $jurnal['debet'];
                        break;
                    case $data['d_umk']:
                        $d_umk = $d_umk + $jurnal['debet'];
                        break;
                    case $data['d_sumber']:
                        $d_sumber = $d_sumber + $jurnal['debet'];
                        break;
                    case $data['d_pengolahan']:
                        $d_pengolahan = $d_pengolahan + $jurnal['debet'];
                        break;
                    case $data['d_distribusi']:
                        $d_distribusi = $d_distribusi + $jurnal['debet'];
                        break;
                    case $data['d_kantor']:
                        $d_kantor = $d_kantor + $jurnal['debet'];
                        break;
                    case $data['d_hublang']:
                        $d_hublang = $d_hublang + $jurnal['debet'];
                        break;
                    default:
                        if($jurnal['kredit'] == 0){
							$d_kode_rupa .= $jurnal['kode'].", ";
                            $d_nom_rupa = $d_nom_rupa + $jurnal['debet'];
						}
						else{
                            //dd($jurnal['kredit']);
							$k_kode_rupa .= $jurnal['kode'].", ";
                            $k_nom_rupa = $k_nom_rupa + $jurnal['kredit'];
                    
						}
						break;
                }//switch
            }   
            if($data['formdata']['jenis']==1){ // insert t_voucher
                //---insert to voucher--
                $dvud = array();
                $dvud['no_vcr'] =  $data['formdata']['referensi'];
                $dvud['tgl_vcr'] = $data['formdata']['tanggal'];
                $dvud['rekanan'] = $data['formdata']['rekanan'];
                $dvud['uraian'] = $data['formdata']['uraian'];
                $dvud['k_usaha'] = $k_usaha;
                $dvud['k_nonUsaha'] = $k_nonUsaha;
                $dvud['k_pajak'] = $k_pajak;
                $dvud['d_kimia'] = $d_kimia;
                $dvud['d_installasi'] = $d_installasi;
				$dvud['d_umk'] =  $d_umk; 
                $dvud['d_sumber'] = $d_sumber; 
                $dvud['d_pengolahan'] = $d_pengolahan; 
				$dvud['d_distribusi'] = $d_distribusi; 
                $dvud['d_kantor'] =  $d_kantor; 
                $dvud['d_hublang'] = $d_hublang; 
				$dvud['d_kode_rupa'] = $d_kode_rupa; 
                $dvud['d_nom_rupa'] = $d_nom_rupa;
                $dvud['opr1'] = $data['formdata']['opr'];
                Dvud::create($dvud);
            } 
            
            if($data['formdata']['jenis']==4){
                //--insert voucher_bayar
                $dvud = array();
                $dvud['no_vcr'] =  $data['formdata']['voucher'];
                $dvud['ref_jurnal'] =  $data['formdata']['referensi'];
                $dvud['tgl_bayar'] = $data['formdata']['tanggal'];
                $dvud['no_cheq'] = $data['formdata']['no_cheq'];
                $dvud['d_usaha'] = $d_usaha;
                $dvud['d_nonUsaha'] = $d_nonUsaha;
                $dvud['d_pajak'] = $d_pajak;
                $dvud['opr'] = $data['formdata']['opr'];
                Voucherbayar::create($dvud); 
                
                //--hitung apa sudah lunas--
                $query_voucher = Dvud::where('no_vcr',$data['formdata']['voucher'])->first();
                $k_usaha = $query_voucher->k_usaha;
                $k_nonUsaha = $query_voucher->k_nonUsaha;
                $k_pajak = $query_voucher->k_pajak;

                //--hitung pembayaran--
                $query_bayar = DB::table('t_voucher_bayar')
                    ->select(DB::raw('SUM(d_usaha) as d_usaha, SUM(d_nonUsaha) as d_nonUsaha, SUM(d_pajak) as d_pajak'))
                    ->groupBy('no_vcr')
                    ->where('no_vcr',$data['formdata']['voucher'])
                    ->first();
                $d_usaha = $query_bayar->d_usaha;
                $d_nonUsaha = $query_bayar->d_nonUsaha;
                $d_pajak = $query_bayar->d_pajak;

                $selisih = ($k_usaha + $k_nonUsaha) - ($d_usaha + $d_nonUsaha);
                if($data['is_pajak_count']==1) 
                    $selisih = $selisih + $k_pajak - $d_pajak;
                if($selisih == 0)
                    $query_voucher->update(['bayar'=>1]);
                else
                    $query_voucher->update(['bayar'=>0]);
            }
        }, 5);  
    }

    public function deletejurnal($data){
        $query_param = Param::where('param_kode','>=','80000')->where('param_kode','<','90000')->get();
        foreach($query_param as $param){
            switch($param->param_key){
                case 'COA HUTANG USAHA':
                    $data['hutang_usaha'] = $param->param_value;
                    break;
                case 'COA HUTANG NON USAHA':
                    $data['hutang_nonusaha'] = $param->param_value;
                    break;
                case 'COA HUTANG PAJAK':
                    $data['hutang_pajak'] = $param->param_value;
                    break;
                case 'PAJAK INCLUDE JPK':
                    $data['is_pajak_count'] = $param->param_value;    
                case 'COA PERSEDIAAN KIMIA':
                    $data['d_kimia'] = $param->param_value;
                    break;
                case 'COA BAHAN INSTALLASI':
                    $data['d_installasi'] = $param->param_value;
                    break;
                case 'COA UANG MUKA KERJA':
                    $data['d_umk'] = $param->param_value;
                    break;
                case 'COA BIAYA OPRS SUMBER':
                    $data['d_sumber'] = $param->param_value;
                case 'COA BIAYA OPRS PENGOLAHAN':
                    $data['d_pengolahan'] = $param->param_value;
                case 'COA BIAYA OPRS DISTRIBUSI':
                    $data['d_distribusi'] = $param->param_value;
                case 'COA BIAYA KANTOR':
                    $data['d_kantor'] = $param->param_value;
                case 'COA BIAYA HUBLANG':
                    $data['d_hublang'] = $param->param_value;    
            }
        }
        DB::transaction(function () use ($data) {
            $data_jurnal = Jurnal::where('referensi',$data['referensi'])->first();
            if(!is_null($data_jurnal)){
                $jenis = $data_jurnal->jenis;
                Jurnal::where('referensi',$data['referensi'])->delete();

                //hapus data t_voucher,jbk, voucher_bayar jika jenis=1
                if($jenis == 1){
                    Jurnal::where('voucher',$data['referensi'])->where('jenis',4)->delete();
                    Dvud::where('no_vcr',$data['referensi'])->delete();
                    Voucherbayar::where('no_vcr',$data['referensi'])->delete();
                }
                if($jenis == 2){
                    Drd::where('ref_jurnal',$data['referensi'])->delete();
                }

                if($jenis == 4){
                    $no_voucher = $data_jurnal->voucher;
                    Voucherbayar::where('ref_jurnal',$data['referensi'])->delete();
                    //--hitung apa sudah lunas--
                    $query_voucher = Dvud::where('no_vcr',$no_voucher)->first();
                    $k_usaha = $query_voucher->k_usaha;
                    $k_nonUsaha = $query_voucher->k_nonUsaha;
                    $k_pajak = $query_voucher->k_pajak;
                    //--hitung pembayaran--
                    DB::enableQueryLog(); // Enable query log
                    $query_bayar = DB::table('t_voucher_bayar')
                    ->select(DB::raw('SUM(d_usaha) as d_usaha, SUM(d_nonUsaha) as d_nonUsaha, SUM(d_pajak) as d_pajak'))
                    ->groupBy('no_vcr')
                    ->where('no_vcr',$no_voucher)
                    ->first();
                    if(!is_null($query_bayar)){
                        $d_usaha = $query_bayar->d_usaha;
                        $d_nonUsaha = $query_bayar->d_nonUsaha;
                        $d_pajak = $query_bayar->d_pajak;

                        $selisih = ($k_usaha + $k_nonUsaha) - ($d_usaha + $d_nonUsaha);
                        if($data['is_pajak_count']==1) 
                            $selisih = $selisih + $k_pajak - $d_pajak;
                    }
                    else{
                        $selisih = $k_usaha + $k_nonUsaha;
                    }
                    if($selisih != 0)
                        $query_voucher->update(['bayar'=>0]);
                }
            }
            
        }, 5);
    }
}
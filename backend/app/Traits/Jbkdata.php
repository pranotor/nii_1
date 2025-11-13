<?php

namespace App\Traits;
use App\Dvud;
use App\Jurnal;
use App\ViewJurnal;
use App\Param;
use App\Tandatangan;
use App\Voucherbayar;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreJbk;


trait Jbkdata
{
    public function savejbk($data){
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
            }
        }
        DB::transaction(function () use ($data) {
            $search  = array('.',',');
            $replace = array('','.');
            $nom_kredit = 0;
            $d_usaha = $d_nonUsaha = $d_pajak = $k_usaha = $k_nonUsaha = $k_pajak = 0;
            foreach($data['formdata']['dataDebet'] as $jurnal){
                $nom_kredit = str_replace($search,$replace,$jurnal['kredit']);
                if((int)$nom_kredit == 0)
                    continue;
                $jbk_input = array();
                $jbk_input['tanggal'] = $data['formdata']['tgl_bayar'];
                $jbk_input['referensi'] = $data['formdata']['referensi'];
                $jbk_input['uraian'] = $data['formdata']['uraian'];
                $jbk_input['kode'] = $jurnal['kode'];
                $jbk_input['debet'] = str_replace($search,$replace,$jurnal['kredit']);
                $jbk_input['kredit'] = 0;
                $jbk_input['opr'] = $data['formdata']['opr'];
                $jbk_input['jenis'] = $data['formdata']['jenis'];
                $jbk_input['voucher'] = $data['formdata']['voucher'];
                Jurnal::create($jbk_input);

                $reksub = substr($jurnal['kode'], 0,5);
                switch ($reksub){
                    case $data['hutang_usaha']:
                        $d_usaha = $d_usaha + $jbk_input['debet'];
                        break;
                    case $data['hutang_nonusaha']:
                        $d_nonUsaha = $d_nonUsaha + $jbk_input['debet'];
                        break;
                    case $data['hutang_pajak']:
                        $d_pajak = $d_pajak + $jbk_input['debet'];
                        break;
                }//switch
            }   
            // kas jurnal (kredit)
            $jbk_input = array();
            $jbk_input['tanggal'] = $data['formdata']['tgl_bayar'];
            $jbk_input['referensi'] = $data['formdata']['referensi'];
            $jbk_input['uraian'] = $data['formdata']['uraian'];
            $jbk_input['kode'] = $data['formdata']['coa_kas'];
            $jbk_input['debet'] = 0;
            $jbk_input['kredit'] = $data['formdata']['numSumBayar'];
            $jbk_input['opr'] = $data['formdata']['opr'];
            $jbk_input['jenis'] = $data['formdata']['jenis'];
            $jbk_input['voucher'] = $data['formdata']['voucher'];
            Jurnal::create($jbk_input);

            //--insert voucher_bayar
            $dvud = array();
            $dvud['no_vcr'] =  $data['formdata']['voucher'];
            $dvud['ref_jurnal'] =  $data['formdata']['referensi'];
            $dvud['tgl_bayar'] = $data['formdata']['tgl_bayar'];
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
        }, 5);  
    }
}
<?php

namespace App\Traits;
use DB;
use App\Penggunaan;
use App\Quotation;

trait Refno
{
    public function getnumber($jenis,$date,$option=''){
        //dd($date->format('n'));
		switch($jenis){
			case 'bpb':
				$referensi = DB::select('SELECT MAX(CAST(SUBSTR(bpb_no,5,4) AS DECIMAL)) as number FROM t_bpb WHERE YEAR(bpb_tgl)='.$date->format('Y').'  AND MONTH(bpb_tgl)='.$date->format('n'));
				//dd($referensi);
				$number = $referensi[0]->number + 1;
				$str_ref = "BPB-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
				break;
            case 'po':
                $referensi = DB::select('SELECT MAX(CAST(SUBSTR(po_no,4,4) AS DECIMAL)) as number FROM t_po WHERE YEAR(po_tgl)='.$date->format('Y').'  AND MONTH(po_tgl)='.$date->format('n'));
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                $str_ref = "PO-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                break;
            case 'qt':
                $sql_ref = 'SELECT MAX(CAST(SUBSTR(qt_no,5,4) AS DECIMAL)) as number FROM t_quotation WHERE qt_no like "PH%" and YEAR(qt_tgl)='.$date->format('Y').'  AND MONTH(qt_tgl)='.$date->format('n');
                //dd($referensi);
                if($option != 'true')
                    $sql_ref .= " AND P='N'";
                else
                    $sql_ref .= " AND P='P'";
                $referensi = DB::select($sql_ref);
                $number = $referensi[0]->number + 1;
                if($option != 'true')
                    $str_ref = "PH-1".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                else
                    $str_ref = "PH-".str_pad($number, 5, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                break;
            case 'so':
                $sql_ref = 'SELECT MAX(CAST(SUBSTR(so_no,5,5) AS DECIMAL)) as number FROM t_quotation WHERE is_sample=0 AND YEAR(qt_tgl)='.$date->format('Y').'  AND MONTH(qt_tgl)='.$date->format('n');
                if($option != 'true')
                    $sql_ref .= " AND P='N'";
                else
                    $sql_ref .= " AND P='P'";
                $referensi = DB::select($sql_ref);
                $number = $referensi[0]->number + 1;
                if($option != 'true')
                    $str_ref = "SO-1".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                else
                    $str_ref = "SO-".str_pad($number, 5, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                break;
            case 'sr':
                $sql_ref = 'SELECT MAX(CAST(SUBSTR(so_no,5,5) AS DECIMAL)) as number FROM t_quotation WHERE is_sample=1 AND YEAR(qt_tgl)='.$date->format('Y').'  AND MONTH(qt_tgl)='.$date->format('n');
                $referensi = DB::select($sql_ref);
                $number = $referensi[0]->number + 1;
                    $str_ref = "SR-".str_pad($number, 5, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                break;
			case 'wo':
                $referensi = DB::select('SELECT MAX(CAST(SUBSTR(wo_no,4,4) AS DECIMAL)) as number FROM t_wo WHERE YEAR(wo_tgl)='.$date->format('Y').'  AND MONTH(wo_tgl)='.$date->format('n'));
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                $str_ref = "WO-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                break;
            case 'sj':
                //dd($option);
                $sql_ref = 'SELECT MAX(CAST(SUBSTR(sj_no,9,4) AS DECIMAL)) as number FROM t_sj WHERE is_sample=0 and YEAR(sj_tgl)='.$date->format('Y').'  AND MONTH(sj_tgl)='.$date->format('n');
                $so = Quotation::where('so_no',$option)->first();
                if($so->p == 1)
                    $p = 'P';
                else
                    $p = 'N';
                $sql_ref .= " AND p='".$p."'";
                //dd($sql_ref);
                $referensi = DB::select($sql_ref);
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                if($so->p == 'P')
                    $str_ref = "SJ-".$date->format('y').$date->format('m').str_pad($number, 5, "0", STR_PAD_LEFT);
                else
                    $str_ref = "SJ-".$date->format('y').$date->format('m').'1'.str_pad($number, 4, "0", STR_PAD_LEFT);
                break;
            case 'sjsr':
                //dd($option);
                $sql_ref = 'SELECT MAX(CAST(SUBSTR(sj_no,9,4) AS DECIMAL)) as number FROM t_sj WHERE is_sample=1 and YEAR(sj_tgl)='.$date->format('Y').'  AND MONTH(sj_tgl)='.$date->format('n');
                $so = Quotation::where('so_no',$option)->first();
                $referensi = DB::select($sql_ref);
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                $str_ref = "SR-".$date->format('y').$date->format('m').'1'.str_pad($number, 4, "0", STR_PAD_LEFT);
                break;
            case 'rt':
                //dd($option);
                $sql_ref = 'SELECT MAX(CAST(SUBSTR(ret_no,9,4) AS DECIMAL)) as number FROM t_retur WHERE YEAR(ret_tgl)='.$date->format('Y').'  AND MONTH(ret_tgl)='.$date->format('n');
                $referensi = DB::select($sql_ref);
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                $str_ref = "RT-".$date->format('y').$date->format('m').str_pad($number, 4, "0", STR_PAD_LEFT);
                break;
            case 'bankin':
                //dd($option);
                $sql_ref = 'SELECT MAX(CAST(SUBSTR(trans_no,6,4) AS DECIMAL)) as number FROM t_bank_in WHERE YEAR(trans_tgl)='.$date->format('Y').'  AND MONTH(trans_tgl)='.$date->format('n');
                $referensi = DB::select($sql_ref);
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                $str_ref = "B".$date->format('y').$date->format('m').str_pad($number, 4, "0", STR_PAD_LEFT);
                break;
			case 'bpp':
                $sql_ref = 'SELECT MAX(CAST(SUBSTR(bpp_no,5,4) AS DECIMAL)) as number FROM t_minta WHERE YEAR(tanggal)='.$date->format('Y').'  AND MONTH(tanggal)='.$date->format('n');
				if($option != ''){
                    $kegunaan = Penggunaan::find($option);
                    $sql_ref .= " AND guna_id=".$option;
                }

                $referensi = DB::select($sql_ref);
				//dd($referensi);

				$number = $referensi[0]->number + 1;
				$str_ref = "BPP-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y')."-".$kegunaan->alias;
				break;
            case 'susut':
                $referensi = DB::select("SELECT MAX(bulan) as number FROM t_asset_trans WHERE tahun=".$date->format('Y')." AND kode_trans=300");
                $str_ref = $referensi[0]->number;
                break;
            case 'ct':
                $referensi = DB::select('SELECT MAX(CAST(SUBSTR(ct_no,4,4) AS DECIMAL)) as number FROM t_cutting WHERE YEAR(ct_tgl)='.$date->format('Y').'  AND MONTH(ct_tgl)='.$date->format('n'));
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                $str_ref = "CT-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                break;
            case 'pd':
                $referensi = DB::select('SELECT MAX(CAST(SUBSTR(prod_no,4,4) AS DECIMAL)) as number FROM t_produksi WHERE YEAR(prod_tgl)='.$date->format('Y').'  AND MONTH(prod_tgl)='.$date->format('n'));
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                $str_ref = "PD-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                break;
            case 'wo':
                $referensi = DB::select('SELECT MAX(CAST(SUBSTR(wo_no,4,4) AS DECIMAL)) as number FROM t_wo WHERE YEAR(wo_tgl)='.$date->format('Y').'  AND MONTH(wo_tgl)='.$date->format('n'));
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                $str_ref = "WO-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                break;
            case 'rb':
                $referensi = DB::select('SELECT MAX(CAST(SUBSTR(ret_no,4,4) AS DECIMAL)) as number FROM t_retur_beli WHERE YEAR(ret_tgl)='.$date->format('Y').'  AND MONTH(ret_tgl)='.$date->format('n'));
                //dd($referensi);
                $number = $referensi[0]->number + 1;
                $str_ref = "RB-".str_pad($number, 4, "0", STR_PAD_LEFT).".".$date->format('m').".".$date->format('y');
                break;
			default:
				$referensi = DB::select('SELECT MAX(CAST(SUBSTR(referensi,1,4) AS DECIMAL)) as number FROM jurnal WHERE YEAR(tanggal)='.$date->format('Y').' AND jenis='.$jenis.' AND MONTH(tanggal)='.$date->format('n'));
				//dd($referensi);	str_pad($input, 10, "-=", STR_PAD_LEFT)
				$number = $referensi[0]->number + 1;
				$str_ref = str_pad($number, 4, "0", STR_PAD_LEFT).".".$jenis.".".$date->format('m').".".$date->format('y');
		}
		return $str_ref;
    }
}

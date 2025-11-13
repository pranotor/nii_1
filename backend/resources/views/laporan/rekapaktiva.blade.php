@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>REKAP AKTIVA</u></span></td>
  </tr>  
  <tr class="noborder">
    @if($periode=='bl')
    <td align="center"><b>Bulan {{$monthName}}</b></td>
    @else
    <td align="center"><b>Tahun {{$year}}</h3></b>
    @endif
  </tr>
</table>
<table width="100%" border="1" cellpadding="5" cellspacing="0">
  <thead>
    <tr>
        <th style= "width: 4%; border-bottom-width:0.1px;" rowspan="2"><strong>Kode</strong></th>
        <th style= "width: 19%; border-bottom-width:0.1px;" rowspan="2"><strong>Uraian</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" rowspan="2"><strong>Saldo Awal <br/>{{$periode_awal}}</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" rowspan="2"><strong>Mutasi {{$periode_awal}} (+)</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" rowspan="2"><strong>Mutasi {{$periode_awal}} (-)</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" rowspan="2"><strong>Saldo Akhir <br/>{{$periode_akhir->translatedFormat('t F Y')}}</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" rowspan="2"><strong>Ak Penyusutan <br/>{{$periode_sebelum->translatedFormat('t F Y')}}</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" rowspan="2"><strong>Nilai Buku Bersih <br/>{{$periode_awal}}</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" colspan="2"><strong>Koreksi / Penghapusan (-)</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" colspan="2"><strong>Koreksi (+)</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" rowspan="2"><strong>Biaya Penyusutan <br/>{{$periode_awal}}</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" rowspan="2"><strong>Ak Penyusutan <br/>{{$periode_akhir->translatedFormat('t F Y')}}</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;" rowspan="2"><strong>Nilai Buku Bersih <br/>{{$periode_akhir->translatedFormat('t F Y')}}</strong></th>
      </tr>
      <tr>
        <th>Ak Penyusutan</th>
        <th>Nilai Bersih</th>
        <th>Ak Penyusutan</th>
        <th>Nilai Bersih</th>
      </tr>
      </thead>
    @foreach ($data_jurnal as $j)
        <?php 
            $harga_lalu = $j->harga_perolehan - $j->tambah_ini - $j->kurang_lalu;
            $harga_perolehan = $j->harga_perolehan;
            $turun_ini = $j->turun_ini;
            $turun_lalu = $j->turun_lalu;
            $naik_ini = $j->naik_ini;
            $naik_lalu = $j->naik_lalu;
            $tambah_ini = $j->tambah_ini;
            $kurang_ini = $j->kurang_ini;
            $akum_susut_lalu = $j->akum_susut_lalu;
            $susut_ini = $j->susut_ini;
            $akum_susut = $j->susut_ini + $akum_susut_lalu;
            
            $nilai_bersih_awal = $j->harga_perolehan - $akum_susut_lalu;
            $akum_koreksi_naik = 0;
            $akum_koreksi_turun = 0;
            $nilai_bersih = $harga_perolehan - $akum_susut - $kurang_ini;
        ?>
        <tr>
            <td>{{$j->kode}}</td>
            <td>{{$j->nama}}</td>
            <td align="right">{{($harga_lalu==0? '' : number_format($harga_lalu,2))}}</td>
            <td align="right">{{($tambah_ini==0? '' : number_format($tambah_ini,2))}}</td>
            <td align="right">{{($kurang_ini==0? '' : number_format($kurang_ini,2))}}</td>
            <td align="right">{{($harga_perolehan==0? '' : number_format($harga_perolehan,2))}}</td>
            <td align="right">{{($akum_susut_lalu==0? '' : number_format($akum_susut_lalu,2))}}</td>
            <td align="right">{{($nilai_bersih_awal==0? '' : number_format($nilai_bersih_awal,2))}}</td>
            <td align="right">{{($akum_koreksi_turun==0? '' : number_format($akum_koreksi_turun,2))}}</td>
            <td align="right">{{($turun_ini==0? '' : number_format($turun_ini,2))}}</td>
            <td align="right">{{($akum_koreksi_naik==0? '' : number_format($akum_koreksi_naik,2))}}</td>
            <td align="right">{{($naik_ini==0? '' : number_format($naik_ini,2))}}</td>
            <td align="right">{{($susut_ini==0? '' : number_format($susut_ini,2))}}</td>
            <td align="right">{{($akum_susut==0? '' : number_format($akum_susut,2))}}</td>
            <td align="right">{{($nilai_bersih==0? '' : number_format($nilai_bersih,2))}}</td>
        </tr>
  @endforeach

</table>

<x-ttd-jurnal :mod-name="$modname" jenis="0"/>

@stop

@push('head')
<style>

    table th{
     text-align: center !important;
     /*border: 0.2px solid black;*/
    }
    th{
     text-align: center !important;
     border: 1px solid black;
     font-size: 9pt;
     font-weight: bold !important;
     padding: 5px;
     background-color: cornflowerblue;
    }
    
    td{
     font-size: 9pt;
     padding: 5px;
     /* border-left: 1px solid black;
     border-right: 1px solid black; */
    /*  border-top: none;
     border-bottom: none; */
    }
    
    td.withborder{
     font-size: 9pt;
     padding: 5px;
     border-left: 1px solid black;
     border-right: 1px solid black;
     border-top: 1px solid black;;
     border-bottom: 1px solid black;
    }
    
    .noborder td{
     border-top: none;
     border-bottom: none;
     border-left: none;
     border-right: none;
     font-size: 9pt;
     font-weight: bold !important;
     padding: 5px;
    }
    
    .colorfulltd{
        background-color: darksalmon
    }
    
    .underlinetd{
        text-decoration: underline;
    }
</style>
@endpush
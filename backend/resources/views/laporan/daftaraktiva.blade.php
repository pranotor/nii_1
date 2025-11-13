@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>DAFTAR AKTIVA</u></span></td>
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
    <th style= "width: 2%; border-bottom-width:0.1px;" rowspan="2"><strong>No</strong></th>
    <th style= "width: 3%; border-bottom-width:0.1px;" rowspan="2"><strong>Tanggal</strong></th>
    <th style= "width: 3%; border-bottom-width:0.1px;" rowspan="2"><strong>Tanggal Koreksi</strong></th>
    <th style= "width: 3%; border-bottom-width:0.1px;" rowspan="2"><strong>Kode</strong></th>
    <th style= "width: 3%; border-bottom-width:0.1px;" rowspan="2"><strong>Kode Aset</strong></th>
    <th style= "width: 16%; border-bottom-width:0.1px;" rowspan="2"><strong>Uraian</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>Harga Perolehan <br/>{{$periode_awal}}</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>Mutasi {{$periode_awal}} (+)</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>Mutasi {{$periode_awal}} (-)</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>Saldo Akhir <br/>{{$periode_akhir->translatedFormat('t F Y')}}</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" colspan="2"><strong>Jumlah Satuan</strong></th>
    <th style= "width: 3%; border-bottom-width:0.1px;" rowspan="2"><strong>Gol</strong></th>
    <th style= "width: 3%; border-bottom-width:0.1px;" rowspan="2"><strong>Tarif</strong></th>
    <th style= "width: 4%; border-bottom-width:0.1px;" rowspan="2"><strong>Masa Manfaat</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>Ak Penyusutan <br/>{{$periode_sebelum->translatedFormat('t F Y')}}</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>Nilai Buku Bersih <br/>{{$periode_awal}}</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" colspan="2"><strong>Koreksi / Penghapusan (-)</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" colspan="2"><strong>Koreksi (+)</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>Biaya Penyusutan <br/>{{$periode_awal}}</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>Ak Penyusutan <br/>{{$periode_akhir->translatedFormat('t F Y')}}</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>Nilai Buku Bersih <br/>{{$periode_akhir->translatedFormat('t F Y')}}</strong></th>
  </tr>
  <tr>
    <th>J</th>
    <th>S</th>
    <th>Ak Penyusutan</th>
    <th>Nilai Bersih</th>
    <th>Ak Penyusutan</th>
    <th>Nilai Bersih</th>
  </tr>
  </thead>
  <?php
    $pre_parent_kode = "";
    $pre_parent_nama = "";
    $pre_kode = "";
    $pre_nama = "";
    $cur_parent_kode = "";
    $cur_parent_nama = "";
    $cur_kode = "";
    $cur_nama = "";
    $counter = 1;

    $tot_harga_perolehan = 0;
    $tot_naik = "0";
    $tot_turun = "0";
    $tot_susut_ini = 0;
    $tot_susut_lalu = 0;
    $tot_harga_perolehan_akhir = 0;
    $tot_akum_susut_blm_koreksi = 0;
    $tot_nilai_buku_blm_koreksi = 0;
    $tot_koreksi_akum_susut = 0;
    $tot_koreksi_nilai = 0;
    $tot_akum_koreksi_turun = 0;
    $tot_akum_koreksi_naik = 0;
    $tot_nilai_koreksi_turun = 0;
    $tot_nilai_koreksi_naik = 0;
    $tot_nilai_bersih = 0;

    $grand_tot_harga_perolehan = 0;
    $grand_tot_naik = "0";
    $grand_tot_turun = "0";
    $grand_tot_susut_ini = 0;
    $grand_tot_susut_lalu = 0;
    $grand_tot_nilai_bersih_lalu = 0;
    $grand_tot_harga_perolehan_akhir = 0;
    $grand_tot_akum_susut_blm_koreksi = 0;
    $grand_tot_akum_susut = 0;
    $grand_tot_nilai_buku_blm_koreksi = 0;
    $grand_tot_koreksi_akum_susut = 0;
    $grand_tot_koreksi_nilai = 0;
    $grand_tot_akum_koreksi_turun = 0;
    $grand_tot_akum_koreksi_naik = 0;
    $grand_tot_nilai_koreksi_turun = 0;
    $grand_tot_nilai_koreksi_naik = 0;
    $grand_tot_nilai_bersih = 0;

  ?>

  @foreach($data_jurnal as $j)
   <?php
     $cur_kode = $j->kode;
     $cur_nama = $j->nama;
     $cur_parent_kode = $j->kp;
     $cur_parent_nama = $j->np;
     $tgl_akhir = $periode_akhir->format('Y-m-t');
     $tgl_awal = $periode_akhir->format('Y-m-d');

     $susut_lalu = ($j->susut_lalu == 0 ? $j->akum_susut : $j->susut_lalu);
     $nilai_bersih_lalu = $j->harga_beli - $susut_lalu;
     $akum_susut = $susut_lalu + $j->susut_ini;

     if($j->koreksi){
         $tgl_koreksi = ($j->naik==0 ? $j->tgl_turun : $j->tgl_naik);
         $str_tgl_koreksi = \Carbon\Carbon::parse($tgl_koreksi)->format('d.m.Y');
         if($tgl_koreksi > $tgl_akhir) {
            $harga_perolehan = $j->harga_beli;
            $naik = "0";
            $turun = "0";
            $harga_perolehan_akhir = $harga_perolehan;
            $akum_susut_blm_koreksi = 0;
            $nilai_buku_blm_koreksi = 0;
            $koreksi_akum_susut = 0;
            $koreksi_nilai = 0;

         }
         elseif($tgl_koreksi <= $tgl_akhir && $tgl_koreksi >= $tgl_awal) {
            $harga_perolehan = $j->harga_beli;
            $naik = $j->naik;
            $turun = $j->turun;
            $harga_perolehan_akhir = $harga_perolehan + $naik - $turun;
            $akum_susut_blm_koreksi = $j->akum_susut_blm_koreksi;
            $nilai_buku_blm_koreksi = $j->nilai_buku_blm_koreksi;
            $koreksi_akum_susut = $susut_lalu - $akum_susut_blm_koreksi;
            $koreksi_nilai = $nilai_bersih_lalu - $nilai_buku_blm_koreksi;
         }
         else{
            $harga_perolehan = $j->harga_beli + $j->naik - $j->turun;
            $naik = "0";
            $turun = "0";
            $harga_perolehan_akhir = $harga_perolehan;
            $akum_susut_blm_koreksi = 0;
            $nilai_buku_blm_koreksi = 0;
            $koreksi_akum_susut = 0;
            $koreksi_nilai = 0;
         }
     } 
     else {
         $str_tgl_koreksi = "";
         $harga_perolehan = $j->harga_beli + $j->naik - $j->turun;
         $naik = "0";
         $turun = "0";
         $harga_perolehan_akhir = $harga_perolehan;
         $akum_susut_blm_koreksi = 0;
         $nilai_buku_blm_koreksi = 0;
         $koreksi_akum_susut = 0;
         $koreksi_nilai = 0;
     }
     
     $akum_koreksi_turun = ($j->koreksi && $koreksi_akum_susut < 0 ? abs($koreksi_akum_susut) : 0);
     $akum_koreksi_naik = ($j->koreksi && $koreksi_akum_susut > 0 ? $koreksi_akum_susut : 0);
     $nilai_koreksi_turun = ($j->koreksi && $koreksi_nilai < 0 ? abs($koreksi_nilai) : 0);
     $nilai_koreksi_naik = ($j->koreksi && $koreksi_nilai > 0 ? $koreksi_nilai : 0);
     $nilai_bersih = $nilai_bersih_lalu + $nilai_koreksi_naik - $j->susut_ini - $akum_koreksi_naik + $akum_koreksi_turun;

   ?>
    @if($cur_parent_kode != $pre_parent_kode)
    @if (!$loop->first)
        <tr>
            <td colspan="6">JUMLAH {{$pre_nama}}</td>
            <td align="right">{{($tot_harga_perolehan==0? '' : number_format($tot_harga_perolehan,2))}}</td>
            <td align="right">{{($tot_naik==0? '' : number_format($tot_naik,2))}}</td>
            <td align="right">{{($tot_turun==0? '' : number_format($tot_turun,2))}}</td>
            <td align="right">{{($tot_harga_perolehan_akhir==0? '' : number_format($tot_harga_perolehan_akhir,2))}}</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">{{($tot_susut_lalu==0? '' : number_format($tot_susut_lalu,2))}}</td>
            <td align="right">{{($tot_nilai_bersih_lalu==0? '' : number_format($tot_nilai_bersih_lalu,2))}}</td>
            <td align="right">{{($tot_akum_koreksi_turun==0? '' : number_format($tot_akum_koreksi_turun,2))}}</td>
            <td align="right">{{($tot_nilai_koreksi_turun==0? '' : number_format($tot_nilai_koreksi_turun,2))}}</td>
            <td align="right">{{($tot_akum_koreksi_naik==0? '' : number_format($tot_akum_koreksi_naik,2))}}</td>
            <td align="right">{{($tot_nilai_koreksi_naik==0? '' : number_format($tot_nilai_koreksi_naik,2))}}</td>
            <td align="right">{{($tot_susut_ini==0? '' : number_format($tot_susut_ini,2))}}</td>
            <td align="right">{{($tot_akum_susut==0? '' : number_format($tot_akum_susut,2))}}</td>
            <td align="right">{{($tot_nilai_bersih==0? '' : number_format($tot_nilai_bersih,2))}}</td>
        </tr>
        <tr>
            <td colspan="6">JUMLAH {{$pre_parent_nama}}</td>
            <td align="right">{{($grand_tot_harga_perolehan==0? '' : number_format($grand_tot_harga_perolehan,2))}}</td>
            <td align="right">{{($grand_tot_naik==0? '' : number_format($grand_tot_naik,2))}}</td>
            <td align="right">{{($grand_tot_turun==0? '' : number_format($grand_tot_turun,2))}}</td>
            <td align="right">{{($grand_tot_harga_perolehan_akhir==0? '' : number_format($grand_tot_harga_perolehan_akhir,2))}}</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">{{($grand_tot_susut_lalu==0? '' : number_format($grand_tot_susut_lalu,2))}}</td>
            <td align="right">{{($grand_tot_nilai_bersih_lalu==0? '' : number_format($grand_tot_nilai_bersih_lalu,2))}}</td>
            <td align="right">{{($grand_tot_akum_koreksi_turun==0? '' : number_format($grand_tot_akum_koreksi_turun,2))}}</td>
            <td align="right">{{($grand_tot_nilai_koreksi_turun==0? '' : number_format($grand_tot_nilai_koreksi_turun,2))}}</td>
            <td align="right">{{($grand_tot_akum_koreksi_naik==0? '' : number_format($grand_tot_akum_koreksi_naik,2))}}</td>
            <td align="right">{{($grand_tot_nilai_koreksi_naik==0? '' : number_format($grand_tot_nilai_koreksi_naik,2))}}</td>
            <td align="right">{{($grand_tot_susut_ini==0? '' : number_format($grand_tot_susut_ini,2))}}</td>
            <td align="right">{{($grand_tot_akum_susut==0? '' : number_format($grand_tot_akum_susut,2))}}</td>
            <td align="right">{{($grand_tot_nilai_bersih==0? '' : number_format($grand_tot_nilai_bersih,2))}}</td>
        </tr>
    @endif
    <?php $counter=1; 
            $tot_harga_perolehan = 0;
            $tot_naik = "0";
            $tot_turun = "0";
            $tot_susut_ini = 0;
            $tot_susut_lalu = 0;
            $tot_nilai_bersih_lalu = 0;
            $tot_harga_perolehan_akhir = 0;
            $tot_akum_susut_blm_koreksi = 0;
            $tot_akum_susut = 0;
            $tot_nilai_buku_blm_koreksi = 0;
            $tot_koreksi_akum_susut = 0;
            $tot_koreksi_nilai = 0;
            $tot_akum_koreksi_turun = 0;
            $tot_akum_koreksi_naik = 0;
            $tot_nilai_koreksi_turun = 0;
            $tot_nilai_koreksi_naik = 0;
            $tot_nilai_bersih = 0;

            $grand_tot_harga_perolehan = 0;
            $grand_tot_naik = "0";
            $grand_tot_turun = "0";
            $grand_tot_susut_ini = 0;
            $grand_tot_susut_lalu = 0;
            $grand_tot_nilai_bersih_lalu = 0;
            $grand_tot_harga_perolehan_akhir = 0;
            $grand_tot_akum_susut_blm_koreksi = 0;
            $grand_tot_akum_susut = 0;
            $grand_tot_nilai_buku_blm_koreksi = 0;
            $grand_tot_koreksi_akum_susut = 0;
            $grand_tot_koreksi_nilai = 0;
            $grand_tot_akum_koreksi_turun = 0;
            $grand_tot_akum_koreksi_naik = 0;
            $grand_tot_nilai_koreksi_turun = 0;
            $grand_tot_nilai_koreksi_naik = 0;
            $grand_tot_nilai_bersih = 0;
        
        ?>
    <tr>
        <td colspan="24">{{$cur_parent_nama}}</td>
    </tr>
    <tr>
        <td colspan="24">{{$cur_nama}}</td>
    </tr>
    @else 
        @if($cur_kode != $pre_kode)
        <tr>
            <td colspan="6">JUMLAH {{$pre_nama}}</td>
            <td align="right">{{($tot_harga_perolehan==0? '' : number_format($tot_harga_perolehan,2))}}</td>
            <td align="right">{{($tot_naik==0? '' : number_format($tot_naik,2))}}</td>
            <td align="right">{{($tot_turun==0? '' : number_format($tot_turun,2))}}</td>
            <td align="right">{{($tot_harga_perolehan_akhir==0? '' : number_format($tot_harga_perolehan_akhir,2))}}</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">{{($tot_susut_lalu==0? '' : number_format($tot_susut_lalu,2))}}</td>
            <td align="right">{{($tot_nilai_bersih_lalu==0? '' : number_format($tot_nilai_bersih_lalu,2))}}</td>
            <td align="right">{{($tot_akum_koreksi_turun==0? '' : number_format($tot_akum_koreksi_turun,2))}}</td>
            <td align="right">{{($tot_nilai_koreksi_turun==0? '' : number_format($tot_nilai_koreksi_turun,2))}}</td>
            <td align="right">{{($tot_akum_koreksi_naik==0? '' : number_format($tot_akum_koreksi_naik,2))}}</td>
            <td align="right">{{($tot_nilai_koreksi_naik==0? '' : number_format($tot_nilai_koreksi_naik,2))}}</td>
            <td align="right">{{($tot_susut_ini==0? '' : number_format($tot_susut_ini,2))}}</td>
            <td align="right">{{($tot_akum_susut==0? '' : number_format($tot_akum_susut,2))}}</td>
            <td align="right">{{($tot_nilai_bersih==0? '' : number_format($tot_nilai_bersih,2))}}</td>
        </tr>
        <tr>
            <td colspan="24">{{$cur_nama}}</td>
        </tr>
        <?php $counter=1; 
            $tot_harga_perolehan = 0;
            $tot_naik = "0";
            $tot_turun = "0";
            $tot_susut_ini = 0;
            $tot_susut_lalu = 0;
            $tot_nilai_bersih_lalu = 0;
            $tot_harga_perolehan_akhir = 0;
            $tot_akum_susut_blm_koreksi = 0;
            $tot_akum_susut = 0;
            $tot_nilai_buku_blm_koreksi = 0;
            $tot_koreksi_akum_susut = 0;
            $tot_koreksi_nilai = 0;
            $tot_akum_koreksi_turun = 0;
            $tot_akum_koreksi_naik = 0;
            $tot_nilai_koreksi_turun = 0;
            $tot_nilai_koreksi_naik = 0;
            $tot_nilai_bersih = 0;
        
        ?>
        @endif
    @endif
    <tr>
        <td>{{$counter}}</td>
        <td>{{\Carbon\Carbon::parse($j->tanggal)->format('d.m.Y')}}</td>
        <td>{{$str_tgl_koreksi}}</td>
        <td>{{$j->kode}}</td>
        <td>{{$j->kode_asset}}</td>
        <td>{{$j->uraian}}</td>
        <td align="right">{{($harga_perolehan==0? '' : number_format($harga_perolehan,2))}}</td>
        <td align="right">{{($naik==0? '' : number_format($naik,2))}}</td>
        <td align="right">{{($turun==0? '' : number_format($turun,2))}}</td>
        <td align="right">{{($harga_perolehan_akhir==0? '' : number_format($harga_perolehan_akhir,2))}}</td>
        <td align="right">{{($j->jumlah==0? '' : number_format($j->jumlah,2))}}</td>
        <td align="right">{{$j->satuan}}</td>
        <td>{{$j->gol}}</td>
        <td align="right">{{($j->tarif * 100==0? '' : number_format($j->tarif * 100,2))}}</td>
        <td>{{$j->masa}}</td>
        <td align="right">{{($susut_lalu==0? '' : number_format($susut_lalu,2))}}</td>
        <td align="right">{{($nilai_bersih_lalu==0? '' : number_format($nilai_bersih_lalu,2))}}</td>
        <td align="right">{{($akum_koreksi_turun==0? '' : number_format($akum_koreksi_turun,2))}}</td>
        <td align="right">{{($nilai_koreksi_turun==0? '' : number_format($nilai_koreksi_turun,2))}}</td>
        <td align="right">{{($akum_koreksi_naik==0? '' : number_format($akum_koreksi_naik,2))}}</td>
        <td align="right">{{($nilai_koreksi_naik==0? '' : number_format($nilai_koreksi_naik,2))}}</td>
        <td align="right">{{($j->susut_ini==0? '' : number_format($j->susut_ini,2))}}</td>
        <td align="right">{{($akum_susut==0? '' : number_format($akum_susut,2))}}</td>
        <td align="right">{{($nilai_bersih==0? '' : number_format($nilai_bersih,2))}}</td>
    </tr>

    <?php
        $pre_parent_kode = $j->kp;
        $pre_parent_nama = $j->np;
        $pre_kode = $j->kode;
        $pre_nama = $j->nama;
        $counter++; 

        $tot_harga_perolehan = $tot_harga_perolehan + $harga_perolehan;
        $tot_naik = $tot_naik + $naik;
        $tot_turun = $tot_turun + $turun;
        $tot_susut_ini = $tot_susut_ini + $j->susut_ini;
        $tot_susut_lalu = $tot_susut_lalu + $susut_lalu;
        $tot_nilai_bersih_lalu = $tot_nilai_bersih_lalu + $nilai_bersih_lalu;
        $tot_harga_perolehan_akhir = $tot_harga_perolehan_akhir + $harga_perolehan_akhir;
        $tot_akum_susut_blm_koreksi = $tot_akum_susut_blm_koreksi + $akum_susut_blm_koreksi;
        $tot_akum_susut = $tot_akum_susut + $akum_susut;
        $tot_nilai_buku_blm_koreksi = $tot_nilai_buku_blm_koreksi + $nilai_buku_blm_koreksi;
        $tot_koreksi_akum_susut = $tot_koreksi_akum_susut + $koreksi_akum_susut;
        $tot_koreksi_nilai = $tot_koreksi_nilai + $koreksi_nilai;
        $tot_akum_koreksi_turun = $tot_akum_koreksi_turun + $akum_koreksi_turun;
        $tot_akum_koreksi_naik = $tot_akum_koreksi_naik + $akum_koreksi_naik;
        $tot_nilai_koreksi_turun = $tot_nilai_koreksi_turun + $nilai_koreksi_turun;
        $tot_nilai_koreksi_naik = $tot_nilai_koreksi_naik + $nilai_koreksi_naik;
        $tot_nilai_bersih = $tot_nilai_bersih + $nilai_bersih;

        $grand_tot_harga_perolehan = $grand_tot_harga_perolehan + $harga_perolehan;
        $grand_tot_naik = $grand_tot_naik + $naik;
        $grand_tot_turun = $grand_tot_turun + $turun;
        $grand_tot_susut_ini = $grand_tot_susut_ini + $j->susut_ini;
        $grand_tot_susut_lalu = $grand_tot_susut_lalu + $susut_lalu;
        $grand_tot_nilai_bersih_lalu = $grand_tot_nilai_bersih_lalu + $nilai_bersih_lalu;
        $grand_tot_harga_perolehan_akhir = $grand_tot_harga_perolehan_akhir + $harga_perolehan_akhir;
        $grand_tot_akum_susut_blm_koreksi = $grand_tot_akum_susut_blm_koreksi + $akum_susut_blm_koreksi;
        $grand_tot_akum_susut = $grand_tot_akum_susut + $akum_susut;
        $grand_tot_nilai_buku_blm_koreksi = $grand_tot_nilai_buku_blm_koreksi + $nilai_buku_blm_koreksi;
        $grand_tot_koreksi_akum_susut = $grand_tot_koreksi_akum_susut + $koreksi_akum_susut;
        $grand_tot_koreksi_nilai = $grand_tot_koreksi_nilai + $koreksi_nilai;
        $grand_tot_akum_koreksi_turun = $grand_tot_akum_koreksi_turun + $akum_koreksi_turun;
        $grand_tot_akum_koreksi_naik = $grand_tot_akum_koreksi_naik + $akum_koreksi_naik;
        $grand_tot_nilai_koreksi_turun = $grand_tot_nilai_koreksi_turun + $nilai_koreksi_turun;
        $grand_tot_nilai_koreksi_naik = $grand_tot_nilai_koreksi_naik + $nilai_koreksi_naik;
        $grand_tot_nilai_bersih = $grand_tot_nilai_bersih + $nilai_bersih;
    ?>
    @if ($loop->last)
        <tr>
            <td colspan="6">JUMLAH {{$pre_nama}}</td>
            <td align="right">{{($tot_harga_perolehan==0? '' : number_format($tot_harga_perolehan,2))}}</td>
            <td align="right">{{($tot_naik==0? '' : number_format($tot_naik,2))}}</td>
            <td align="right">{{($tot_turun==0? '' : number_format($tot_turun,2))}}</td>
            <td align="right">{{($tot_harga_perolehan_akhir==0? '' : number_format($tot_harga_perolehan_akhir,2))}}</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">{{($tot_susut_lalu==0? '' : number_format($tot_susut_lalu,2))}}</td>
            <td align="right">{{($tot_nilai_bersih_lalu==0? '' : number_format($tot_nilai_bersih_lalu,2))}}</td>
            <td align="right">{{($tot_akum_koreksi_turun==0? '' : number_format($tot_akum_koreksi_turun,2))}}</td>
            <td align="right">{{($tot_nilai_koreksi_turun==0? '' : number_format($tot_nilai_koreksi_turun,2))}}</td>
            <td align="right">{{($tot_akum_koreksi_naik==0? '' : number_format($tot_akum_koreksi_naik,2))}}</td>
            <td align="right">{{($tot_nilai_koreksi_naik==0? '' : number_format($tot_nilai_koreksi_naik,2))}}</td>
            <td align="right">{{($tot_susut_ini==0? '' : number_format($tot_susut_ini,2))}}</td>
            <td align="right">{{($tot_akum_susut==0? '' : number_format($tot_akum_susut,2))}}</td>
            <td align="right">{{($tot_nilai_bersih==0? '' : number_format($tot_nilai_bersih,2))}}</td>
        </tr>
        <tr>
            <td colspan="6">JUMLAH {{$pre_parent_nama}}</td>
            <td align="right">{{($grand_tot_harga_perolehan==0? '' : number_format($grand_tot_harga_perolehan,2))}}</td>
            <td align="right">{{($grand_tot_naik==0? '' : number_format($grand_tot_naik,2))}}</td>
            <td align="right">{{($grand_tot_turun==0? '' : number_format($grand_tot_turun,2))}}</td>
            <td align="right">{{($grand_tot_harga_perolehan_akhir==0? '' : number_format($grand_tot_harga_perolehan_akhir,2))}}</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">{{($grand_tot_susut_lalu==0? '' : number_format($grand_tot_susut_lalu,2))}}</td>
            <td align="right">{{($grand_tot_nilai_bersih_lalu==0? '' : number_format($grand_tot_nilai_bersih_lalu,2))}}</td>
            <td align="right">{{($grand_tot_akum_koreksi_turun==0? '' : number_format($grand_tot_akum_koreksi_turun,2))}}</td>
            <td align="right">{{($grand_tot_nilai_koreksi_turun==0? '' : number_format($grand_tot_nilai_koreksi_turun,2))}}</td>
            <td align="right">{{($grand_tot_akum_koreksi_naik==0? '' : number_format($grand_tot_akum_koreksi_naik,2))}}</td>
            <td align="right">{{($grand_tot_nilai_koreksi_naik==0? '' : number_format($grand_tot_nilai_koreksi_naik,2))}}</td>
            <td align="right">{{($grand_tot_susut_ini==0? '' : number_format($grand_tot_susut_ini,2))}}</td>
            <td align="right">{{($grand_tot_akum_susut==0? '' : number_format($grand_tot_akum_susut,2))}}</td>
            <td align="right">{{($grand_tot_nilai_bersih==0? '' : number_format($grand_tot_nilai_bersih,2))}}</td>
        </tr>
    @endif
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
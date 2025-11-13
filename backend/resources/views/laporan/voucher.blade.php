@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>REKAP VOUCHER</u></span></td>
  </tr>  
  <tr class="noborder">
    @if($periode=='bl')
    <td align="center"><b>Bulan {{$monthName}} / {{$year}}</b></td>
    @else
    <td align="center"><b>Tahun {{$year}}</b></td>
    @endif
  </tr>
</table>
<table width="100%" border="1" cellpadding="5" cellspacing="0">
  <thead>
  <tr>
    <th style= "width: 11%; border-bottom-width:0.1px;" colspan="2"><strong>Voucer</strong></th>
    <th style= "width: 18%; border-bottom-width:0.1px;" rowspan="3"><strong>Uraian</strong></th>
    <th style= "width: 12%; border-bottom-width:0.1px;" colspan="2"><strong>Dibayar</strong></th>
    <th style= "width: 12%; border-bottom-width:0.1px;" colspan="2"><strong>Kredit</strong></th>
    <th style= "width: 47%; border-bottom-width:0.1px;" colspan="10"><strong>Debet</strong></th>
  </tr>
  <tr>
    <th rowspan="2" width="4">Tgl</th>
    <th rowspan="2" width="7">No</th>
    <th rowspan="2" width="6">Tgl</th>
    <th rowspan="2" width="6">Chek / Giro</th>
    <th colspan="2" width="12">Utang</th>
    <th rowspan="2" width="5">PERSD. BAHAN KIMIA</th>
    <th rowspan="2" width="5">BAHAN INSTALASI</th>
    <th rowspan="2" width="5">UANG MUKA KERJA</th>
    <th rowspan="2" width="5">BIAYA OPR SUMBER</th>
    <th rowspan="2" width="5">BIAYA OPR PENGOLAHAN</th>
    <th rowspan="2" width="5">BIAYA OPR DISTRIBUSI</th>
    <th colspan="2" width="12">BIAYA UMUM & ADMINISTRASI</th>
    <th colspan="2" width="12">RUPA - RUPA</th>
  </tr>
  <tr>
    <th width="6">USAHA</th>
    <th width="6">NON USAHA</th>
    <th width="6">BIAYA KANTOR</th>
    <th width="6">BIAYA HUBLANG</th>
    <th width="6">KODE</th>
    <th width="6">JUMLAH</th>
  </tr>
  </thead>
  <?php
    $tot_k_usaha = $tot_k_nonUsaha = $tod_d_kimia = $tot_d_installasi = $tot_d_umk = $tot_d_sumber = $tot_d_pengolahan = 0;
    $tot_d_distribusi = $tot_d_kantor = $tot_d_hublang = $tot_d_nom_rupa = 0;
  ?>
  @foreach($data_jurnal as $j)
    <tr>
        <td nowrap class="withborder">{{date('d-m',strtotime($j->tgl_vcr))}}</td>
        <td class="withborder">{{$j->no_vcr}}</td>
        <td class="withborder">{{$j->uraian}}</td>
        <td class="withborder" nowrap>{{($j->bayar==1 ? date('d-m',strtotime($j->tgl_cheq)) : '')}}</td>
        <td class="withborder">{{$j->no_cheq}}</td>
        <td class="withborder" align="right">{{($j->k_usaha==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="right">{{($j->k_nonUsaha==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="right">{{($j->d_kimia==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="right">{{($j->d_installasi==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="right">{{($j->d_umk==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="right">{{($j->d_sumber==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="right">{{($j->d_pengolahan==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="right">{{($j->d_distribusi==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="right">{{($j->d_kantor==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="right">{{($j->d_hublang==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
        <td class="withborder" align="left">{{$j->d_kode_rupa}}</td>
        <td class="withborder" align="right">{{($j->d_nom_rupa==0 ? '' : number_format($j->k_usaha,2,",","."))}}</td>
    </tr>
    <?php 
        $tot_k_usaha += $j->k_usaha;
        $tot_k_nonUsaha += $j->k_nonUsaha;
        $tod_d_kimia += $j->d_kimia;
        $tot_d_installasi += $j->d_installasi;
        $tot_d_umk += $j->d_umk;
        $tot_d_sumber += $j->d_sumber;
        $tot_d_pengolahan += $j->d_pengolahan;
        $tot_d_distribusi += $j->d_distribusi;
        $tot_d_kantor += $j->d_kantor;
        $tot_d_hublang += $j->d_hublang;
        $tot_d_nom_rupa += $j->d_nom_rupa;
    ?>
  @endforeach
    <tr>
        <td class="withborder colorfulltd" align="center" colspan="5"><strong>J U M L A H</strong></td>
        <td class="withborder colorfulltd" align="right">{{($tot_k_usaha==0 ? '' : number_format($tot_k_usaha,2,",","."))}}</td>
        <td class="withborder colorfulltd" align="right">{{($tot_k_nonUsaha==0 ? '' : number_format($tot_k_nonUsaha,2,",","."))}}</td>
        <td class="withborder colorfulltd" align="right">{{($tod_d_kimia==0 ? '' : number_format($tod_d_kimia,2,",","."))}}</td>
        <td class="withborder colorfulltd" align="right">{{($tot_d_installasi==0 ? '' : number_format($tot_d_installasi,2,",","."))}}</td>
        <td class="withborder colorfulltd" align="right">{{($tot_d_umk==0 ? '' : number_format($tot_d_umk,2,",","."))}}</td>
        <td class="withborder colorfulltd" align="right">{{($tot_d_sumber==0 ? '' : number_format($tot_d_sumber,2,",","."))}}</td>
        <td class="withborder colorfulltd" align="right">{{($tot_d_pengolahan==0 ? '' : number_format($tot_d_pengolahan,2,",","."))}}</td>
        <td class="withborder colorfulltd" align="right">{{($tot_d_distribusi==0 ? '' : number_format($tot_d_distribusi,2,",","."))}}</td>
        <td class="withborder colorfulltd" align="right">{{($tot_d_kantor==0 ? '' : number_format($tot_d_kantor,2,",","."))}}</td>
        <td class="withborder colorfulltd" align="right">{{($tot_d_hublang==0 ? '' : number_format($tot_d_hublang,2,",","."))}}</td>
        <td class="withborder colorfulltd">&nbsp;</td>
        <td class="withborder colorfulltd" align="right">{{($tot_d_nom_rupa==0 ? '' : number_format($tot_d_nom_rupa,2,",","."))}}</td>
    </tr>
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
 font-size: 7pt;
 padding: 5px;
 border-left: 1px solid black;
 border-right: 1px solid black;
 border-top: none;
 border-bottom: none;
 vertical-align: top;
}

td.withborder{
 font-size: 7pt;
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
</style>
@endpush
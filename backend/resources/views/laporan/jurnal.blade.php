@extends('layouts.simple')

@section('maincontent')

@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>R E K A P &nbsp;&nbsp;J U R N A L</u></span></td>  
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
        <th style= "width: 8%; border-bottom-width:0.1px;"><strong>TGL</strong></th>
        <th style= "width: 36%; border-bottom-width:0.1px;"><strong>U R A I A N</strong></th>
        <th style= "width: 10%; border-bottom-width:0.1px;"><strong>Kode Perk.</strong></th>
        <th style= "width: 6%; border-bottom-width:0.1px;"><strong>Unit</strong></th>
        <th style= "width: 20%; border-bottom-width:0.1px;"><strong>Debet</strong></th>
        <th style= "width: 20%; border-bottom-width:0.1px;"><strong>Kredit</strong></th>
    </tr>
    </thead>  
  <?php
    $total_deb = 0;
    $total_kre = 0;
    $cur_ref = '';
    $prev_ref = '';
  ?>
  @foreach($data_jurnal as $ju)
  <?php
    $cur_ref = $ju->referensi;
  ?>
  @if($cur_ref <> $prev_ref)
  <tr>
    <td align="center" class="withborder">{{date('d / m',strtotime($ju->tanggal))}}</td>
    <td colspan="5" class="withborder">{{$ju->referensi}}&nbsp;&nbsp;{{$ju->uraian}}</td>
  </tr>
  @endif
  <tr>
    <td>&nbsp;</td>
    @if($ju->debet==0)  
    <td> &nbsp; &nbsp; {{$ju->nama_perk}}</td>
    @else
    <td> {{$ju->nama_perk}}</td>
    @endif
    <td align="center">{{$ju->kode}}</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;{{($ju->debet==0 ? '' : number_format($ju->debet,2,",","."))}}</td>
    <td align="right">&nbsp;{{($ju->kredit==0 ? '' : number_format($ju->kredit,2,",","."))}}</td>
  </tr>
  <?php
    $total_deb = $total_deb + $ju->debet;
    $total_kre = $total_kre + $ju->kredit;
    $prev_ref = $ju->referensi;
  ?>
  @endforeach
  <tr>
    <td align="center" colspan="2" class="withborder colorfulltd"><strong>JUMLAH</strong></td>
    <td class="withborder colorfulltd">&nbsp;</td>
    <td class="withborder colorfulltd">&nbsp;</td>
    <td align="right" class="withborder colorfulltd"><strong>{{number_format($total_deb,2,",",".")}}</strong></td>
    <td align="right" class="withborder colorfulltd"><strong>{{number_format($total_kre,2,",",".")}}</strong></td>
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
 font-size: 9pt;
 padding: 5px;
 border-left: 1px solid black;
 border-right: 1px solid black;
 border-top: none;
 border-bottom: none;
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
 padding: 5px;
}
.colorfulltd{
    background-color: darksalmon
}
</style>
@endpush
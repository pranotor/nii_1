@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>WORK SHEET</u></span></td>  
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
    <th style= "width: 25%; border-bottom-width:0.1px;" rowspan="2"><strong>NAMA PERKIRAAN</strong></th>
    <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>KODE</strong></th>
    <th style= "width: 14%; border-bottom-width:0.1px;" colspan="2"><strong>Neraca Awal</strong></th>
    <th style= "width: 14%; border-bottom-width:0.1px;" colspan="2"><strong>Mutasi</strong></th>
    <th style= "width: 14%; border-bottom-width:0.1px;" colspan="2"><strong>Saldo</strong></th>
    <th style= "width: 14%; border-bottom-width:0.1px;" colspan="2"><strong>Laba Rugi</strong></th>
    <th style= "width: 14%; border-bottom-width:0.1px;" colspan="2"><strong>Neraca Akhir</strong></th>
  </tr>
  <tr>
    <th width="7%" align="center">DEBET</th>
    <th width="7%" align="center">KREDIT</th>
    
    <th width="7%" align="center">DEBET</th>
    <th width="7%" align="center">KREDIT</th>
    
    <th width="7%" align="center">DEBET</th>
    <th width="7%" align="center">KREDIT</th>
    
    <th width="7%" align="center">DEBET</th>
    <th width="7%" align="center">KREDIT</th>
    
    <th width="7%" align="center">DEBET</th>
    <th width="7%" align="center">KREDIT</th>
  </tr>
  </thead>
  <?php
    $total = 0;
  ?>
  @foreach($data_jurnal as $j)
  
  <tr>
    @if($j->level == 1)
    <td width="25%"> &nbsp; &nbsp; <strong>{{$j->nama_perk}}</strong></td>
    <td width="5%"> &nbsp;</td>
    @elseif ($j->level > 2 && $j->level < 100)
    <td width="25%"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{$j->nama_perk}}</td>
    <td width="5%" align="center"> {{$j->kode_perk}}</td>
    @else
    <td width="25%"> &nbsp; <strong>{{$j->nama_perk}}</strong></td>
    <td width="5%"> &nbsp;</td>
    @endif
    
    <?php
        $kode_awal = substr($j->kode_perk,0,1);

        $awal_debet = $j->awal_debet;
        $awal_kredit = $j->awal_kredit;
        $akhir_debet = $j->debet;
        $akhir_kredit = $j->kredit;
        $mutasi_debet = $akhir_debet - $awal_debet;
        $mutasi_kredit = $akhir_kredit - $awal_kredit;

        if($kode_awal < 8) {
			  $debet_lr = "0";
			  $kredit_lr = "0";
			  $debet_bs = $akhir_debet;
			  $kredit_bs = $akhir_kredit;
        }
        else{
            $debet_lr  = $akhir_debet;
            $kredit_lr = $akhir_kredit;
            $debet_bs  = "0";
            $kredit_bs = "0";
        }

        if($j->level == 100)
            $str_class = "withborder";
        else 
            $str_class = "";
    ?>
    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($awal_debet==0 ? '' : number_format($awal_debet,2,",","."))}}</td>
    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($awal_kredit==0 ? '' : number_format($awal_kredit,2,",","."))}}</td>
    
    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($mutasi_debet==0 ? '' : number_format($mutasi_debet,2,",","."))}}</td>
    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($mutasi_kredit==0 ? '' : number_format($mutasi_kredit,2,",","."))}}</td>
    
    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($akhir_debet==0 ? '' : number_format($akhir_debet,2,",","."))}}</td>
    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($akhir_kredit==0 ? '' : number_format($akhir_kredit,2,",","."))}}</td>

    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($debet_lr==0 ? '' : number_format($debet_lr,2,",","."))}}</td>
    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($kredit_lr==0 ? '' : number_format($kredit_lr,2,",","."))}}</td>

    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($debet_bs==0 ? '' : number_format($debet_bs,2,",","."))}}</td>
    <td width="7%" align="right" class="{{$str_class}}">&nbsp;{{($kredit_bs==0 ? '' : number_format($kredit_bs,2,",","."))}}</td>
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
 font-size: 8pt;
 padding: 5px;
 border-left: 1px solid black;
 border-right: 1px solid black;
 border-top: none;
 border-bottom: none;
}

td.withborder{
 font-size: 8pt;
 padding: 5px;
 border-left: 1px solid black;
 border-right: 1px solid black;
 border-top: 1px solid black;;
 border-bottom: 1px solid black;
 font-weight: bold !important;
}

.noborder td{
 border-top: none;
 border-bottom: none;
 border-left: none;
 border-right: none;
 font-size: 9pt;
 
 padding: 5px;
}
</style>
@endpush
@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr class="noborder">
        <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>LABA / RUGI</u></span></td>  
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
    <th style= "width: 44%; border-bottom-width:0.1px;" rowspan="2"><strong>URAIAN</strong></th>
    <th style= "width: 17%; border-bottom-width:0.1px;" rowspan="2"><strong>{{$year}} </strong></th>
    <th style= "width: 17%; border-bottom-width:0.1px;" rowspan="2"><strong>{{$year-1}} </strong></th>
    <th style= "width: 22%; border-bottom-width:0.1px;" colspan="2"><strong>NAIK (TURUN)</strong></th>
    </tr>
	<tr>
	  <th width="17%" align="center">Jumlah</th>
	  <th width="5%" align="center">%</th>
	</tr>
  </tr>
  </thead>
  <?php
    $total = 0;
  ?>
  @foreach($data_jurnal as $j)
  <?php 
    $kode_awal = substr($j->kode_perk,0,1);
  ?>
  <tr>
    @if($j->level == 1)
    <td width="44%"> &nbsp; &nbsp; &nbsp; &nbsp; <strong>{{$j->nama_perk}}</strong></td>
    @elseif ($j->level > 2 && $j->level < 100)
    <td width="44%"> &nbsp; {{$j->kode_perk}} &nbsp; &nbsp; {{$j->nama_perk}}</td>
    @else
    <td width="44%"> &nbsp;  <strong>{{$j->nama_perk}}</strong></td>
    @endif
   
    <?php
        
        if($kode_awal < 5 || $kode_awal==9){
            $saldo0 =  $j->awal_debet - $j->awal_kredit;
            $saldo =  $j->debet - $j->kredit;
            $naik = $saldo - $saldo0;
            if($saldo0 > 0)
                $percen = $naik / $saldo0 *100;
            else
                $percen = $naik*100;
        }
        else{
            $saldo0 =  $j->awal_kredit - $j->awal_debet;
            $saldo =  $j->kredit - $j->debet;
            $naik = $saldo - $saldo0;
            if($saldo0 > 0)
                $percen = $naik / $saldo0 *100;
            else
                $percen = $naik*100;
        }

        if($j->level >= 100){
            $str_class = "withborder";
            if($j->level == 200)
                $str_class .= " colorfulltd1";
            if($j->level == 250)
                $str_class .= " colorfulltd2";
        }
        else 
            $str_class = "";

        if($saldo0 == '0')
            $saldo0 = "";
        else
            $saldo0 = ($saldo0 < 0 ? "(".number_format(abs($saldo0),2).")" : number_format($saldo0,2));
        
        if($saldo == '0')
            $saldo = "";
        else
            $saldo = ($saldo < 0 ? "(".number_format(abs($saldo),2).")" : number_format($saldo,2));    
            
    ?>
    <td width="17%" align="right" class="{{$str_class}}">&nbsp;{{$saldo}}</td>
    <td width="17%" align="right" class="{{$str_class}}">&nbsp;{{$saldo0}}</td>
    <td width="17%" align="right" class="{{$str_class}}">&nbsp;{{($naik==0 ? '' : number_format($naik,2,",","."))}}</td>
    <td width="5%" align="right" class="{{$str_class}}">&nbsp;{{($percen==0 ? '' : number_format($percen,2,",","."))}}</td>
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
 font-size: 9pt;
 padding: 5px;
 border-left: 1px solid black;
 border-right: 1px solid black;
 border-top: 1px solid black;;
 border-bottom: 1px solid black;
}

.colorfulltd{
    background-color: darksalmon
}

.noborder td{
 border-top: none;
 border-bottom: none;
 border-left: none;
 border-right: none;
 font-size: 9pt;
 padding: 5px;
}
.colorfulltd1{
    background-color: darksalmon
}

.colorfulltd2{
    background-color:greenyellow
}
</style>
@endpush
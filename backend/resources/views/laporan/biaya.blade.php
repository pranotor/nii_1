@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>B I A Y A</u></span></td>  
  </tr>  
  <tr class="noborder">
    @if($periode=='bl')
    <td align="center">Bulan {{$monthName}} / {{$year}}</td>
    @else
    <td align="center">Tahun {{$year}}</td>
    @endif
  </tr>
</table>
<table width="100%" border="1" cellpadding="5" cellspacing="0">
  <thead>
  <tr>
    <th style= "width: 38%; border-bottom-width:0.1px;" colspan="4"><strong>BULAN INI</strong></th>  
    <th style= "width: 24%; border-bottom-width:0.1px;" rowspan="2"><strong>U R A I A N</strong></th>
    <th style= "width: 38%; border-bottom-width:0.1px;" colspan="4"><strong>SAMPAI DENGAN BULAN INI</strong></th>
  </tr>
  <tr>
    <th width="10%" align="center">REALISASI</th>
    <th width="10%" align="center">ANGGARAN</th>
    <th width="10%" align="center">LEBIH / KURANG</th>
    <th width="8%" align="center">%</th>
    
    <th width="10%" align="center">REALISASI</th>
    <th width="10%" align="center">ANGGARAN</th>
    <th width="10%" align="center">LEBIH / KURANG</th>
    <th width="8%" align="center">%</th>
  </tr>
  </thead>
  @foreach($data_jurnal as $j)
  <?php
        $kode_awal = substr($j->kode_perk,0,1);

        if($kode_awal==9){
            $saldo0 =  $j->awal_debet - $j->awal_kredit;
            $anggaran0 = $j->anggaran_awal;
            $saldo =  $j->debet - $j->kredit;
        }
        else{
            $saldo0 =  $j->awal_kredit - $j->awal_debet;
            $anggaran0 = $j->anggaran_awal;
            $saldo =  $j->kredit - $j->debet;
        }
        $anggaran = $j->anggaran;
        $naik0 = $saldo0 - $anggaran0;
        $naik = $saldo - $anggaran;

        if($anggaran0 ==0 && $saldo0==0){
            $percen0 = "";
        }
        else{
            if($anggaran0 > 0)
                $percen0 = abs($naik0) / $anggaran0 *100;
            else
                $percen0 = "-";
        }
        if($anggaran ==0 && $saldo==0){
            $percen = "";
        }
        else{
            if($anggaran > 0)
                $percen = abs($naik) / $anggaran *100;
            else
                $percen = "-"; 
        }
        

        if($j->level == 100)
            $str_class = "withborder";
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
        
        if($anggaran == '0')
            $anggaran = "";
        else
            $anggaran = ($anggaran < 0 ? "(".number_format(abs($anggaran),2).")" : number_format($anggaran,2));
            
        if($anggaran0 == '0')
            $anggaran0 = "";
        else
            $anggaran0 = ($anggaran0 < 0 ? "(".number_format(abs($anggaran0),2).")" : number_format($anggaran0,2));
        
        if($naik0 == '0')
            $naik0 = "";
        else
            $naik0 = ($naik0 < 0 ? "(".number_format(abs($naik0),2).")" : number_format($naik0,2));

        if($naik == '0')
            $naik = "";
        else
            $naik = ($naik < 0 ? "(".number_format(abs($naik),2).")" : number_format($naik,2));
        
    ?>
  @if($kode_awal > 8)  
  <tr>
    <td width="10%" align="right" class="{{$str_class}}">&nbsp;{{$saldo0}}</td>
    <td width="10%" align="right" class="{{$str_class}}">&nbsp;{{$anggaran0}}</td>
    <td width="10%" align="right" class="{{$str_class}}">&nbsp;{{$naik0}}</td>
    <td width="8%" align="right" class="{{$str_class}}">&nbsp;{{$percen0}}</td>
    

    @if($j->level == 1)
    <td width="20%" > &nbsp; &nbsp; <strong>{{$j->nama_perk}}</strong></td>
    @elseif ($j->level > 2 && $j->level < 100)
    <td width="20%" > &nbsp; {{$j->kode_perk}} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{$j->nama_perk}}</td>
    @else
    <td width="20%" class="{{$str_class}}""> &nbsp; <strong>{{$j->nama_perk}}</strong></td>
    @endif
    
    
    <td width="10%" align="right" class="{{$str_class}}">&nbsp;{{$saldo}}</td>
    <td width="10%" align="right" class="{{$str_class}}">&nbsp;{{$anggaran}}</td>
    <td width="10%" align="right" class="{{$str_class}}">&nbsp;{{$naik}}</td>
    <td width="8%" align="right" class="{{$str_class}}">&nbsp;{{$percen}}</td>
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
 font-weight: bold !important;
 padding: 5px;
}
</style>
@endpush
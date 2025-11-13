@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>NERACA SALDO</u></span></td>  
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
    <th style= "width: 46%; border-bottom-width:0.1px;"><strong>NAMA PERKIRAAN</strong></th>
    <th style= "width: 18%; border-bottom-width:0.1px;"><strong>Debet</strong></th>
    <th style= "width: 18%; border-bottom-width:0.1px;"><strong>Kredit</strong></th>
    <th style= "width: 18%; border-bottom-width:0.1px;"><strong>Saldo</strong></th>
  </tr>
  </thead>
  <?php
    $total = 0;
  ?>
  @foreach($data_jurnal as $j)
  <tr>
    @if($j->level == 1)
    <td width="46%"> &nbsp; &nbsp; &nbsp; &nbsp; <strong>{{$j->nama_perk}}</strong></td>
    @elseif ($j->level > 2 && $j->level < 100)
    <td width="46%"> &nbsp; {{$j->kode_perk}} &nbsp; &nbsp; {{$j->nama_perk}}</td>
    @else
    <td width="46%"> &nbsp;  <strong>{{$j->nama_perk}}</strong></td>
    @endif
    <?php 
        $kode_depan = Str::substr($j->kode_perk, 0, 1);
        if($kode_depan >4 && $kode_depan < 9)
            $saldo = $j->kredit - $j->debet;
        else {
            $saldo = $j->debet - $j->kredit;
        }

        if($j->level == 100)
            $str_class = "withborder";
        else 
            $str_class = "";
    ?>
    <td width="18%" align="right" class="{{$str_class}}">&nbsp;{{($j->debet==0 ? '' : number_format($j->debet,2,",","."))}}</td>
    <td width="18%" align="right" class="{{$str_class}}">&nbsp;{{($j->kredit==0 ? '' : number_format($j->kredit,2,",","."))}}</td>
    <td width="18%" align="right" class="{{$str_class}}">&nbsp;{{($saldo==0 ? '' : number_format($saldo,2,",","."))}}</td>
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
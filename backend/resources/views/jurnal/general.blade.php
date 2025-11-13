@extends('layouts.simple')

@section('maincontent')

<table width="100%" border="0" cellpadding="2" cellspacing="2">
    <tr class="noborder">
      <td width="71%" rowspan="2" align="center">
          <span style="font-size: 20pt">&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; <u>B U K T I &nbsp;&nbsp; J U R N A L</u></span>
      </td>
      <td width="8%" align="left">Nomor</td>
      <td width="3%" align="center">:</td>
      <td width="18%" align="left">{{$jurnalH['referensi']}}</td>
    </tr>  
    <tr class="noborder">
      <td align="left">Tanggal</td>
      <td align="center">:</td>
      <td align="left">{{\Carbon\Carbon::parse($jurnalH['tanggal'])->translatedFormat('j F Y')}}</td>
    </tr>
  </table>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
   <tr>
    <td colspan="5" align="left"><h3>Jenis Jurnal : {{$jurnalH->jenisJurnal->uraian}}</h3></td>
   </tr>
  
  <tr class="perkiraan">
    <td width="44%" align="center"><strong>NAMA PERKIRAAN</strong></td>
    <td width="10%" align="center"><strong>Kode Perk.</strong></td>
    <td width="6%" align="center"><strong>Unit</strong></td>
    <td width="20%" align="center"><strong>Debet</strong></td>
    <td width="20%" align="center"><strong>Kredit</strong></td>
  </tr>
  <?php
    $total_debet = 0;
    $total_kredit = 0;
  ?>
  @foreach($jurnalD as $ju)
  @if($ju->debet != 0)
  <tr class="noborderperk">
    @if($ju->debet==0)  
    <td> &nbsp; &nbsp; &nbsp;{{$ju->getCoa()->nama_perk}}</td>
    @else
    <td> &nbsp;{{$ju->getCoa()->nama_perk}}</td>
    @endif
    <td align="center">{{$ju->kode}}</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;{{($ju->debet==0 ? '' : number_format($ju->debet,2,",","."))}}</td>
    <td align="right">&nbsp;{{($ju->kredit==0 ? '' : number_format($ju->kredit,2,",","."))}}</td>
  </tr>
  <?php
    $total_debet = $total_debet + $ju->debet;
  ?>
  @endif
  @endforeach
  @foreach($jurnalD as $ju)
  @if($ju->kredit != 0)
  <tr class="noborderperk">
    <td> &nbsp; &nbsp; &nbsp;{{$ju->getCoa()->nama_perk}}</td>
    <td align="center">{{$ju->kode}}</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;{{($ju->debet==0 ? '' : number_format($ju->debet,2,",","."))}}</td>
    <td align="right">&nbsp;{{($ju->kredit==0 ? '' : number_format($ju->kredit,2,",","."))}}</td>
  </tr>
  <?php
    $total_kredit = $total_kredit + $ju->kredit;
  ?>
  @endif
  @endforeach
 
  <tr>
    <td align="center"><strong>JUMLAH</strong></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right"><strong>{{number_format($total_debet,2,",",".")}}</strong></td>
    <td align="right"><strong>{{number_format($total_kredit,2,",",".")}}</strong></td>
  </tr>
  <tr>
    <td height="30" colspan="5">
        <table cellpadding="0" cellspacing="0" border="0">
            <tr class="nobordersub">  
                <td height="30" valign="top" width="12%">Terbilang</td>
                <td width="3%">:</td>
                <td width="84%">{{terbilang($total_debet)}} Rupiah</td>
            </tr>
        </table>
         
    </td>
  </tr>
  <tr>
    <td colspan="5">
    <table cellpadding="0" cellspacing="0" border="0">
        <tr class="nobordersub">  
            <td height="30" valign="top" width="12%">Penjelasan</td>
            <td width="3%">:</td>
            <td width="84%">{{$jurnalH->uraian}}</td>
        </tr>
    </table>
    </td>
  </tr>
</table>

<x-ttd-jurnal :mod-name="$modname" :jenis="$jenis"/>
@stop

@push('head')
<style>

    table th{
     text-align: center !important;
    }
    th{
     text-align: center !important;
     border-bottom: 0.2px solid black;
     font-size: 10pt;
     font-weight: bold !important;
     padding: 5px;
    }
    td{
     text-align: left !important;
     border-top: 0.2px solid black;
     border-bottom: 0.2px solid black;
     border-left: 0.2px solid black;
     border-right: 0.2px solid black;
     /*border-left: 1px solid black;*/
     /*border-right: 1px solid black;*/
     font-size: 10pt;
     /*font-weight: bold !important;*/
     padding: 5px;
    }
    
    .perkiraan td{
     text-align: left !important;
     border-top: 0.2px solid black;
     border-bottom: 0.2px solid black;
     border-left: 0.2px solid black;
     border-right: 0.2px solid black;
     font-size: 10pt;
     padding: 5px;
    }
    
    .noborderperk td{
     text-align: left !important;
     border-top: none;
     border-bottom: none;
     border-left: 0.2px solid black;
     border-right: 0.2px solid black;
     font-size: 10pt;
     /*font-weight: bold !important;*/
     padding: 2px;
    }
    
    .noborder td{
     text-align: left !important;
     border-top: none;
     border-bottom: none;
     border-left: none;
     border-right: none;
     font-size: 10pt;
     
     padding: 5px;
    }
    
    .nobordersub td{
     text-align: left !important;
     border-top: none;
     border-bottom: none;
     border-left: none;
     border-right: none;
     font-size: 10pt;
     
     padding: 5px;
    }
    </style>
@endpush
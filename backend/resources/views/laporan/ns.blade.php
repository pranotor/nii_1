@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="60%" rowspan="2" align="right"><h1><u>NERACA SALDO</u></h1></td>
    <td width="20%" align="right">&nbsp;</td>
    <td width="20%" align="left">&nbsp;</td>
  </tr>  
  <tr class="noborder">
    <td align="right">&nbsp;</td>
    <td align="left">&nbsp;</td>
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
  @if($j['level'] != 2)
  <tr>
    @if($j['level'] == 1)
    <td width="46%"> &nbsp; &nbsp; &nbsp; &nbsp; <strong>{{$j['nama']}}</strong></td>
    @elseif ($j['level'] > 2)
    <td width="46%"> &nbsp; {{$j['kode']}} &nbsp; &nbsp; {{$j['nama']}}</td>
    @else
    <td width="46%"> &nbsp;  <strong>{{$j['nama']}}</strong></td>
    @endif
    @if(!empty($j['jurnal']))
    <?php
        $saldo =  $j['jurnal'][0]->tot_debet - $j['jurnal'][0]->tot_kredit;
    ?>
    <td width="18%" align="right">&nbsp;{{($j['jurnal'][0]->tot_debet==0 ? '' : number_format($j['jurnal'][0]->tot_debet,2,",","."))}}</td>
    <td width="18%" align="right">&nbsp;{{($j['jurnal'][0]->tot_kredit==0 ? '' : number_format($j['jurnal'][0]->tot_kredit,2,",","."))}}</td>
    <td width="18%" align="right">&nbsp;{{($saldo==0 ? '' : number_format($saldo,2,",","."))}}</td>
    @else
    <td width="18%" align="right">&nbsp;</td>
    <td width="18%" align="right">&nbsp;</td>
    <td width="18%" align="right">&nbsp;</td>
    @endif
  </tr>
  @endif
  @endforeach
  
</table>


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
@extends('layouts.simple')

@section('maincontent')


<table width="100%" border="0" cellpadding="2" cellspacing="0">
  <tr class="noborder">
    <td width="5%" align="center" valign="middle"></td>
    <td width="12%" align="center"></td>
    <td width="25%" align="center"></td>

    <td width="23%" colspan="2" style= "border-top-width:0.1px; border-top-color:black; border-left-width:0.1px solid; border-left-color:black; border-right-width:0.1px solid; border-right-color:black"><strong>PURCHASE ORDER</strong></td>
    <td width="7%" align="center"></td>
    <td width="10%" align="center"></td>
    <td width="14%" align="center"></td>
  </tr>
  <tr class="perkiraan">
    <td width="5%" align="left" valign="middle">To</td>
    <td colspan="2" width="56%"><strong>{{$jurnalH->supplier->nama}}</strong></td>
    <td width="9%" align="left">Date</td>
    <td colspan="4" width="30%"><strong>{{\Carbon\Carbon::parse($jurnalH['qt_tgl'])->translatedFormat('l, F j, Y')}}</strong></td>

  </tr>
  <tr class="perkiraan">
    <td align="left">Add</td>
    <td colspan="4" width="95%"><strong>{{$jurnalH->supplier->alamat}}</strong></td>
  </tr>


  <tr class="perkiraan">
    <td width="5%" align="center" style= "border-top-width:2px"><strong>Items</strong></td>
    <td width="30%" align="center" style= "border-top-width:2px"><strong>Product No</strong></td>
    <td width="43%" align="center" style= "border-top-width:2px"><strong>Description</strong></td>
    <td width="10%" align="center" style= "border-top-width:2px"><strong>Qty</strong></td>
    <td width="12%" align="center" style= "border-top-width:2px"><strong>Unit</strong></td>
  </tr>
  <?php
  $i = 1;
  $subtotal = 0;
  ?>
  @foreach($jurnalD as $ju)
  <?php
    $netto = $ju->harga - ($ju->harga * $ju->discount/100);
    $total_i = $ju->qty_pesan * $netto;

  ?>
    <tr>
        <td align="right">{{$i}}</td>
        @if($ju->kode2 != '')
        <td>{{$ju->kode2}}</td>
        @else
        <td>{{$ju->kode_barang}}</td>
        @endif
        <td>@if($ju->nama_barang2 == ''){{$ju->itembarang->itemperkiraan->nama_perk}}@else{{$ju->nama_barang2}}@endif
        @if($ju->size != '')
        <br/>
        {!! nl2br(e($ju->size)) !!}
        <br/>
        @endif
        </td>
        <td align="right">{{number_format($ju->qty_pesan,2)}}</td>
        <td>{{$ju->itembarang->satuan}}</td>


    </tr>
    <?php
        $i++;
        $subtotal = $subtotal + $total_i;
    ?>
  @endforeach
  <tr>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
  </tr>

</table>


@stop

@push('head')
<style>

table th{
 text-align: center !important;
}
th{
 text-align: center !important;
 border-bottom: 0.2px solid black;
 font-size: 11pt;
 font-weight: bold !important;
 padding: 5px;
}
td{
 text-align: left !important;

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
 font-size: 8pt;
 padding: 5px;
}

.noborderperk td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 font-size: 11pt;
 /*font-weight: bold !important;*/
 padding: 2px;
}

.noborder td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: none;
 border-right: none;
 font-size: 8pt;

 padding: 5px;
}

.nobordersub td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: none;
 border-right: none;
 font-size: 11pt;

 padding: 5px;
}
</style>
@endpush

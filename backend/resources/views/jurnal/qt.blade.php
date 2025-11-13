@extends('layouts.simple')

@section('maincontent')


<table width="100%" border="0" cellpadding="2" cellspacing="0">
  <tr class="noborder">
    <td width="5%" align="center" valign="middle"></td>
    <td width="12%" align="center"></td>
    <td width="25%" align="center"></td>
    @if($jurnalH->posting == 0)
    <td width="23%" colspan="2" style= "border-top-width:0.1px; border-top-color:black; border-left-width:0.1px solid; border-left-color:black; border-right-width:0.1px solid; border-right-color:black"><strong>Price Quotation</strong></td>
    @else
    <td width="19%" colspan="2" style= "border-top-width:0.1px; border-top-color:black; border-left-width:0.1px solid; border-left-color:black; border-right-width:0.1px solid; border-right-color:black"><strong>Sales Order</strong></td>
    @endif
    <td width="7%" align="center"></td>
    <td width="10%" align="center"></td>
    <td width="14%" align="center"></td>
  </tr>
  <tr class="perkiraan">
    <td width="5%" align="left" valign="middle">To</td>
    <td colspan="2" width="37%"><strong>{{$jurnalH->qcustomer->nama}}</strong></td>
    <td width="9%" align="left">Date</td>
    <td colspan="4" width="49%"><strong>{{\Carbon\Carbon::parse($jurnalH['qt_tgl'])->translatedFormat('l, F j, Y')}}</strong></td>

  </tr>
  <tr class="perkiraan">
    <td align="left" rowspan="3">Add</td>
    <td rowspan="3" width="37%"><strong>{{$jurnalH->qcustomer->alamat}}</strong></td>
    <td align="left" width="9%">Payment</td>
    <td colspan="4" width="49%"><strong>{{$jurnalH->payment}}</strong></td>
  </tr>
  <tr class="perkiraan">
    <td align="left" width="9%">Currency</td>
    <td colspan="4" width="49%"><strong>Rp</strong></td>
  </tr>
  <tr class="perkiraan">
    <td align="left" width="9%">Lead Time</td>
    <td colspan="4" width="49%"><strong>{{$jurnalH->leadtime}}</strong></td>
  </tr>
  <tr class="perkiraan">
    <td width="5%" align="left" valign="middle">Tel</td>
    <td width="37%"><strong>{{$jurnalH->qcustomer->telepon}}</strong></td>
    <td width="9%" align="left">Validity</td>
    <td colspan="4" width="49%"><strong>{{$jurnalH->validity}} Days</strong></td>
  </tr>
  <tr class="perkiraan">
    <td width="5%" align="left" valign="middle">Attn</td>
    <td width="37%"><strong>{{$jurnalH->qcustomer->kontak_person}}</strong></td>
    <td width="9%" align="left">quote By</td>
    <td colspan="4" width="49%"><strong>{{$jurnalH->sales->nama}}</strong></td>
  </tr>
  <tr class="perkiraan">
    <td width="5%" align="center" style= "border-top-width:2px"><strong>Items</strong></td>
    <td width="12%" align="center" style= "border-top-width:2px"><strong>Product No</strong></td>
    <td width="25%" align="center" style= "border-top-width:2px"><strong>Description</strong></td>
    <td width="9%" align="center" style= "border-top-width:2px"><strong>Qty / Unit</strong></td>
    <td width="14%" align="center" style= "border-top-width:2px"><strong>Unit Price</strong></td>
    <td width="7%" align="center" style= "border-top-width:2px"><strong>Dics %</strong></td>
    <td width="14%" align="center" style= "border-top-width:2px"><strong>Netto</strong></td>
    <td width="14%" align="center" style= "border-top-width:2px"><strong>Total</strong></td>
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
        <td>@if($ju->nama_barang2 == ''){{$ju->itembarang->perkiraan->nama_perk}}@else{{$ju->nama_barang2}}@endif
        @if($ju->size != '')
        <br/>
        {!! nl2br(e($ju->size)) !!}
        <br/>
        @endif
        </td>
        <td>{{number_format($ju->qty_pesan,0,',','.')}} / {{$ju->satuan}}</td>
        <td align="right">{{number_format($ju->harga,2,',','.')}}</td>
        <td>{{$ju->discount}}</td>
        <td align="right">{{number_format($netto,2,',','.')}}</td>
        <td align="right">{{number_format($total_i,0,',','.')}}</td>

    </tr>
    <?php
        $i++;
        $subtotal = $subtotal + $total_i;
    ?>
  @endforeach
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td align="center" colspan="3" style= "border-bottom-width:2px; border-top-width:2px">SUBTOTAL</td>
    <td align="right" style= "border-bottom-width:2px; border-top-width:2px">{{number_format($subtotal,0,',','.')}}</td>
  </tr>
  @if($jurnalH->p ==1)
  <tr>
    <td></td>
    <td></td>
    <td><br/>PPN {{$ppn * 100}}%</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td align="right"><br/>{{number_format($subtotal*$ppn,0,',','.')}}</td>
  </tr>
  <tr>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td align="center" colspan="2" style= "border-bottom-width:2px; border-top-width:2px">TOTAL</td>
    <td align="right" style= "border-bottom-width:2px; border-top-width:2px">{{number_format($subtotal + $subtotal*$ppn,0,',','.')}}</td>
  </tr>
  @else
  <tr>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td style= "border-bottom-width:2px;"></td>
    <td align="center" colspan="2" style= "border-bottom-width:2px; border-top-width:2px"></td>
    <td align="right" style= "border-bottom-width:2px; border-top-width:2px"></td>
  </tr>
  @endif
  <tr class="noborder">
    <td colspan="2" style= "border-left-width:0.1px; border-right-width:0.1px; border-bottom-width:0.1px">Terms & Condition</td>
    <td align="center" colspan="6" style= "border-left-width:0.1px; border-right-width:0.1px"></td>
  </tr>
  <tr class="noborder">
    <td width="100%" colspan="8" style= "border-left-width:0.1px; border-bottom-width:2px; border-right-width:0.1px"><ol><li>Above is quoted on Warehouse of {{$jurnalH->qcustomer->nama}}</li><li></li><li></li></ol></td>
  </tr>
  <tr class="noborder">
    <td colspan="2" style= "border-left-width:0.1px; border-right-width:0.1px; border-bottom-width:0.1px">Order Placement Instruction</td>
    <td align="left" colspan="6" style= "border-left-width:0.1px; border-right-width:0.1px; border-bottom-width:0.1px">Please issue PO to PT NUSA INTERNATIONAL - Bandung 40236 Indonesia</td>
  </tr>
  <tr class="noborder">
    <td colspan="2" style= "border-left-width:0.1px; border-right-width:0.1px; border-bottom-width:2px">Payment Instruction</td>
    <td align="left" colspan="6" style= "border-left-width:0.1px; border-right-width:0.1px; border-bottom-width:2px"></td>
  </tr>
  <tr class="noborder">
    <td colspan="2" style= "border-left-width:0.1px; border-right-width:0.1px; border-bottom-width:0.1px">Remark</td>
    <td align="left" colspan="6" style= "border-left-width:0.1px; border-right-width:0.1px; border-bottom-width:0.1px">Quo No {{$jurnalH->qt_no}}</td>
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

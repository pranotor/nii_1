@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>REKAP PEMBELIAN</u></span></td>
  </tr>
  <tr class="noborder">
    @if($periode=='bl')
    <td align="center"><b>Bulan {{$monthName}}</b></td>
    @elseif($periode=='th')
    <td align="center"><b>Tahun {{$year}}</b></td>
    @elseif($periode=='range')
    <td align="center"><b>Periode :  {{$str_tanggal}}</b></td>
    @endif
  </tr>
</table>

  <?php
    $init = 0;
    $grandtotal = 0;
    $counter = 0;
  ?>
  @if(!empty($data_jurnal))
  <table width="100%" border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th style= "width: 7%; border-bottom-width:0.1px;"><strong>Tgl</strong></th>
        <th style= "width: 10%; border-bottom-width:0.1px;"><strong>No PO</strong></th>
        <th style= "width: 10%; border-bottom-width:0.1px;"><strong>No BPB</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;"><strong>Supp</strong></th>
        <th style= "width: 17%; border-bottom-width:0.1px;"><strong>Kode</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;"><strong>Harga</strong></th>
        <th style= "width: 5%; border-bottom-width:0.1px;"><strong>Qty Pesan</strong></th>
        <th style= "width: 5%; border-bottom-width:0.1px;"><strong>Qty Terima</strong></th>
        <th style= "width: 5%; border-bottom-width:0.1px;"><strong>Harga</strong></th>
        <th style= "width: 5%; border-bottom-width:0.1px;"><strong>PPN</strong></th>
        <th style= "width: 5%; border-bottom-width:0.1px;"><strong>Duty cost</strong></th>
        <th style= "width: 5%; border-bottom-width:0.1px;"><strong>Freight cost</strong></th>
        <th style= "width: 10%; border-bottom-width:0.1px;"><strong>Total</strong></th>
    </tr>
    </thead>
    @foreach($data_jurnal as $j)
    <?php
    $subtotal = $j->qty_terima*($j->harga + $j->ppn + $j->duty_cost + $j->freight_cost);
    $grandtotal = $grandtotal + $subtotal;
    ?>
    <tr>
        <td  class="withborder" align="center">{{ \Carbon\Carbon::parse($j->bpb_tgl)->translatedFormat('d-m')}}</td>
        <td  class="withborder">{{$j->po_no}}</td>
        <td  class="withborder">{{$j->bpb_no}}</td>
        <td  class="withborder">{{$j->nama}}</td>
        <td  class="withborder">{{$j->kode_barang}}</td>
        <td  class="withborder">{{number_format($j->harga,2)}}</td>
        <td  class="withborder">{{$j->qty_pesan}}</td>
        <td  class="withborder">{{$j->qty_terima}}</td>
        <td  class="withborder">{{$j->harga}}</td>
        <td  class="withborder">{{$j->ppn}}</td>
        <td  class="withborder">{{$j->duty_cost}}</td>
        <td  class="withborder">{{$j->freight_cost}}</td>
        <td  class="withborder" align="right">{{number_format($subtotal,2)}}</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="12" align="center" class="withborder">J U M L A H</td>
        <td align="right" class="withborder">{{number_format($grandtotal,2)}}</td>
    </tr>
    </table>
@endif

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
 font-size: 12pt;
 font-weight: bold !important;
 padding: 5px;
 /*background-color: cornflowerblue;*/
}

td{
 font-size: 12pt;
 padding: 5px;
 border-left: 1px solid black;
 border-right: 1px solid black;
 border-top: none;
 border-bottom: none;
}

td.withborder{
 font-size: 12pt;
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
 font-size: 12pt;
 padding: 5px;
}
</style>
@endpush

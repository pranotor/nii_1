@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>REKAP PENJUALAN</u></span></td>
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
        <th style= "width: 10%; border-bottom-width:0.1px;"><strong>No Fak</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;"><strong>Cust</strong></th>
        <th style= "width: 17%; border-bottom-width:0.1px;"><strong>Kode</strong></th>
        <th style= "width: 25%; border-bottom-width:0.1px;"><strong>Nama</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;"><strong>Harga</strong></th>

        <th style= "width: 5%; border-bottom-width:0.1px;"><strong>Qty</strong></th>
        <th style= "width: 5%; border-bottom-width:0.1px;"><strong>Sat</strong></th>
        <th style= "width: 7%; border-bottom-width:0.1px;"><strong>Pot 1</strong></th>
        <th style= "width: 10%; border-bottom-width:0.1px;"><strong>Total</strong></th>
    </tr>
    </thead>
    @foreach($data_jurnal as $j)
    <?php
    $jenis = $j->jenis;
    $subtotal = $j->qty_kirim*$j->harga*(1-($j->discount)/100);
    if($jenis=='retur')
        $subtotal = -1*$subtotal;
    $grandtotal = $grandtotal + $subtotal;
    ?>
    <tr>
        <td  class="withborder" align="center">{{ \Carbon\Carbon::parse($j->sj_tgl)->translatedFormat('d-m')}}</td>
        @if($jenis=='jual')
        <td  class="withborder">{{$j->inv_no}}</td>
        @else
        <td  class="withborder">{{$j->sj_no}}</td>
        @endif
        <td  class="withborder">{{$j->nick}}</td>
        <td  class="withborder">{{$j->kode_barang}}</td>
        <td  class="withborder">{{$j->nama_barang2}}</td>
        <td  class="withborder">{{number_format($j->harga,2)}}</td>
        @if($jenis=='jual')
        <td  class="withborder">{{$j->qty_kirim}}</td>
        @else
        <td  class="withborder">{{-1*$j->qty_kirim}}</td>
        @endif
        <td  class="withborder">{{$j->satuan}}</td>
        <td  class="withborder">{{$j->discount}}</td>
        <td  class="withborder" align="right">{{number_format($subtotal,2)}}</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="9" align="center" class="withborder">J U M L A H</td>
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

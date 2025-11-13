@extends('layouts.simple')

@section('maincontent')
<?php
    $arr_jurnal = array_chunk($data_jurnal,17);
?>
@foreach($arr_jurnal as $chunk)
@if (!$loop->first)
<p class="new-page"/> &nbsp;</p>
@endif
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>REKAP STOCK BARANG</u></span></td>
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
    <th style= "width: 20%; border-bottom-width:0.1px;" rowspan="2"><strong>KODE</strong></th>
    <th style= "width: 30%; border-bottom-width:0.1px;" rowspan="2"><strong>Uraian Barang / Persediaan</strong></th>
    <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>Satuan</strong></th>
    <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>Saldo Awal</strong></th>
    <th style= "width: 15%; border-bottom-width:0.1px;" colspan="2"><strong>Mutasi</strong></th>
    <th style= "width: 15%; border-bottom-width:0.1px;" rowspan="2"><strong>Saldo Akhir</strong></th>
  </tr>
  <tr>
      <th>Tambah</th>
      <th>Kurang</th>
  </tr>
  </thead>
  <?php
    $total = 0;
  ?>
  @foreach($chunk as $j)
  <tr>
    <?php
    if($j->kode == ''){
        $uraian = $j->uraian;
        $satuan = "";
        $awal = "";
        $tambah = "";
        $kurang = "";
        $akhir = "";
        $str_class_line = " underlinetd";
    }
    else {
        $uraian = $j->uraian;
        $satuan = $j->satuan;
        $awal = $j->awal;
        $tambah = $j->tambah;
        $kurang = $j->kurang;
        $akhir = $j->akhir;
        $str_class_line = "";
    }
    if($j->perkiraan == 'JUM'){
        $kode = "";
        $str_class = " colorfulltd";
    }
    else{
        $kode = $j->kode;
        $str_class = "";
    }
    ?>
    <td align="center" class="withborder {{$str_class}} {{$str_class_line}}" class="withborder">{{$kode}}</td>
    <td class="withborder {{$str_class}} {{$str_class_line}}">{{$uraian}}</td>
    <td align="center" class="withborder {{$str_class}} {{$str_class_line}}">{{$satuan}}</td>
    <td align="right" class="withborder {{$str_class}} {{$str_class_line}}">{{($awal == '' ? '' : number_format($awal,2))}}</td>
    <td align="right" class="withborder {{$str_class}} {{$str_class_line}}">{{($tambah == ''? '' : number_format($tambah,2))}}</td>
    <td align="right" class="withborder {{$str_class}} {{$str_class_line}}">{{($kurang == ''? '' : number_format($kurang,2))}}</td>
    <td align="right" class="withborder {{$str_class}} {{$str_class_line}}">{{($akhir == ''? '' : number_format($akhir,2))}}</td>
  </tr>
  @endforeach

</table>
@if ($loop->last)
<?php
    $data['tanggal'] = \Carbon\Carbon::now()->format('Y-m-d');

?>
@endif
@endforeach
@stop

@push('head')
<style>

.new-page {
page-break-after: always;
}

table th{
 text-align: center !important;
 /*border: 0.2px solid black;*/
}
th{
 text-align: center !important;
 border: 1px solid black;
 font-size: 14pt;
 font-weight: bold !important;
 padding: 5px;
 background-color: cornflowerblue;
}

td{
 font-size: 14pt;
 padding: 5px;
 border-left: 1px solid black;
 border-right: 1px solid black;
 border-top: none;
 border-bottom: none;
}

td.withborder{
 font-size: 14pt;
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
 font-size: 14pt;
 font-weight: bold !important;
 padding: 5px;
}

.colorfulltd{
    background-color: darksalmon
}

.underlinetd{
    text-decoration: underline;
}
</style>
@endpush

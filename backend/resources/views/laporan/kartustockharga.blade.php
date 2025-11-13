@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>KARTU STOCK & HARGA PERSEDIAAN</u></span></td>
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
    $total = 0;
    $counter = 0;
    $tot_debet = 0;
    $tot_kredit = 0;
    $tot_saldo = 0;
  ?>
  @if(!empty($data_jurnal))
  @foreach($data_jurnal as $j)
    @if($j->kode_perk=='' || is_null($j->kode_perk))
        @continue
    @endif
    @if($j->kode_perk != '-')
        <?php $saldo=0; ?>
        @if($init = 1)
        </table>
        @endif
        <h3>Nama Barang : {{$j->uraian}} &nbsp;&nbsp;&nbsp;[{{$j->kode_perk}} - {{$j->kode_barang}}]</h3>
        <table width="100%" border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th style= "width: 50%; border-bottom-width:0.1px;" colspan="4"><strong>MUTASI BARANG/PERSEDIAAN</strong></th>
                <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>No Ref</strong></th>
                <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>Harga</strong></th>
                <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>Ref Harga</strong></th>
                <th style= "width: 20%; border-bottom-width:0.1px;" rowspan="2"><strong>Keterangan</strong></th>
            </tr>
            <tr>
                <th style= "width: 11%; border-bottom-width:0.1px;"><strong>Tanggal</strong></th>
                <th style= "width: 13%; border-bottom-width:0.1px;"><strong>Tambah</strong></th>
                <th style= "width: 13%; border-bottom-width:0.1px;"><strong>Kurang</strong></th>
                <th style= "width: 13%; border-bottom-width:0.1px;"><strong>Saldo Akhir</strong></th>
            </tr>
            </thead>
    @else
    <?php
    $saldo = $saldo + $j->tambah - $j->kurang;
    ?>
    <tr>
        <td  class="withborder" align="center">{{ \Carbon\Carbon::parse($j->tanggal)->translatedFormat('d-m-Y')}}</td>
        <td  class="withborder" align="right">{{($j->tambah==0 ? '-' : number_format($j->tambah,2,",","."))}}</td>
        <td  class="withborder" align="right">{{($j->kurang==0 ? '-' : number_format($j->kurang,2,",","."))}}</td>
        <td  class="withborder" align="right">{{($saldo==0 ? '-' : number_format($saldo,2,",","."))}}</td>
        <td  class="withborder">{{$j->referensi}}</td>
        <td  class="withborder">{{number_format($j->harga,2)}}</td>
        <td  class="withborder">{{$j->ref_harga}}</td>
        <td  class="withborder">{{$j->keterangan}}</td>
    </tr>
    @endif

  <?php
    $init = 1;
  ?>
  @endforeach
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

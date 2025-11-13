@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>REKAP VOUCHER REKANAN</u></span></td>
  </tr>  
  <tr class="noborder">
    @if($periode=='bl')
    <td align="center"><b>Bulan {{$monthName}} / {{$year}}</b></td>
    @else
    <td align="center"><b>Tahun {{$year}}</b></td>
    @endif
  </tr>
</table>
<?php
    $prev_rekanan = "";
    $init = 1;
    $no_urut = 1;
  ?>
  @foreach($data_jurnal as $j)
    @if($prev_rekanan != $j->rekanan)
        @if($init > 1)
        <tr>
            <td class="withborder" colspan="4" align="center"> JUMLAH </td>
            <td class="withborder" align="right">{{($tot_hutang==0 ? '-' : number_format($tot_hutang,2,",","."))}}</td>
            <td class="withborder" align="right">{{($tot_bayar==0 ? '-' : number_format($tot_bayar,2,",","."))}}</td>
            <td class="withborder" align="right">{{($tot_saldo==0 ? '-' : number_format($tot_saldo,2,",","."))}}</td>

        </tr>
        </table>
        @endif
        <?php
            $no_urut = 1; 
            $tot_hutang = 0;
            $tot_bayar = 0;
            $tot_saldo = 0;
        ?>
        <h3>{{$j->rekanan}}</h3>
        <table width="100%" border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2">Nomor Urut</th>
                <th style= "width: 20%; border-bottom-width:0.1px;" colspan="2"><strong>Voucer</strong></th>
                <th style= "width: 45%; border-bottom-width:0.1px;" rowspan="2"><strong>Uraian</strong></th>
                <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>Nilai Utang</strong></th>
                <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>Pembayaran</strong></th>
                <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>Saldo</strong></th>
                </tr>
                <tr>
                <th width="7">No</th>
                <th width="4">Tgl</th>
                </tr>
            </thead>
    @endif
            <?php
                $saldo = $j->hutang - $j->bayar; 
                $tot_hutang = $tot_hutang + $j->hutang;
                $tot_bayar = $tot_bayar + $j->bayar;
                $tot_saldo = $tot_saldo + $saldo;
            ?>
            <tr>
                <td class="withborder">{{$no_urut}}</td>
                <td class="withborder">{{$j->no_vcr}}</td>
                <td class="withborder">{{date('d-m-Y',strtotime($j->tgl_vcr))}}</td>
                <td class="withborder">{{$j->uraian}}</td>
                <td class="withborder" align="right">{{($j->hutang==0 ? '-' : number_format($j->hutang,2,",","."))}}</td>
                <td class="withborder" align="right">{{($j->bayar==0 ? '-' : number_format($j->bayar,2,",","."))}}</td>
                <td class="withborder" align="right">{{($saldo==0 ? '-' : number_format($saldo,2,",","."))}}</td>
            </tr>
    <?php
        $prev_rekanan = $j->rekanan;
        $init++; 
        $no_urut++;
    ?>
  @endforeach


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
 font-size: 7pt;
 padding: 5px;
 border-left: 1px solid black;
 border-right: 1px solid black;
 border-top: none;
 border-bottom: none;
 vertical-align: top;
}

td.withborder{
 font-size: 7pt;
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
 font-size: 9pt;
 font-weight: bold !important;
 padding: 5px;
}
.colorfulltd{
    background-color: darksalmon
}
</style>
@endpush
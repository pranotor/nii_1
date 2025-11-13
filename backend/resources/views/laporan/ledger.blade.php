@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="noborder">
    <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>BUKU BESAR</u></span></td>  
  </tr>  
  <tr class="noborder">
    @if($periode=='bl')
    <td align="center"><b>Bulan {{$monthName}} / {{$year}}</b></td>
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
    @if($j->kode_perk=='A')
        @if($init)
        <tr>
            <td width="60%" class="withborder" colspan="4" align="center"><strong>J U M L A H</strong></td>
            <td width="13%" align="right" class="withborder">{{number_format($tot_debet,2,",",".")}}</td>
            <td width="13%" align="right" class="withborder">{{number_format($tot_kredit,2,",",".")}}</td>
            <td width="14%" align="right" class="withborder">{{number_format($tot_saldo,2,",",".")}}</td>
        </tr>
        </table>
        <br/><br/>
        @endif
        <h3>{{$j->nama_perk}}</h3>
        <table width="100%" border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th style= "width: 5%; border-bottom-width:0.1px;" rowspan="2"><strong>No</strong></th>
                <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>Tanggal</strong></th>
                <th style= "width: 10%; border-bottom-width:0.1px;" rowspan="2"><strong>No Bukti Jurnal</strong></th>
                <th style= "width: 35%; border-bottom-width:0.1px;" rowspan="2"><strong>Uraian</strong></th>
                <th style= "width: 26%; border-bottom-width:0.1px;" colspan="2"><strong>Mutasi</strong></th>
                <th style= "width: 14%; border-bottom-width:0.1px;" rowspan="2"><strong>Saldo</strong></th>
            </tr>
            <tr>
                <th style= "width: 13%; border-bottom-width:0.1px;"><strong>Debet</strong></th>
                <th style= "width: 13%; border-bottom-width:0.1px;"><strong>Kredit</strong></th>
            </tr>
            </thead>
            <?php
                $saldo = 0;
                $counter = 0; 
                $tot_debet = 0;
                $tot_kredit = 0;
                $tot_saldo = 0;
            ?>
    @else 
    <?php
        $kode_depan = Str::substr($j->kode_perk, 0, 1); 
        if($kode_depan >4 && $kode_depan < 9)
            $saldo = $j->kredit - $j->debet;
        else {
            $saldo = $j->debet - $j->kredit;
        }
        if($j->referensi == 'sss'){
            $jurnal_debet = 0;
            $jurnal_kredit = 0;
            $referensi = 'SALDO AWAL';
        }
        else{
            $jurnal_debet = $j->debet;
            $jurnal_kredit = $j->kredit;
            $tot_debet += $j->debet;
            $tot_kredit += $j->kredit;
            $referensi = $j->referensi;
        }
        
        $tot_saldo += $saldo;
        
    ?>
    <tr>
        <td width="5%" class="withborder">{{$counter}}</td>
        <td width="10%" class="withborder">{{$j->tanggal}}</td>
        <td width="10%" class="withborder">{{$referensi}}</td>
        <td width="35%" class="withborder">{{$j->uraian}}</td>
        <td width="13%" align="right" class="withborder">{{($jurnal_debet==0 ? '' : number_format($jurnal_debet,2,",","."))}}</td>
        <td width="13%" align="right" class="withborder">{{($jurnal_kredit==0 ? '' : number_format($jurnal_kredit,2,",","."))}}</td>
        <td width="14%" align="right" class="withborder">{{number_format($tot_saldo,2,",",".")}}</td>
    </tr>    
    @endif
            
  <?php
    $init = 1;
    $counter++;
  ?>
  @endforeach
    <tr>
        <td width="60%" class="withborder" colspan="4" align="center"><strong>J U M L A H</strong></td>
        <td width="13%" align="right" class="withborder">{{number_format($tot_debet,2,",",".")}}</td>
        <td width="13%" align="right" class="withborder">{{number_format($tot_kredit,2,",",".")}}</td>
        <td width="14%" align="right" class="withborder">{{number_format($tot_saldo,2,",",".")}}</td>
    </tr>
    </table>
</table>
@endif

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
 font-size: 9pt;
 padding: 5px;
 border-left: 1px solid black;
 border-right: 1px solid black;
 border-top: none;
 border-bottom: none;
}

td.withborder{
 font-size: 9pt;
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
 padding: 5px;
}
</style>
@endpush
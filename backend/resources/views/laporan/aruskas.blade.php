@extends('layouts.simple')

@section('maincontent')
@include('laporan.header')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr class="noborder">
        <td width="100%"  align="center"><span style="font-size: 14pt; text-decoration:bold"><u>LAPORAN ARUS KAS</u></span></td>  
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
    if($periode=='bl'){
        $str_periode = 'BULAN';
    }
    else{
        $str_periode = 'TAHUN';
    }
?>
<table border="0" cellspacing="2" cellpadding="2" width="100%">
    <tbody>
        <?php
          $jenis = "";
          $counter = 1;
          $total_saldo = 0;
          $total_kas = 0;
          $saldo_kas = 0;
          $saldo_kas0 = 0;
          $grand_total = 0;
          $saldo_awalkas = 0;
          $saldo_akhirkas = 0;
        ?>
        @foreach($data_jurnal as $det)
          @if($jenis != $det->klplak)
              @if($counter != 1)
              <?php
                  $str_total_saldo = ($total_saldo < 0 ? "(".number_format(abs($total_saldo),2).")" : number_format($total_saldo,2));
              ?>
              
              <tr>
                  <th width="80%" align="center"><strong>Arus Kas Bersih dari {{$jenis}}</strong></th>
                  <th width="20%" align="right">{{$str_total_saldo}}</th>
              </tr>
              <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
              </tr>
             
              <?php
                  $total_saldo=0;
              ?>
              @endif
              @if($det->kodelak != '999')
              <tr>
                  <th colspan="2" align="left"><strong>Arus Kas dari {{$det->klplak}}</strong></th>
              </tr>
              @endif
          @endif
          @if($det->kodelak != '999')
          <tr>
            <?php
              if($det->kodelak != '999') {
                  $total = -1*$det->total ;
                  $total0 = -1*$det->total0;
              }
              /* if($det->kodelak == '110')
                  $total = $laba_rugi; */ 
              $total = $total - $total0;
              $saldo = ($total < 0 ? "(".number_format(abs($total),2).")" : number_format($total,2));
              $jenis = $det->klplak;
              $counter++;
              $total_saldo = $total_saldo + $total;
              $grand_total = $grand_total + $total;
            ?>
            <td width="80%" align="left">&nbsp;&nbsp;&nbsp;{{$det->namalak}}</td>
            <td width="20%" align="right">{{$saldo}}</td> 
          </tr>
          @else
            <?php
                  $saldo_kas = $det->total ;
                  $saldo_kas0 = $det->total0;
                  $total_kas = $total_kas + $saldo_kas - $saldo_kas0;
            ?>
          @endif
         @endforeach
        
         <?php
              $saldo_kas = ($total_kas < 0 ? "(".number_format(abs($total_kas),2).")" : number_format($total_kas,2));
              $saldo_awalkas = ($saldo_kas0 < 0 ? "(".number_format(abs($saldo_kas0),2).")" : number_format($saldo_kas0,2));
              $saldo_akhirkas = $total_kas + $saldo_kas0;
              $saldo_akhirkas = ($saldo_akhirkas < 0 ? "(".number_format(abs($saldo_akhirkas),2).")" : number_format($saldo_akhirkas,2));
         ?>
         <tr>
            <td width="80%" align="left">&nbsp;&nbsp;&nbsp;<strong>Kenaikan (Penurunan) Bersih Kas dan Setara Kas</strong></td>
            <td width="20%" align="right">{{$saldo_kas}}</td> 
         </tr>
         <tr>
            <td width="80%" align="left">&nbsp;&nbsp;&nbsp;<strong>Saldo Awal Kas dan Setara Kas</strong></td>
            <td width="20%" align="right">{{$saldo_awalkas}}</td> 
         </tr>
         <tr>
            <td width="80%" align="left">&nbsp;&nbsp;&nbsp;<strong>Saldo Akhir Kas dan Setara Kas</strong></td>
            <td width="20%" align="right">{{$saldo_akhirkas}}</td> 
         </tr>
    </tbody>
  </table>
<x-ttd-jurnal :mod-name="$modname" jenis="0"/>

@stop


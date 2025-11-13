@extends('layouts.simple')

@section('maincontent')
<?php
$subtotal = 0;
$no = 1;

?>

<br/>
<table width="95%" border="0">
  <tr>
    <td colspan="2" align="center"><h1>TANDA TERIMA</h1></td>
  </tr>
  <tr>
    <td width="15%">Nama Customer : </td>
    <td width="70%"><strong>{{$jurnalH->nama}}</strong></td>
  </tr>
  <tr>
    <td colspan="2">Berikut ini kami kirimkan Faktur tagihan Sbb :</td>
  </tr>
</table>
<br/>
<table width="95%" border="0" cellpadding="10" cellspacing="0" class="tblIsi">
  <tr style= "border-bottom: 0.2px solid  black;">
    <td width="12%" align="center"><strong>Tgl</strong></td>
    <td width="18%" align="center"><strong>No PO</strong></td>
    <td width="15%" align="center"><strong>No Faktur</strong></td>
    <td width="28%" align="center"><strong>No Seri Faktur Pajak</strong></td>
    <td width="27%" align="center"><strong>Jumlah Tagihan</strong></td>
  </tr>
   <?php
  $i = 1;
  ?>
  @foreach ($jurnalD as $ju)
  <tr style= "border-bottom: 0.2px solid  black;">
    <td align="center">{{\Carbon\Carbon::parse($ju->inv_tgl)->translatedFormat('d-M-y')}}</td>
    <td align="center">{{$ju->po_cust}}</td>
    <td align="center">{{$ju->inv_no}}</td>
    <td align="center">{{$ju->no_fp}}</td>
    <td align="center">@currency($ju->subtotal)</td>
  </tr>
  @if($ju->biaya_kirim <> 0)
  <tr style= "border-bottom: 0.2px solid  black;">
    <td align="center">&nbsp</td>
    <td align="center">&nbsp</td>
    <td align="center"></td>
    <td align="center">Biaya Kirim</td>
    <td align="center">@currency($ju->biaya_kirim)</td>
  </tr>
  @endif
  <?php
  $i++;
  $no++;
  $subtotal = $subtotal + $ju->total;
  ?>
@endforeach
<?php
      for($j=1;$j<=6-$i;$j++){
  ?>
  <tr style= "border-bottom: 0.2px solid  black;">
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
    </tr>
  <?php
      }
  ?>
    <tr style= "border-bottom: 0.2px solid  black;">
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">@currency($subtotal)</td>
      </tr>
</table>

</table>
<p>Bandung, {{\Carbon\Carbon::parse($jurnalH->tt_tgl)->translatedFormat('d-F-Y')}}</p>
<table width="95%" border="0" cellpadding="0" cellspacing="0" >
    <tr>
        <td width="80%" align="left"><strong>Ket : </strong></td>
        <td width="20%">Penerima </td>
    </tr>
    <tr>
        <td><strong>Mohon agar setelah ditandatangan dan dicap</strong></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><strong>Tanda terima ini di fax kembali ke No. 022-87784536</strong></td>
        <td>&nbsp;</td>
    </tr>

</table>
<br/>
<br/>
<br/>
<br/>

<br/>
<table width="95%" border="0">
  <tr>
    <td colspan="2" align="center"><h1>TANDA TERIMA</h1></td>
  </tr>
  <tr>
    <td width="15%">Nama Customer : </td>
    <td width="70%"><strong>{{$jurnalH->nama}}</strong></td>
  </tr>
  <tr>
    <td colspan="2">Berikut ini kami kirimkan Faktur tagihan Sbb :</td>
  </tr>
</table>
<br/>
<table width="95%" border="0" cellpadding="10" cellspacing="0" class="tblIsi">
  <tr style= "border-bottom: 0.2px solid  black;">
    <td width="12%" align="center"><strong>Tgl</strong></td>
    <td width="18%" align="center"><strong>No PO</strong></td>
    <td width="15%" align="center"><strong>No Faktur</strong></td>
    <td width="28%" align="center"><strong>No Seri Faktur Pajak</strong></td>
    <td width="27%" align="center"><strong>Jumlah Tagihan</strong></td>
  </tr>
   <?php

  $subtotal = 0;
  $i = 1;
  ?>
  @foreach ($jurnalD as $ju)
  <tr style= "border-bottom: 0.2px solid  black;">
    <td align="center">{{\Carbon\Carbon::parse($ju->inv_tgl)->translatedFormat('d-M-y')}}</td>
    <td align="center">{{$ju->po_cust}}</td>
    <td align="center">{{$ju->inv_no}}</td>
    <td align="center">{{$ju->no_fp}}</td>
    <td align="center">@currency($ju->subtotal)</td>
  </tr>
  @if($ju->biaya_kirim <> 0)
  <tr style= "border-bottom: 0.2px solid  black;">
    <td align="center">&nbsp</td>
    <td align="center">&nbsp</td>
    <td align="center"></td>
    <td align="center">Biaya Kirim</td>
    <td align="center">@currency($ju->biaya_kirim)</td>
  </tr>
  @endif
  <?php
  $i++;
  $no++;
  $subtotal = $subtotal + $ju->total;
  ?>
@endforeach
<?php
      for($j=1;$j<=6-$i;$j++){
  ?>
  <tr style= "border-bottom: 0.2px solid  black;">
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
    </tr>
  <?php
      }
  ?>
    <tr style= "border-bottom: 0.2px solid  black;">
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">@currency($subtotal)</td>
      </tr>
</table>

</table>
<p>Bandung, {{\Carbon\Carbon::parse($jurnalH->tt_tgl)->translatedFormat('d-F-Y')}}</p>
<table width="95%" border="0" cellpadding="0" cellspacing="0" >
    <tr>
        <td width="80%" align="left"><strong>Ket : </strong></td>
        <td width="20%">Penerima </td>
    </tr>
    <tr>
        <td><strong>Mohon agar setelah ditandatangan dan dicap</strong></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><strong>Tanda terima ini di fax kembali ke No. 022-87784536</strong></td>
        <td>&nbsp;</td>
    </tr>

</table>

<?php
$subtotal = 0;
$no = 1;
?>

@stop

@push('head')
<style>
body{
    font-size: 10pt;
}
pre {
    display: block;
    /*font-family: monospace;*/
    font-family: Helvetica,'Source Sans Pro', 'Helvetica Neue', Arial, sans-serif;
    white-space: pre;
    margin: 0 0;
}

table.tblIsi {
    border: solid 1px black ;
    border-collapse: collapse;
    padding: 8;
    font-size: 10pt;
}
.tblIsi tr { border: none; }
.tblIsi td {
  border-right: solid 1px ;
  border-left: solid 1px;
}

.noborder td{
    border-right: none !important;
    border-left: none !important;
}
</style>
@endpush

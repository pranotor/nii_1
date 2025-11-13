@extends('layouts.simple')

@section('maincontent')
<?php
$subtotal = 0;
$no = 1;

?>

<table cellspacing="0" cellpadding="1" border="0" width="100%" style="page-break-before: always;"><tr>
    <td width="90%" style="border-bottom: 2px solid red;" valign="bottom"><span style="font-size: 20pt; font-weight:bold">PT. Nusa International</span></td></tr>
    <tr><td colspan="2"><span style="font-size: 10pt;" >Komp. Pergudangan Biz Park Commercial Estate Blok A1-36
    <br/>Jl. Raya Kopo No. 455
    <br/>Bandung 40236 - Indonesia
    <br/>Tel: +62-22-8778 4636 ; 8777 5656
    <br/>Fax: +62-22-8778 4536
    <br/>Email : info@nii-ltd.com  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website : www.nii-ltd.com</span></td></tr>
</table>
<table width="100%" border="0">
  <tr>
    <td colspan="2" align="center"><h2>TANDA TERIMA</h2></td>
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
    <td align="center">{{ $ju->berikat ? '070.' : '010.' }}{{$ju->no_fp}}</td>
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
      for($j=1;$j<=4-$i;$j++){
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
        <td><strong>Tanda terima ini di fax kembali ke No. 022-87784636</strong></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><strong>Email ; accounting@nii-ltd.com</strong></td>
        <td>&nbsp;</td>
    </tr>
</table>
<br/>
<br/>

<table cellspacing="0" cellpadding="1" border="0" width="100%"><tr>
    <td width="90%" style="border-bottom: 2px solid red;" valign="bottom"><span style="font-size: 20pt; font-weight:bold">PT. Nusa International</span></td></tr>
    <tr><td colspan="2"><span style="font-size: 10pt;" >Komp. Pergudangan Biz Park Commercial Estate Blok A1-36
    <br/>Jl. Raya Kopo No. 455
    <br/>Bandung 40236 - Indonesia
    <br/>Tel: +62-22-8778 4636 ; 8777 5656
    <br/>Fax: +62-22-8778 4536
    <br/>Email : info@nii-ltd.com  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website : www.nii-ltd.com</span></td></tr>
</table>
<table width="100%" border="0">
  <tr>
    <td colspan="2" align="center"><h2>TANDA TERIMA</h2></td>
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
    <td align="center">{{ $ju->berikat ? '070.' : '010.' }}{{$ju->no_fp}}</td>
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
        for($j=1;$j<=4-$i;$j++){
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
        <td><strong>Tanda terima ini di fax kembali ke No. 022-87784636</strong></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><strong>Email ; accounting@nii-ltd.com</strong></td>
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

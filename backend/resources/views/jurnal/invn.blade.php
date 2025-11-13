@extends('layouts.simple')

@section('maincontent')


<table width="100%" border="0">
  <tr>
      <td width="50%"  style= "border: 0.2px dashed  black;border-radius:15px">
          <table>
              <tr>
                <td width="10%" align="left" valign="middle">Bill To</td>
                <td width="45%"><strong>{{$jurnalH->sales->qcustomer->nama}}</strong></td>
              </tr>
              <tr height="100px">
                <td align="left" valign="middle">Address</td>
                <td width="45%"><strong>{{$jurnalH->sales->qcustomer->alamat}}</strong></td>
              </tr>
              <tr>
                <td align="left" valign="middle">Tel</td>
                <td width="45%"><strong>{{$jurnalH->sales->qcustomer->telepon}}</strong></td>
              </tr>
          </table>
      </td>
      <td width="50%" valign="top">
        <table>
            <tr>
              <td width="10%" align="left" valign="middle">Invoice No</td>
              <td width="45%"><strong>{{$jurnalH->inv_no}}</strong></td>
            </tr>
            <tr>
              <td align="left" valign="middle">Date</td>
              <td><strong>{{\Carbon\Carbon::parse($jurnalH['sj_tgl'])->translatedFormat('d-M-Y')}}</strong></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td>PO : {{$jurnalH->sales->po_cust}}</td>
    <td></td>
  </tr>
</table>
<br/>
<table width="100%" border="0" cellpadding="10" cellspacing="0" class="tblIsi">
  <tr style= "border-bottom: 0.2px dashed  black;">
    <td width="7%" align="center"><strong>No</strong></td>
    <td width="20%" align="center"><strong>Product No</strong></td>
    <td width="28%" align="center"><strong>Description</strong></td>
    <td width="12%" align="center"><strong>Qty</strong></td>
    <td width="15%" align="center"><strong>Unit Price</strong></td>
    <td width="18%" align="center"><strong>Total</strong></td>
  </tr>
  <?php
  $i = 1;
  $subtotal = 0;
  ?>
  @foreach($jurnalD as $ju)
  <?php
    if($ju->qty_kirim==0)
        continue;
    $netto = $ju->harga - ($ju->harga * $ju->discount/100);
    $total_i = $ju->qty_kirim * $netto;

  ?>
    <tr valign="top">
        <td align="right">{{$i}}</td>
        @if($ju->kode2 != '')
        <td>{{$ju->kode2}}</td>
        @else
        <td>{{$ju->kode_barang}}</td>
        @endif
        <td>@if($ju->nama_barang2 == ''){{$ju->itembarang->itemperkiraan->nama_perk}}<br/><br/>@else{{$ju->nama_barang2}}@endif
        @if($ju->size != '')
        <br/>
        {!! nl2br(e($ju->size)) !!}
        <br/>
        @endif
        </td>
        <td align="right">{{number_format($ju->qty_kirim,0,',','.')}} {{$ju->satuan}}</td>
        <td align="right"><pre>Rp {{str_pad(number_format($netto,2,',','.'),13," ",STR_PAD_LEFT)}}</pre></td>

        <td align="right"><pre>Rp {{str_pad(number_format($total_i,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>

    </tr>
    <?php
        $i++;
        $subtotal = $subtotal + $total_i;
    ?>
  @endforeach
    <?php
        for($j=1;$j<=7-$i;$j++){
    ?>
    <tr>
        <td align="right"></td>
        <td> &nbsp; </td>
        <td><br/>  <br/>
        <br/>
        </td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>

        <td align="right">&nbsp;</td>

    </tr>
    <?php
        }
    ?>
  <tr valign="top" class="noborder" >
    <td colspan="4" rowspan="2" style="border-top: dashed 0.2px black;">{{terbilang($subtotal + $subtotal*$ppn)}} Rupiah</td>
    <td align="right"  style="border-top: dashed 0.2px black;">SUBTOTAL</td>
    <td align="right" style="border-top: dashed 0.2px black;"><pre>Rp {{str_pad(number_format($subtotal,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
  <tr class="noborder">
    <td align="right">TOTA INV</td>
    <td align="right"><pre>Rp {{str_pad(number_format($subtotal,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
</table>

<table border="0" width="100%" style="page-break-after: always;">
    <tr class="nobordersub">
        <td width="70%">&nbsp;</td>
        <td width="30%" align="center"></td>
    </tr>

</table>

<div style="background:url({{$img_file_copy}});background-repeat:no-repeat;background-position: center center;">
<table width="100%" border="0">
  <tr>
      <td width="50%"  style= "border: 0.2px dashed  black;border-radius:15px">
          <table>
              <tr>
                <td width="10%" align="left" valign="middle">Bill To</td>
                <td width="45%"><strong>{{$jurnalH->sales->qcustomer->nama}}</strong></td>
              </tr>
              <tr height="100px">
                <td align="left" valign="middle">Address</td>
                <td width="45%"><strong>{{$jurnalH->sales->qcustomer->alamat}}</strong></td>
              </tr>
              <tr>
                <td align="left" valign="middle">Tel</td>
                <td width="45%"><strong>{{$jurnalH->sales->qcustomer->telepon}}</strong></td>
              </tr>
          </table>
      </td>
      <td width="50%" valign="top">
        <table>
            <tr>
              <td width="10%" align="left" valign="middle">Invoice No</td>
              <td width="45%"><strong>{{$jurnalH->inv_no}}</strong></td>
            </tr>
            <tr>
              <td align="left" valign="middle">Date</td>
              <td><strong>{{\Carbon\Carbon::parse($jurnalH['sj_tgl'])->translatedFormat('d-M-Y')}}</strong></td>
            </tr>
        </table>
    </td>
      <td>
      </td>
  </tr>
  <tr>
    <td>PO : {{$jurnalH->sales->po_cust}}</td>
    <td></td>
  </tr>
</table>
<br/>
<table width="100%" border="0" cellpadding="10" cellspacing="0" class="tblIsi">
  <tr style= "border-bottom: 0.2px dashed  black;">
    <td width="7%" align="center"><strong>No</strong></td>
    <td width="20%" align="center"><strong>Product No</strong></td>
    <td width="28%" align="center"><strong>Description</strong></td>
    <td width="12%" align="center"><strong>Qty</strong></td>
    <td width="15%" align="center"><strong>Unit Price</strong></td>
    <td width="18%" align="center"><strong>Total</strong></td>
  </tr>
  <?php
  $i = 1;
  $subtotal = 0;
  ?>
  @foreach($jurnalD as $ju)
  <?php
    if($ju->qty_kirim==0)
        continue;
    $netto = $ju->harga - ($ju->harga * $ju->discount/100);
    $total_i = $ju->qty_kirim * $netto;

  ?>
    <tr valign="top">
        <td align="right">{{$i}}</td>
        @if($ju->kode2 != '')
        <td>{{$ju->kode2}}</td>
        @else
        <td>{{$ju->kode_barang}}</td>
        @endif
        <td>@if($ju->nama_barang2 == ''){{$ju->itembarang->itemperkiraan->nama_perk}}<br/><br/>@else{{$ju->nama_barang2}}@endif
        @if($ju->size != '')
        <br/>
        {!! nl2br(e($ju->size)) !!}
        <br/>
        @endif
        </td>
        <td align="right">{{number_format($ju->qty_kirim,2,',','.')}} {{$ju->satuan}}</td>
        <td align="right"><pre>Rp {{str_pad(number_format($netto,2,',','.'),13," ",STR_PAD_LEFT)}}</pre></td>

        <td align="right"><pre>Rp {{str_pad(number_format($total_i,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>

    </tr>
    <?php
        $i++;
        $subtotal = $subtotal + $total_i;
    ?>
  @endforeach
    <?php
        for($j=1;$j<=7-$i;$j++){
    ?>
    <tr>
        <td align="right"></td>
        <td> &nbsp; </td>
        <td><br/>  <br/>
        <br/>
        </td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>

        <td align="right">&nbsp;</td>

    </tr>
    <?php
        }
    ?>
  <tr valign="top" class="noborder" >
    <td colspan="4" rowspan="2" style="border-top: dashed 0.2px black;">{{terbilang($subtotal + $subtotal*$ppn)}} Rupiah</td>
    <td align="right"  style="border-top: dashed 0.2px black;">SUBTOTAL</td>
    <td align="right" style="border-top: dashed 0.2px black;"><pre>Rp {{str_pad(number_format($subtotal,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
  <tr class="noborder">
    <td align="right">TOTA INV</td>
    <td align="right"><pre>Rp {{str_pad(number_format($subtotal,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
</table>

<table border="0" width="100%" style="page-break-after: always;">
    <tr class="nobordersub">
        <td width="70%">&nbsp;</td>
        <td width="30%" align="center"></td>
    </tr>

</table>
</div>
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
    border: dashed 1px black ;
    border-collapse: collapse;
    padding: 10;
    font-size: 10pt;
}
.tblIsi tr { border: none; }
.tblIsi td {
  border-right: dashed 1px ;
  border-left: dashed 1px;
}

.noborder td{
    border-right: none !important;
    border-left: none !important;
}
</style>
@endpush

@extends('layouts.simple')

@section('maincontent')
<?php
$subtotal = 0;
$no = 1;
?>
@foreach($jurnalD->chunk(5) as $chunk)
<table cellspacing="0" cellpadding="1" border="0" width="100%" style="page-break-before: always;"><tr><td width="10%" style="border-bottom: 2px solid red;"><img src="{{$img_file}}" height="60px"/></td>
    <td width="90%" style="border-bottom: 2px solid red;" valign="bottom">&nbsp;&nbsp;<span style="font-size: 20pt; font-weight:bold">PT. Nusa International</span></td></tr>
    <tr><td colspan="2"><span style="font-size: 10pt;" >Komp. Pergudangan Biz Park Commercial Estate Blok A1-36
    <br/>Jl. Raya Kopo No. 455
    <br/>Bandung 40236 - Indonesia
    <br/>Tel: +62-22-8778 4636 ; 8777 5656
    <br/>Fax: +62-22-8778 4536
    <br/>Email : info@nii-ltd.com  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website : www.nii-ltd.com</span></td></tr>
</table>
<br/>
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
      <td width="50%">
        <table>
            <tr>
              <td width="10%" align="left" valign="middle">Invoice No</td>
              <td width="45%"><strong>{{$jurnalH->inv_no}}</strong></td>
            </tr>
            <tr>
              <td align="left" valign="middle">Date</td>
              <td><strong>{{\Carbon\Carbon::parse($jurnalH['sj_tgl'])->translatedFormat('d-M-Y')}}</strong></td>
            </tr>
            <tr>
              <td align="left" valign="middle">Payment</td>
              <td><strong>{{$jurnalH->sales->qcustomer->kredit_term}} days</strong></td>
            </tr>
            <tr>
              <td align="left" valign="middle">Bank</td>
              <td><strong>{{$bank->bank_name}}</strong></td>
            </tr>
            <tr>
                <td align="left" valign="middle">Address</td>
                <td><strong>{{$bank->bank_addr}}</strong></td>
            </tr>
            <tr>
                <td align="left" valign="middle">Acc.Name</td>
                <td><strong>{{$bank->bank_acc_name}}</strong></td>
            </tr>
            <tr>
                <td align="left" valign="middle">Acc.No</td>
                <td><strong>{{$bank->bank_acc_no}} ( IDR )</strong></td>
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
  ?>
  @foreach($chunk as $ju)
  <?php
    if($ju->qty_kirim==0)
        continue;
    $netto = $ju->harga - ($ju->harga * $ju->discount/100);
    $total_i = $ju->qty_kirim * $netto;

  ?>
    <tr valign="top">
        <td align="right">{{$no}}</td>
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
        $no++;
        $subtotal = $subtotal + $total_i;
    ?>
  @endforeach
    <?php
        for($j=1;$j<=8-$i;$j++){
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
  @if ($loop->last)
  <tr valign="top" class="noborder" >
    <td colspan="4" rowspan="3" style="border-top: dashed 0.2px black;">{{terbilang($subtotal + $subtotal*$ppn)}} Rupiah</td>
    <td align="right"  style="border-top: dashed 0.2px black;">SUBTOTAL</td>
    <td align="right" style="border-top: dashed 0.2px black;"><pre>Rp {{str_pad(number_format($subtotal,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
  <tr class="noborder">
    <td align="right">PPN {{$ppn * 100}}%</td>
    <td align="right"><pre>Rp {{str_pad(number_format($subtotal*$ppn,2,',','.'),21," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
  <tr class="noborder">
    <td align="right">TOTAL INV</td>
    <td align="right"><pre>Rp {{str_pad(number_format($subtotal + $subtotal*$ppn,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
  @else
  <tr valign="top" class="noborder" >
    <td colspan="4" rowspan="3" style="border-top: dashed 0.2px black;">&nbsp;</td>
    <td align="right"  style="border-top: dashed 0.2px black;">&nbsp;</td>
    <td align="right" style="border-top: dashed 0.2px black;">&nbsp;</td>
  </tr>
  <tr class="noborder">
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr class="noborder" style="page-break-after: always;">
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  @endif
</table>
@endforeach
<table border="0" width="100%" style="page-break-after: always;">
    <tr class="nobordersub">
        <td width="70%">&nbsp;</td>
        <td width="30%" align="center"></td>
    </tr>
    <tr class="nobordersub">
        <td width="70%">&nbsp;</td>
        <td width="30%" align="center">PT Nusa International</td>
    </tr>
</table>

<?php
$subtotal = 0;
$no = 1;
?>
@foreach($jurnalD->chunk(5) as $chunk)
<table cellspacing="0" cellpadding="1" border="0" width="100%" style="page-break-before: always;"><tr><td width="10%" style="border-bottom: 2px solid red;"><img src="{{$img_file}}" height="60px"/></td>
    <td width="90%" style="border-bottom: 2px solid red;" valign="bottom">&nbsp;&nbsp;<span style="font-size: 20pt; font-weight:bold">PT. Nusa International</span></td></tr>
    <tr><td colspan="2"><span style="font-size: 10pt;" >Komp. Pergudangan Biz Park Commercial Estate Blok A1-36
    <br/>Jl. Raya Kopo No. 455
    <br/>Bandung 40236 - Indonesia
    <br/>Tel: +62-22-8778 4636 ; 8777 5656
    <br/>Fax: +62-22-8778 4536
    <br/>Email : info@nii-ltd.com  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website : www.nii-ltd.com</span></td></tr>
</table>
<br/>
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
      <td width="50%">
        <table>
            <tr>
              <td width="10%" align="left" valign="middle">Invoice No</td>
              <td width="45%"><strong>{{$jurnalH->inv_no}}</strong></td>
            </tr>
            <tr>
              <td align="left" valign="middle">Date</td>
              <td><strong>{{\Carbon\Carbon::parse($jurnalH['sj_tgl'])->translatedFormat('d-M-Y')}}</strong></td>
            </tr>
            <tr>
              <td align="left" valign="middle">Payment</td>
              <td><strong>{{$jurnalH->sales->qcustomer->kredit_term}} days</strong></td>
            </tr>
            <tr>
              <td align="left" valign="middle">Bank</td>
              <td><strong>{{$bank->bank_name}}</strong></td>
            </tr>
            <tr>
                <td align="left" valign="middle">Address</td>
                <td><strong>{{$bank->bank_addr}}</strong></td>
            </tr>
            <tr>
                <td align="left" valign="middle">Acc.Name</td>
                <td><strong>{{$bank->bank_acc_name}}</strong></td>
            </tr>
            <tr>
                <td align="left" valign="middle">Acc.No</td>
                <td><strong>{{$bank->bank_acc_no}} ( IDR )</strong></td>
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
  ?>
  @foreach($chunk as $ju)
  <?php
    if($ju->qty_kirim==0)
        continue;
    $netto = $ju->harga - ($ju->harga * $ju->discount/100);
    $total_i = $ju->qty_kirim * $netto;

  ?>
    <tr valign="top">
        <td align="right">{{$no}}</td>
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
        $no++;
        $subtotal = $subtotal + $total_i;
    ?>
  @endforeach
    <?php
        for($j=1;$j<=8-$i;$j++){
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
  @if ($loop->last)
  <tr valign="top" class="noborder" >
    <td colspan="4" rowspan="3" style="border-top: dashed 0.2px black;">{{terbilang($subtotal + $subtotal*$ppn)}} Rupiah</td>
    <td align="right"  style="border-top: dashed 0.2px black;">SUBTOTAL</td>
    <td align="right" style="border-top: dashed 0.2px black;"><pre>Rp {{str_pad(number_format($subtotal,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
  <tr class="noborder">
    <td align="right">PPN {{$ppn * 100}}%</td>
    <td align="right"><pre>Rp {{str_pad(number_format($subtotal*$ppn,2,',','.'),21," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
  <tr class="noborder">
    <td align="right">TOTAL INV</td>
    <td align="right"><pre>Rp {{str_pad(number_format($subtotal + $subtotal*$ppn,2,',','.'),20," ",STR_PAD_LEFT)}}</pre></td>
  </tr>
  @else
  <tr valign="top" class="noborder" >
    <td colspan="4" rowspan="3" style="border-top: dashed 0.2px black;">&nbsp;</td>
    <td align="right"  style="border-top: dashed 0.2px black;">&nbsp;</td>
    <td align="right" style="border-top: dashed 0.2px black;">&nbsp;</td>
  </tr>
  <tr class="noborder">
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr class="noborder" style="page-break-after: always;">
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  @endif
</table>
@endforeach
<table border="0" width="100%" style="page-break-after: always;">
    <tr class="nobordersub">
        <td width="70%">&nbsp;</td>
        <td width="30%" align="center"></td>
    </tr>
    <tr class="nobordersub">
        <td width="70%">&nbsp;</td>
        <td width="30%" align="center">PT Nusa International</td>
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

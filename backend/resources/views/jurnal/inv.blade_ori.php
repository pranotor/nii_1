@extends('layouts.simple')

@section('maincontent')


<table width="100%" border="0" cellpadding="2" cellspacing="0" height="700px">
  
  <tr class="perkiraan">
    <td width="10%" align="left" valign="middle" style= "border-top: 0.2px dashed  black">Bill To</td>
    <td colspan="2" width="45%" style= "border-top: 0.2px dashed  black"><strong>{{$jurnalH->sales->qcustomer->nama}}</strong></td>
    <td width="12%" align="left" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; Invoice No</td>
    <td colspan="4" width="33%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; <strong>{{$jurnalH->inv_no}}</strong></td>
    
  </tr>
  <tr class="perkiraan">
    <td align="left" rowspan="4" style= "border-top: 0.2px dashed  black">Add</td>
    <td rowspan="4" width="45%" style= "border-top: 0.2px dashed  black"><strong>{{$jurnalH->sales->qcustomer->alamat}}</strong></td>
    <td align="left" width="12%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; Date</td>
    <td colspan="3" width="33%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; <strong>{{\Carbon\Carbon::parse($jurnalH['qt_tgl'])->translatedFormat('d-m-Y')}}</strong></td>
  </tr>
  <tr class="perkiraan">
    <td align="left" width="12%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; Payment</td>
    <td colspan="3" width="33%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; <strong>30 days</strong></td>
  </tr>
  <tr class="perkiraan">
    <td align="left" width="12%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; Bank</td>
    <td colspan="3" width="33%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; <strong>PT BANK HSBC INDONESIA</strong></td>
  </tr>
  <tr class="perkiraan">
    <td align="left" width="12%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; Address</td>
    <td colspan="3" width="33%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; <strong>JL IR H JUANDA BANDUNG</strong></td>
  </tr>
  <tr class="perkiraan">
    <td width="10%" align="left" valign="middle">Tel</td>
    <td width="45%"><strong>{{$jurnalH->sales->qcustomer->telepon}}</strong></td>
    <td width="12%" align="left" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; Acc. Name</td>
    <td colspan="3" width="33%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; <strong>PT. NUSA INTERNATIONAL</strong></td>
  </tr>
  <tr class="perkiraan">
    <td width="10%" align="left" valign="middle" style= "border-bottom: 0.2px dashed  black; border-radius: 25px">P/O NO </td>
    <td width="45%" style= "border-bottom: 0.2px dashed  black"><strong>{{$jurnalH->sales->po_cust}}</strong></td>
    <td width="12%" align="left" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; Acc.No</td>
    <td colspan="3" width="33%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none">&nbsp; <strong>602012676075 ( IDR )</strong></td>
  </tr>
  <tr class="noborder">
    <td width="10%" align="left" valign="middle"></td>
    <td width="45%"></td>
    <td width="12%" align="left" style= "border-top:none;border-right:none;border-bottom:none;border-left:none"></td>
    <td colspan="3" width="33%" style= "border-top:none;border-right:none;border-bottom:none;border-left:none"></td>
  </tr>
  <tr class="perkiraan">
    <td width="10%" align="center" style= "border-top: 0.2px dashed  black"><strong>No</strong></td>
    <td width="20%" align="center" style= "border-top: 0.2px dashed  black"><strong>Product No</strong></td>
    <td width="25%" align="center" style= "border-top: 0.2px dashed  black"><strong>Description</strong></td>
    <td width="12%" align="center" style= "border-top: 0.2px dashed  black"><strong>Qty</strong></td>
    <td width="15%" align="center" style= "border-top: 0.2px dashed  black"><strong>Unit Price</strong></td>
    <td width="18%" align="center" style= "border-top: 0.2px dashed  black"><strong>Total</strong></td>
  </tr>
  <?php
  $i = 1; 
  $subtotal = 0;
  ?>
  @foreach($jurnalD as $ju)
  <?php
    $netto = $ju->harga - ($ju->harga * $ju->discount/100); 
    $total_i = $ju->qty_kirim * $netto;
    
  ?>
    <tr>
        <td align="right">{{$i}}</td>
        @if($ju->kode2 != '')
        <td>{{$ju->kode2}}</td>
        @else
        <td>{{$ju->kode_barang}}</td>
        @endif
        <td>@if($ju->nama_barang2 == ''){{$ju->itembarang->perkiraan->nama_perk}}<br/><br/>@else{{$ju->nama_barang2}}@endif 
        @if($ju->size != '')
        <br/>
        {!! nl2br(e($ju->size)) !!}
        <br/> 
        @endif  
        </td>
        <td align="right">{{number_format($ju->qty_kirim,0,',','.')}} {{$ju->satuan}}</td>
        <td align="right">{{number_format($netto,2,',','.')}}</td>
        
        <td align="right">{{number_format($total_i,0,',','.')}}</td>
        
    </tr>
    <?php
        $i++; 
        $subtotal = $subtotal + $total_i;
    ?>
  @endforeach
    <?php
        for($j=1;$j<=9-$i;$j++){
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
  <tr>
    <td colspan="4" rowspan="3" style= "border-bottom-width:0.2px; ">{{terbilang($subtotal + $subtotal*0.1)}} Rupiah</td>
    <td align="right"  style= "border-bottom-width:0.2px; ">SUBTOTAL</td>
    <td align="right" style= "border-bottom-width:0.2px; ">{{number_format($subtotal,0,',','.')}}</td>  
  </tr>
  <tr>
    <td align="right" style= "border-bottom-width:0.2px; ">PPN 10%</td>
    <td align="right" style= "border-bottom-width:0.2px; ">{{number_format($subtotal*0.1,0,',','.')}}</td>  
  </tr>
  <tr>
    <td align="right" style= "border-bottom-width:0.2px; ">TOTA INV</td>
    <td align="right" style= "border-bottom-width:0.2px; ">{{number_format($subtotal + $subtotal*0.1,0,',','.')}}</td>  
  </tr>
</table>

<table border="0" width="100%">
    <tr class="nobordersub">
        <td width="70%">&nbsp;</td>
        <td width="30%" align="center"></td>
    </tr>
    <tr class="nobordersub">
        <td width="70%">&nbsp;</td>
        <td width="30%" align="center">PT Nusa International</td>
    </tr>
</table>


@stop

@push('head')
<style>

table th{
 text-align: center !important;
}
th{
 text-align: center !important;
 border-bottom: 0.2px dashed  black;
 font-size: 11pt;
 font-weight: bold !important;
 padding: 5px;
}
td{
 text-align: left !important;
 
 border-left: 0.2px dashed  black;
 border-right: 0.2px dashed  black;
 /*border-left: 1px solid black;*/
 /*border-right: 1px solid black;*/
 font-size: 10pt;
 /*font-weight: bold !important;*/
 padding: 5px;
}

.perkiraan td{
 text-align: left !important;
 /*border-top: 0.2px dashed  black;*/
 /*border-bottom: 0.2px dashed  black;*/
 border-left: 0.2px dashed  black;
 border-right: 0.2px dashed  black;
 border-radius: 25px;
 font-size: 10pt;
 padding: 5px;
}

.noborderperk td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: 0.2px dashed  black;
 border-right: 0.2px dashed  black;
 font-size: 11pt;
 /*font-weight: bold !important;*/
 padding: 2px;
}

.noborder td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: none;
 border-right: none;
 font-size: 8pt;
 
 padding: 5px;
}

.nobordersub td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: none;
 border-right: none;
 font-size: 11pt;
 
 padding: 5px;
}
</style>
@endpush
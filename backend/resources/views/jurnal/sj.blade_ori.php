@extends('layouts.simple')

@section('maincontent')


<table width="100%" border="1" cellpadding="2" cellspacing="0" height="700px">
  <tr class="perkiraan">
    <td width="10%" align="center" style= "border-top-width:2px"><strong>No</strong></td>
    <td width="18%" align="center" style= "border-top-width:2px"><strong>Product No</strong></td>
    <td width="60%" align="center" style= "border-top-width:2px"><strong>Description</strong></td>
    <td width="12%" align="center" style= "border-top-width:2px"><strong>Qty</strong></td>
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
        <td>{{$ju->kode_barang}}</td>
        <td>{{$ju->itembarang->perkiraan->nama_perk}} <br/> SIZE : <br/>
        {{$ju->size}} <br/>   
        </td>
        <td align="right">{{number_format($ju->qty_kirim,0,',','.')}}</td>
        
        
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
        <td></td>
        <td><br/>  <br/>
        <br/>   
        </td>
        <td align="right"></td>
       
        
    </tr>
    <?php         
        } 
    ?>
    <tr>
        <td style= "border-bottom-width:2px;"></td>
        <td style= "border-bottom-width:2px;"></td>
        <td style= "border-bottom-width:2px;"></td>
        <td style= "border-bottom-width:2px;"></td>
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
 border-bottom: 0.2px solid black;
 font-size: 11pt;
 font-weight: bold !important;
 padding: 5px;
}
td{
 text-align: left !important;
 
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 /*border-left: 1px solid black;*/
 /*border-right: 1px solid black;*/
 font-size: 10pt;
 /*font-weight: bold !important;*/
 padding: 5px;
}

.perkiraan td{
 text-align: left !important;
 border-top: 0.2px solid black;
 border-bottom: 0.2px solid black;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 font-size: 10pt;
 padding: 5px;
}

.noborderperk td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
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
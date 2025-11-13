@extends('layouts.simple')

@section('maincontent')


<table width="100%" border="1" cellpadding="2" cellspacing="0" height="700px">
  <tr>
    <td width="10%" align="center"><strong>No</strong></td>
    <td width="18%" align="center"><strong>Product No</strong></td>
    <td width="60%" align="center"><strong>Description</strong></td>
    <td width="12%" align="center"><strong>Qty</strong></td>
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
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>




@stop

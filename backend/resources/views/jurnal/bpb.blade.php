@extends('layouts.simple')

@section('maincontent')

<table width="100%" border="0" cellpadding="2" cellspacing="2">
  <tr class="noborder">
    <td width="73%" rowspan="2" align="center">
        <span style="font-size: 20pt">&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;<u>BUKTI PENERIMAAN BARANG (BPB)</u></span>
    </td>
    <td width="6%" align="left"><b>Nomor</b></td>
    <td width="3%" align="center">:</td>
    <td width="18%" align="left"><b>{{$jurnalH['bpb_no']}}</b></td>
  </tr>  
  <tr class="noborder">
    <td align="left">Tanggal</td>
    <td align="center">:</td>
    <td align="left">{{\Carbon\Carbon::parse($jurnalH['bpb_tgl'])->translatedFormat('j F Y')}}</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="2" cellspacing="0">
  <tr>
      <td colspan="6">
        <table cellpadding="0" cellspacing="0">
            <tr class="noborder">
                <td align="left" width="6%">Supplier : </td>
                <td align="left" width="30%">{{$jurnalH->supplier->nama}}</td>
                <td align="left" width="6%">No. OP : </td>
                <td align="left" width="28%">{{$jurnalH->po_no}} </td>
                <td align="left" width="10%">Surat Jalan No : </td>
                <td align="left" width="20%">{{$jurnalH->sj_no}}</td>
              </tr> 
        </table>
      </td>
  </tr>  
  
  
  <tr class="perkiraan">
    <td width="10%" align="center" valign="middle"><strong>Banyaknya</strong></td>
    <td width="8%" align="center"><strong>Satuan</strong></td>
    <td width="15%" align="center"><strong>Kode Barang</strong></td>
    <td width="37%" align="center"><strong>URAIAN</strong></td>
    <td width="30%" align="center"><strong>Catatan atas kondisi barang yang diterima</strong></td>
  </tr>
  
  <?php
    $total = 0;
    $data['tanggal'] = \Carbon\Carbon::parse($jurnalH['bpb_tanggal'])->format('Y-m-d');
  ?>
  @foreach($jurnalD as $ju)
    <tr>
        <td align="right">{{$ju->qty_terima}}</td>
        <td>{{$ju->itembarang->satuan}}</td>
        <td align="center">{{$ju->kode_perk}} - {{$ju->kode_barang}}</td>
        <td>{{$ju->itembarang->uraian}}</td>
        <td align="center">{{$ju->catatan}}</td>
    </tr>
  @endforeach
  
</table>

<x-ttd-bpb :mod-name="$modname" :jenis="$jenis" :data="$data"/>

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
 border-top: 0.2px solid black;
 border-bottom: 0.2px solid black;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 /*border-left: 1px solid black;*/
 /*border-right: 1px solid black;*/
 font-size: 11pt;
 /*font-weight: bold !important;*/
 padding: 5px;
}

.perkiraan td{
 text-align: left !important;
 border-top: 0.2px solid black;
 border-bottom: 0.2px solid black;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 font-size: 11pt;
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
 font-size: 11pt;
 
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
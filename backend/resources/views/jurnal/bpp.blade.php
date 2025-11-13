@extends('layouts.simple')

@section('maincontent')
<?php
  $total = 0;
?>
@foreach($jurnalD->chunk(26) as $chunk)
@if (!$loop->first)
<br pagebreak="true"/>
@endif
<table width="100%" border="0" cellpadding="2" cellspacing="2">
    <tr class="noborder">
      <td width="100%" align="center">
          <span style="font-size: 14pt"><u>BUKTI PERMINTAAN DAN PENGELUARAN BARANG (BPP)</u></span>
      </td>
    </tr>
    <tr class="noborder">
        <td>&nbsp;</td>
    </tr>
    <tr class="noborder">
      <td width="10%" align="left"><b>Nomor</b></td>
      <td width="3%" align="center">:</td>
      <td width="70%" align="left"><b>{{$jurnalH['bpp_no']}}</b></td>
    </tr>
    <tr class="noborder">
      <td align="left">Tanggal</td>
      <td align="center">:</td>
      <td align="left">{{\Carbon\Carbon::parse($jurnalH['tanggal'])->translatedFormat('j F Y')}}</td>
    </tr>
  </table>

<table width="100%" border="1" cellpadding="5" cellspacing="0">
  <tr class="perkiraan">
    <td width="15%" align="center" rowspan="2"><strong><div style="font-size:3pt">&nbsp;</div>Kode Barang.</strong></td>
    <td width="25%" align="center" rowspan="2"><strong><div style="font-size:3pt">&nbsp;</div>Nama Barang</strong></td>
    <td width="10%" align="center" rowspan="2"><strong><div style="font-size:3pt">&nbsp;</div>Satuan</strong></td>
    <td width="35%" align="center" colspan="3"><strong>Jumlah</strong></td>
    <td width="15%" align="center" rowspan="2"><strong><div style="font-size:3pt">&nbsp;</div>Catatan</strong></td>
  </tr>
  <tr class="perkiraan">
      <td align="center" width="10%"><strong>Minta</strong></td>
      <td align="center" width="10%"><strong>Keluar</strong></td>
      <td align="center" width="15%"><strong>Harga</strong></td>
  </tr>


  @foreach($chunk as $ju)
  <tr class="noborderperk">
    <td>{{$ju['kode_perk']}} - {{$ju['kode_barang']}}</td>
    <td>{{$ju['itembarang']['uraian']}}</td>
    <td>{{$ju['itembarang']['satuan']}}</td>
    <td align="right">&nbsp;{{($ju->qty_pesan==0 ? '' : number_format($ju->qty_pesan,2,",","."))}}</td>
    <td align="right">&nbsp;{{($ju->qty_terima==0 ? '' : number_format($ju->qty_terima,2,",","."))}}</td>
    <td align="right">{{$ju->harga}}</td>
    <td>&nbsp;</td>
  </tr>
  <?php
    $total = $total + $ju->qty_terima;
  ?>
  @endforeach

@if ($loop->last)
<tr>
    <td align="center" colspan="4"><strong>JUMLAH</strong></td>
    <td align="right"><strong>{{number_format($total,2,",",".")}}</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6">
    <table cellpadding="0" cellspacing="0" border="0">
        <tr class="nobordersub">
            <td height="30" valign="top" width="12%">Penjelasan</td>
            <td width="3%">:</td>
            <td width="84%">{{$jurnalH->uraian}}</td>
        </tr>
    </table>
    </td>
  </tr>
@endif
</table>
@endforeach
<table width="100%" border="0" cellpadding="5" cellspacing="0">

</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr class="noborder">
        <td>&nbsp;</td>
    </tr>
    <tr class="noborder">
        <td width="25%" height="100px" align="center">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
                <tr class="noborder">
                    <td width="100%" align="center">Diminta oleh:</td>
                </tr>
                <tr class="noborder" >
                    <td align="center" height="30px">{{$jurnalH->pemohon_jabatan}}</td>
                </tr>
                <tr class="noborder">
                    <td height="44">&nbsp;</td>
                </tr>
                <tr class="noborder">
                    <td align="center"><b><u>{{$jurnalH->pemohon}}</u></b></td>
                </tr>
                <tr class="noborder">
                    <td align="center">Tanggal : .............</td>
                </tr>
            </table>
        </td>

        <td width="25%" height="100px" align="center">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
                <tr class="noborder">
                    <td width="100%" align="center">Disetujui oleh:</td>
                </tr>
                <tr class="noborder">
                    <td align="center" height="30px">{{$jurnalH->disetujui_jabatan}}</td>
                </tr>
                <tr class="noborder">
                    <td height="44">&nbsp;</td>
                </tr>
                <tr class="noborder">
                    <td align="center"><b><u>{{$jurnalH->disetujui}}</u></b></td>
                </tr>
                <tr class="noborder">
                    <td align="center">Tanggal : .............</td>
                </tr>
            </table>
        </td>

        <td width="25%" height="100px" align="center">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
                <tr class="noborder">
                    <td width="100%" align="center">Dikeluarkan oleh gudang:</td>
                </tr>
                <tr class="noborder">
                    <td align="center" height="30px">{{$jurnalH->mengeluarkan_jabatan}}</td>
                </tr>
                <tr class="noborder">
                    <td height="44">&nbsp;</td>
                </tr>
                <tr class="noborder">
                    <td align="center"><b><u>{{$jurnalH->mengeluarkan}}</u></b></td>
                </tr>
                <tr class="noborder">
                    <td align="center">Tanggal : .............</td>
                </tr>
            </table>
        </td>

        <td width="25%" height="100px" align="center">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
                <tr class="noborder">
                    <td width="100%" align="center">Yang menerima barang:</td>
                </tr>
                <tr class="noborder">
                    <td align="center" height="30px">{{$jurnalH->penerima_jabatan}}</td>
                </tr>
                <tr class="noborder">
                    <td height="44">&nbsp;</td>
                </tr>
                <tr class="noborder">
                    <td align="center"><b><u>{{$jurnalH->penerima}}</u></b></td>
                </tr>
                <tr class="noborder">
                    <td align="center">Tanggal : .............</td>
                </tr>
            </table>
        </td>
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
 font-size: 9pt;
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
 font-size: 9pt;

 padding: 5px;
}

.perkiraan td{
 text-align: left !important;
 border-top: 0.2px solid black;
 border-bottom: 0.2px solid black;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 font-size: 9pt;
 padding: 5px;
}

.noborderperk td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 font-size: 9pt;

 padding: 5px;
}

.noborder td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: none;
 border-right: none;
 font-size: 9pt;

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

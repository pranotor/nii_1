@extends('layouts.simple')

@section('maincontent')

<table width="100%" border="0" cellpadding="5" cellspacing="0">
  <tr>
      <td colspan="4">
          <table cellspacing="0" cellpadding="1" border="0">
              <tr class="noborder">
                <td rowspan="3" width="15%"><img src="{{$LOGO}}"/></td>
                <td width="88%">&nbsp;&nbsp;&nbsp;<span style="font-size: 12px">{{$PEMKOT}}</span></td>
              </tr>
              <tr class="noborder">
                <td><div style="font-size:6pt">&nbsp;</div>&nbsp;&nbsp;&nbsp;<span style="font-size: 14px; font-weight:bold">{{$LEMBAGA}}</span></td>
              </tr>
              <tr class="noborder">
                <td><div style="font-size:6pt">&nbsp;</div>&nbsp;&nbsp;&nbsp;<span style="font-size: 10px;">{{$ALAMAT}}</span></td>
              </tr>
          </table>
      </td>
  </tr>
  <tr>
      <td colspan="4" class="ttd">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr class="noborder">
                <td colspan="4" align="center"><h1><u>K U I T A N S I</u></h1></td>
            </tr>
            <tr class="noborder">
                <td colspan="4" align="center"><i>Nomor : {{$jurnalH['referensi']}}</i></td>
            </tr>
            <tr class="noborder">
              <td width="23%">Telah Terima dari</td>
              <td width="2%">:</td>
              <td width="75%" colspan="2">PERUSAHAAN DAERAH AIR MINUM</td>
            </tr>
            <?php
               $total = 0;
            ?>
            @foreach($jurnalD as $ju)
            <?php
              if($ju->kode == '50.01.10' || $ju->kode == '50.02.10' || $ju->kode == '50.02.00' || $ju->kode == '50.01.00')
                $total = $total + $ju->kredit;
            ?>
            @endforeach
            <tr class="noborder">
              <td>Terbilang</td>
              <td>:</td>
              <td colspan="2" class="border">{{terbilang($total)}} Rupiah</td>
            </tr>
            <tr class="noborder">
              <td width="23%">Uang Pembayaran</td>
              <td width="2%">:</td>
              <td width="75%" colspan="2">{{$jurnalH->uraian}}</td>
            </tr>
            <tr class="noborder">
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr class="noborder">
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr class="noborder">
              <td width="23%"><b><i>Jumlah Rp</i></b></td>
              <td width="2%">:</td>
              <td width="35%"><strong>{{number_format($total,2,",",".")}}</strong></td>
              <td width="30%" align="center"><strong>{{$KOTKAB}}, {{ \Carbon\Carbon::parse($jurnalH->tanggal)->translatedFormat('j F Y')}}<br/>Yang menerima</strong></td>
            </tr>
            <tr class="noborder">
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr class="noborder">
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr class="noborder">
                <td colspan="4">&nbsp;</td>
            </tr>
           
          </table>
      </td> 
  </tr>
  <?php
    $data['total'] = $total;
    $data['tanggal'] = \Carbon\Carbon::parse($jurnalH['tanggal'])->format('Y-m-d');
?>
  <x-ttd-jurnal :mod-name="$modname" :jenis="$jenis" tipe="kuitansi" :data="$data"/>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr class="noborder">
        <td>&nbsp;</td>
    </tr>
</table>

@stop

@push('head')
<style>

.table th{
 text-align: center !important;
}
th{
 text-align: center !important;
 border-bottom: 0.2px solid black;
 font-size: 10pt;
 font-weight: bold !important;
 padding: 20px;
}
td{
 text-align: left !important;
 border-top: 0.2px solid black;
 border-bottom: 0.2px solid black;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 /*border-left: 1px solid black;*/
 /*border-right: 1px solid black;*/
 font-size: 20pt;
 /*font-weight: bold !important;*/
 padding: 20px;
}

.perkiraan td{
 text-align: left !important;
 border-top: 0.2px solid black;
 border-bottom: 0.2px solid black;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 font-size: 10pt;
 padding: 20px;
}

.noborderperk td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 font-size: 10pt;
 /*font-weight: bold !important;*/
 /*padding: 20px;*/
}

.noborder td{
 text-align: left !important;
 border-top: none;
 border-bottom: none;
 border-left: none;
 border-right: none;
 font-size: 11pt !important;
 /*font-weight: bold !important;*/
 padding: 40px !important;
}

td.border{
 text-align: left !important;
 border-top: 0.5px solid black;
 border-bottom: 0.5px solid black;
 border-left: 0.5px solid black;
 border-right: 0.5px solid black;
 /*border-left: 1px solid black;*/
 /*border-right: 1px solid black;*/
 font-size: 10pt;
 /*font-weight: bold !important;*/
 padding: 30px;
}

.ttd td{
 text-align: left !important;
 border-top: 0.2px solid black;
 border-bottom: 0.2px solid black;
 border-left: 0.2px solid black;
 border-right: 0.2px solid black;
 /*border-left: 1px solid black;*/
 /*border-right: 1px solid black;*/
 font-size: 9pt;
 /*font-weight: bold !important;*/
 padding: 20px;
}
</style>
@endpush
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr class="noborder">
        <td>&nbsp;</td>
    </tr>
    <tr class="noborder">
        <?php
            $size = intval(floor(100/sizeof($arr_ttd))); 
            //dd(intval(floor($size)));
        ?>
        @foreach($arr_ttd as $ttd)
        <td width="{{$size}}%" align="center">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
                <tr class="noborder">
                    <td width="100%" align="center">{{$ttd['fungsi']}}</td>
                </tr>
                <tr class="noborder">
                    <td align="center">{{$ttd['jabatan']}}</td>
                </tr>
                <tr class="noborder">
                    <td height="44">&nbsp;</td>
                </tr>
                <tr class="noborder">
                    <td align="center"><b><u>{{$ttd['nama']}}</u></b></td>
                </tr>
                <tr class="noborder">
                    <td align="center">NIK : {{$ttd['nip']}}</td>
                </tr>
            </table>
        </td>
        @endforeach
    </tr>
</table>
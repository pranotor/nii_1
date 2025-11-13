<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Jenis;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/ju','JuController');
    Route::apiResource('/aktiva','AktivaController');
    Route::apiResource('/item','ItemController');
    Route::apiResource('/jbk','JbkController');
    Route::apiResource('/bpb','BpbController');
    Route::apiResource('/po','POController');
    Route::apiResource('/quotation','QuotationController');
    Route::apiResource('/wo','WoController');
    Route::apiResource('/produksi','ProduksiController');
    Route::apiResource('/cutting','CuttingController');
    Route::apiResource('/sj','SJController');
    Route::apiResource('/piutang','PiutangController');
    Route::apiResource('/bpp','BppController');
    Route::apiResource('/satker','SatkerController');
    Route::apiResource('/perkiraan','PerkiraanController');
    Route::apiResource('/masterbank','MasterbankController');
    Route::apiResource('/rekanan','RekananController');
    Route::apiResource('/customer','CustomerController');
    Route::apiResource('/sales','SalesController');
    Route::apiResource('/pegawai','PegawaiController');
    Route::apiResource('/penggunaan','PenggunaanController');
    Route::apiResource('/nomorfp','NomorFpController');
    Route::apiResource('/bankin','BankinController');
    Route::get('/nomorfp_avail','NomorFpController@list_available');
    //Route::get('/masterbank','MasterBankController@list_available');

    Route::apiResource('/role','RoleController');
    Route::apiResource('/userapp','UserController');
    Route::apiResource('/tarif','TarifController');

    Route::apiResource('/drd','DrdController');
    //Route::apiResource('/drd','DrdController');

    Route::get('/customerblokir','CustomerController@blokir');
    Route::get('/customerblokir/{ref}','CustomerController@blokir');
    Route::get('/customeraktif','CustomerController@custaktif');
    Route::post('/customerblokir','CustomerController@blokirproc');
    Route::get('/bpblist','BpbController@bpblist');
    Route::get('/refsales','RefController@sales');
    Route::get('/refmarket','RefController@market');
    Route::get('/refkota','RefController@kota');
    Route::post('/referensi', 'ReferensiController');

    Route::post('/jurnallist','JuController@jurnallist');
    Route::post('/dvudlist','JbkController@dvudlist');
    Route::post('/aktivalist','AktivaController@index');
    Route::get('/bayarlist/{ref}','JbkController@bayarlist');
    Route::get('/bayardata/{ref}','JbkController@bayardata');
    Route::post('/polist','POController@polist');
    Route::post('/quotationlist','QuotationController@quotationlist');
    Route::post('/quotationhistory','QuotationController@quotationhistory');
    Route::post('/quotationdet','QuotationController@quotationdet');
    Route::post('/quotationdh','QuotationController@quotationdh');
    Route::get('/quotation/{id}/{tgl}','QuotationController@show');
    Route::post('/cekpocust','QuotationController@cekpocust');
    Route::post('/sjhistory','SJController@sjhistory');
    Route::post('/sjdet','SJController@sjdet');
    Route::get('/sjbyno/{ref}','SJController@sjbyno');
    Route::post('/retur','SJController@retur');
    Route::post('/solist','QuotationController@solist');
    Route::post('/samplelist','QuotationController@samplelist');
    Route::post('/wolist','WoController@wolist');
    Route::post('/woitem','WoController@woitem');
    Route::post('/produksilist','ProduksiController@index');
    Route::post('/produksidet','ProduksiController@produksidet');
    Route::post('/cuttinglist','CuttingController@index');
    Route::post('/cuttingdet','CuttingController@cuttingdet');
    Route::post('/invoicinglist','PiutangController@invoicinglist');
    Route::get('/invoicexls','PiutangController@invoice_xls');
    Route::post('/invoice_fp','PiutangController@invoice_fp');
    Route::post('/invoice_bank','PiutangController@invoice_bank');
    Route::post('/returlist','SJController@returlist');
    Route::post('/returbelilist','BpbController@returbelilist');
    Route::post('/returbeli','BpbController@returbeli');
    Route::post('/invoicelist','PiutangController@invoicelist');
    Route::post('/invoiceblmkirim','PiutangController@invoiceblmkirim');
    Route::post('/invoicekirim','PiutangController@invoicekirim');
    Route::post('/piutanglist','PiutangController@piutanglist');
    Route::post('/piutanggrid','PiutangController@piutangGrid');
    Route::post('/piutangbayarret','PiutangController@historybayarret');
    Route::post('/bankindet','BankinController@bankindet');
    Route::get('/banktrans/{ref}','BankinController@show');
    Route::post('/bpblist','BpbController@bpblist');
    Route::post('/bpbdet','BpbController@bpbdet');
    Route::post('/bpplist','BppController@bpplist');
    Route::post('/susutlist','AktivaController@susutlist');
    Route::post('/aktivasusut','AktivaController@susutproses');
    Route::post('/susutbatal','AktivaController@susutbatal');
    Route::post('/anggaranlist','AnggaranController@anggaranlist');
    Route::post('/aktivakoreksi','AktivaController@koreksi');
    Route::post('/aktivahapus','AktivaController@hapus');
    Route::post('/menulist','MenuController@menulist');
    Route::get('/laporanlist','MenuController@laporanlist');

    Route::post('/jurnaldelete','JuController@jurnaldelete');
    Route::post('/unpostingbpb','BpbController@unposting');
    Route::post('/convtoso','QuotationController@convert');
    Route::post('/unpostingbpp','BppController@unposting');
    Route::get('/pdf/{ref}', 'JuController@pdf');
    Route::get('/bpppdf/{ref}', 'BppController@pdf');
    Route::get('/bpbpdf/{ref}', 'BpbController@pdf');
    Route::get('/popdf/{ref}', 'POController@pdf');
    Route::get('/quotationpdf/{ref}', 'QuotationController@pdf');
    Route::get('/invoicepdf/{ref}', 'PiutangController@pdf');
    Route::get('/pdftt/{ref}', 'PiutangController@pdftt');
    Route::post('/tt_list', 'PiutangController@tt_list');
    Route::post('/tt_detail', 'PiutangController@tt_detail');

    Route::get('/getPO/{ref}', 'POController@getPO');
    Route::get('/getSO/{ref}', 'QuotationController@getSO');

    Route::post('/lppinit','LppController@lppinit');
    Route::post('/lppsubmit','LppController@store');
    Route::post('/lpplist','LppController@lpplist');
    Route::post('/lppdelete','LppController@lppdelete');


    Route::get('/hitung_stok', 'ItemController@hitung_ulang_stock');
    Route::get('/hitung_saldo_awal', 'ItemController@hitung_saldo_awal');

    //--laporan
    Route::post('/neracasaldo','laporan\NeracasaldoController@store');
    Route::post('/worksheet','laporan\WorksheetController@store');
    Route::post('/neraca','laporan\NeracaController@store');
    Route::post('/lr','laporan\LrController@store');
    Route::post('/stock','laporan\StockController@store');
    Route::post('/nilaistock','laporan\StocknilaiController@store');
    Route::post('/ledger','laporan\LedgerController@store');
    Route::post('/biaya','laporan\BiayaController@store');
    Route::post('/rekapvoucher','laporan\VoucherController@store');
    Route::post('/rekapvcrrekanan','laporan\VoucherRekananController@store');
    Route::post('/rekapjurnal','laporan\JurnalController@store');
    Route::post('/rekapaktiva','laporan\RekapaktivaController@store');
    Route::post('/daftaraktiva','laporan\DaftaraktivaController@store');
    Route::post('/kartustock','laporan\KartustockController@store');
    Route::post('/kartustockharga','laporan\KartustockHargaController@store');
    Route::post('/penjualan','laporan\PenjualanController@store');
    Route::post('/pembelian','laporan\PembelianController@store');
    Route::post('/aruskas','laporan\AruskasController@store');

    // Closing month
    Route::post('/closing_month','ClosingController@closing_month');

    //--end laporan
    Route::get('/module','ModuleController@index');
    Route::get('/menu','MenuController@index');
});
Route::get('/bppjurnal/{ref}','BppController@bppjurnal');
Route::apiResource('/tandatangan','TandatanganController');
Route::get('/itemlist','ItemController@itemlist');
Route::get('/koreksibpp','BppController@koreksi');
Route::get('/koreksi_ct','ItemController@koreksi_ct');
Route::get('/koreksi_prod','ItemController@koreksi_prod');
Route::get('/koreksi_sj','ItemController@koreksi_sj');
Route::get('/sjprint/{ref}', 'PiutangController@sjprint');
Route::get('/invprint/{ref}', 'PiutangController@invprint');
Route::get('/invoicexls/{ref}', 'PiutangController@invoicexls');
Route::get('/piutangxls/{tgl}', 'PiutangController@piutangxls');
Route::get('/fp/{ref}', 'PiutangController@fp');
Route::get('/fpcsv/{ref}', 'PiutangController@fpcsv');
Route::get('/hariancsv/{tgl}', 'PiutangController@hariancsv');
Route::get('/custxls', 'CustomerController@custxls');


Route::apiResource('/userapp','UserController');
Route::get('/jenis', function(){
    return Response::json(Jenis::get());
});
Route::get('/perklist','PerkiraanController@perklist');

Route::get('/testpdf','laporan\NeracaController@store');
Route::get('/testns','laporan\NeracasaldoController@test');
Route::get('/susutawal', 'AktivaController@susutawal');
Route::get('/getPO/{ref}', 'POController@getPO');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

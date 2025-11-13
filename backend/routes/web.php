<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::Resource('/ju','JuController');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/cek_overdue', 'PiutangController@cek_overdue');
Route::get('/piutangxls/{tgl}', 'PiutangController@piutangxls');
Route::get('/custxls', 'CustomerController@custxls');

//Route::get('/pdf/{ref}', 'JuController@pdf');

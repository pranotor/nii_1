<?php

namespace App\Http\Controllers;

use App\Penggunaan;
use App\Rekanan;
use Illuminate\Http\Request;
use Response;
use DB;

class PenggunaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Penggunaan::get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function show(Perkiraan $perkiraan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Perkiraan $perkiraan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Perkiraan $perkiraan)
    {
        //
    }
}

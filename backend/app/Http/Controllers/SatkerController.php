<?php

namespace App\Http\Controllers;

use App\Satker;
use Illuminate\Http\Request;
use Response;
use DB;

class SatkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Satker::get());
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
     * @param  \App\Satker  $satker
     * @return \Illuminate\Http\Response
     */
    public function show(Satker $satker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Satker  $satker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Satker $satker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Satker  $satker
     * @return \Illuminate\Http\Response
     */
    public function destroy(Satker $satker)
    {
        //
    }
}

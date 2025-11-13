<?php

namespace App\Http\Controllers;

use App\Tarif;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreRekanan;

class TarifController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Tarif::get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRekanan $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $data_inp['kode'] = $data['formdata']['kode'];
            $data_inp['nama'] = $data['formdata']['nama'];
            $data_inp['alamat'] = $data['formdata']['alamat'];
            if($data['formdata']['isEdit']) {
                Rekanan::where('kode',$data['formdata']['prevReferensi'])->update($data_inp);
            }
            else{
                Rekanan::create($data_inp);
            }
                
               
                
        }, 5);  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function show(Rekanan $rekanan)
    {
        return Response::json($rekanan);
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

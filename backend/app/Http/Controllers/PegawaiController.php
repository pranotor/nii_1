<?php

namespace App\Http\Controllers;

use App\Pegawai;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StorePegawai;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Pegawai::orderBy('nama')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePegawai $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $data_inp['nik'] = $data['formdata']['nik'];
            $data_inp['nama'] = $data['formdata']['nama'];
            $data_inp['jabatan'] = $data['formdata']['jabatan'];
            if($data['formdata']['isEdit']) {
                Pegawai::where('nik',$data['formdata']['prevReferensi'])->update($data_inp);
            }
            else{
                Pegawai::create($data_inp);
            }
        }, 5);  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function show(Pegawai $pegawai)
    {
        return Response::json($pegawai);
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
    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
    }
}

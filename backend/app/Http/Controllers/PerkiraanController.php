<?php

namespace App\Http\Controllers;

use App\Perkiraan;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StorePerkiraan;
use Facade\FlareClient\Http\Response as HttpResponse;

class PerkiraanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return   Response::json(Perkiraan::select(DB::raw('kode_perk, CONCAT(RPAD(kode_perk,15,"="),"+",nama_perk) as nama,level'))
                                ->doesntHave('subperkiraan')
                                ->orderBy('kode_perk','ASC')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePerkiraan $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $data_inp['kode_perk'] = $data['formdata']['kode_perk'];
            $data_inp['nama_perk'] = $data['formdata']['nama_perk'];
            $data_inp['parent'] = $data['formdata']['kode_depan'];
            if($data['formdata']['isEdit']) {
                Perkiraan::where('kode_perk',$data['formdata']['prevReferensi'])->update($data_inp);
            }
            else{
                Perkiraan::create($data_inp);
            }
        }, 5);  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function show(Perkiraan $perkiraan)
    {
        return Response::json($perkiraan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function perklist(Request $request){

        return Response::json(Perkiraan::select(DB::raw('kode_perk, CONCAT(RPAD(kode_perk,15," ")," -- ",nama_perk) as nama_perk,parent,level'))->get());
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

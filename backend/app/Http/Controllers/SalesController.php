<?php

namespace App\Http\Controllers;

use App\Sales;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreCustomer;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Sales::orderBy('nama')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomer $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $data_inp['kode'] = $data['formdata']['kode'];
            $data_inp['nama'] = $data['formdata']['nama'];
            $data_inp['alamat'] = $data['formdata']['alamat'];
            $data_inp['kota'] = $data['formdata']['kota'];
            $data_inp['propinsi'] = $data['formdata']['propinsi'];
            $data_inp['telepon'] = $data['formdata']['telepon'];
            $data_inp['kontak_person'] = $data['formdata']['kontak_person'];
            if($data['formdata']['isEdit']) {
                Customer::where('kode',$data['formdata']['prevReferensi'])->update($data_inp);
            }
            else{
                Customer::create($data_inp);
            }



        }, 5);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return Response::json($customer);
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
    public function destroy(Customer $customer)
    {
        //
    }
}

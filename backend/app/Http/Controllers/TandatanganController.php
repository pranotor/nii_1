<?php

namespace App\Http\Controllers;

use App\Tandatangan;
use App\Ttdetail;
use App\Drd;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreTtd;
use Illuminate\Support\Facades\Auth;
use PDF;


class TandatanganController extends Controller
{
    var $username = '';
    
    public function index()
    {
        //filter jenis dan tahun

        $query = DB::select("SELECT t.id,t.mod_name,m.name,CONCAT(j.`uraian`,' ',IFNULL(t.`tipe`,' ')) as tipe FROM t_ttd AS t 
                            LEFT JOIN module_controller AS m ON m.`controller` = t.`mod_name`
                            LEFT JOIN jnstrans AS j ON t.`jenis` = j.`jns`"); 
        //dd(collect($query));
        return Response::json($query);
    }

    public function store(StoreTtd $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
                if(!$data['formdata']['isEdit']) {
                    //Tandatangan::where('id',$data['formdata']['prevReferensi'])->delete();
                    //Ttdetail::where('ttd_id',$data['formdata']['prevReferensi'])->delete();
                    $ttd_inp = new Tandatangan();
                    $ttd_inp->mod_name = $data['formdata']['mod_name'];
                    $ttd_inp->jenis = $data['formdata']['jenis'];
                    $ttd_inp->tipe = $data['formdata']['tipe'];
                    $ttd_inp->save();
                    $ttd_id = $ttd_inp->id;
                }
                else{
                    $ttd = Tandatangan::find($data['formdata']['prevReferensi']);
                    $ttd->touch();
                    $ttd_id = $data['formdata']['prevReferensi'];
                }
                
                $ordinal = 1;
                foreach($data['formdata']['datatrans'] as $ttd_detail){
                    $ttd_detail['ttd_id'] = $ttd_id;
                    $ttd_detail['ordinal'] = $ordinal;
                    Ttdetail::create($ttd_detail);
                    $ordinal++;
                }
        }, 5);  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Jurnal  $jurnal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /* return Response::json(Tandatangan::where('id',$id)->with('pejabat')->first()); 
        //dd(Tandatangan::find($id)->get()); */
        $query_ttd = Tandatangan::where('id',$id)->get()->map(function ($item) {
            $item['pejabat'] = Ttdetail::whereDate('created_at',$item->updated_at)->orderBy('ordinal')->get();
            return $item;
        });
        return Response::json($query_ttd); 

    }

    
    public function destroy(Jurnal $jurnal)
    {
        //
    }

    
}

<?php

namespace App\Http\Controllers;

use App\Role;
use App\MenuRole;
use App\Http\Requests\StoreRole;
use Illuminate\Http\Request;
use Response;
use DB;
use App\User;
use Illuminate\Support\Arr;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Role::orderBy('id','asc')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRole $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $data_inp['role_name'] = $data['formdata']['role_name'];
            if($data['formdata']['isEdit']) {
                Role::where('id',$data['formdata']['prevReferensi'])->update($data_inp);
                MenuRole::where('role_id',$data['formdata']['prevReferensi'])->delete();
                $id = $data['formdata']['prevReferensi'];
            }
            else{
                $role = Role::create($data_inp);
                $id = $role->id;
            }
            //insert menu_role
            $arr_parent = array();
            foreach($data['formdata']['datatrans'] as $jurnal){
                $data_det['menu_id'] = $jurnal['id'];
                $data_det['role_id'] = $id;
                $data_det['create_auth'] = $jurnal['create_auth'];
                $data_det['edit_auth'] = $jurnal['edit_auth'];
                $data_det['delete_auth'] = $jurnal['delete_auth'];
                if($jurnal['parentId'] != 0){
                    if(!in_array($jurnal['parentId'],$arr_parent)){
                        array_push($arr_parent,$jurnal['parentId']);
                        $data_parent['menu_id'] = $jurnal['parentId'];
                        $data_parent['role_id'] = $id;
                        $data_parent['create_auth'] = false;
                        $data_parent['edit_auth'] = false;
                        $data_parent['delete_auth'] = false;
                        MenuRole::create($data_parent);
                    }
                    MenuRole::create($data_det);
                }
            }
        }, 5);  
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\JenisPengadaan  $jenisPengadaan
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return Response::json($role->load('wewenang.menudetail'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JenisPengadaan  $jenisPengadaan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::where('id',$id)->delete();
        MenuRole::where('role_id',$id)->delete();
    }
}

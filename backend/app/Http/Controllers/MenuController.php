<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuRole;
use App\Http\Requests\StoreKbli;
use Illuminate\Http\Request;
use Response;
use DB;
use App\User;
use Illuminate\Support\Arr;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //check role_id
        $role_id = $request->user()->role_id;
        if($role_id != 1) {
            $menu_user = $request->user()->userrole->menus()->pluck('id');
            $menu = Menu::where('parent',0)->where('public',1)->whereIn('id', $menu_user)
                    ->with(['child' => function($query) use ($menu_user){
                        $query->where('public',1)->whereIn('id', $menu_user);
                    }])->get();
        }
        else{
            $menu = Menu::where('parent',0)->where('public',1)
                    ->with(['child' => function($query) {
                        $query->where('public',1);
                    }])->get();
        }
        $menu_collect = $menu->filter()->all();
        return Response::json($menu_collect);
    }

    public function menulist(Request $request){

        $data = json_decode($request->getContent(), true);
        
        if(isset($data['role_id'])){
            /* $sql_menu = "SELECT id,`name`,parent,(SELECT IF(COUNT(menu_id)=0,'FALSE','TRUE') FROM menu_role WHERE role_id=".$data['role_id']." AND menu_id=t.id ) AS checked,
                         IFNULL((SELECT IF(create_auth=0,'FALSE','TRUE') FROM menu_role WHERE role_id=".$data['role_id']." AND menu_id=t.id),'FALSE') AS create_auth,
                         IFNULL((SELECT IF(edit_auth=0,'FALSE','TRUE') FROM menu_role WHERE role_id=".$data['role_id']." AND menu_id=t.id),'FALSE') AS edit_auth,
                         IFNULL((SELECT IF(delete_auth=0,'FALSE','TRUE') FROM menu_role WHERE role_id=".$data['role_id']." AND menu_id=t.id),'FALSE') AS delete_auth
                         FROM t_menu AS t";
            $q_menu = DB::select($sql_menu); */
            $q_menu = Menu::selectRaw("id,`name`,parent")->get()->map(function($item) use ($data){
                $item['checked_menu'] = MenuRole::selectRaw("IF(COUNT(menu_id)=0,FALSE,TRUE) as checked")
                                   ->where('role_id',$data['role_id'])
                                   ->where('menu_id',$item->id)
                                   ->first();
                $item['wewenang'] = MenuRole::selectRaw("IFNULL(IF(create_auth=0,'FALSE','TRUE'),'FALSE') as create_auth,
                                    IFNULL(IF(edit_auth=0,'FALSE','TRUE'),'FALSE') as edit_auth,
                                    IFNULL(IF(delete_auth=0,'FALSE','TRUE'),'FALSE') as delete_auth")
                                    ->where('role_id',$data['role_id'])
                                    ->where('menu_id',$item->id)
                                    ->first();
                return $item;
            });
        }
        else
            $q_menu = Menu::select('id','name','parent')->get();
        return Response::json($q_menu);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreKbli $request)
    {
        
    }

    public function laporanlist(Request $request)
    {
        //check role_id
        $role_id = $request->user()->role_id;
        if($role_id != 1) {
            $menu_user = $request->user()->userrole->menus()->pluck('id');
            $menu = Menu::where('parent',9)->whereIn('id', $menu_user)->get();
        }
        else{
            $menu = Menu::where('parent',9)->get();
        }
        $menu_collect = $menu->filter()->all();
        return Response::json($menu_collect);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JenisPengadaan  $jenisPengadaan
     * @return \Illuminate\Http\Response
     */
    public function destroy(JenisPengadaan $jenisPengadaan)
    {
        //
    }
}

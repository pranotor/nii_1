<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\StoreUser;
use Illuminate\Http\Request;
use Response;
use DB;

use Illuminate\Support\Arr;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('test');
        // Fetch closing_month from t_closing (assumes single-row config table)
        $closing = DB::table('t_closing')->select('closing_month')->first();
        $closingMonth = $closing ? $closing->closing_month : null;

        // Return users list, each augmented with closing_month to avoid breaking array-based consumers
        $users = User::with('userrole')->orderBy('name')->get()->map(function($u) use ($closingMonth) {
            $u->setAttribute('closing_month', $closingMonth);
            $u->setAttribute('is_admin', 'ok');
            return $u;
        });

        return Response::json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            $data_inp['name'] = $data['formdata']['name'];
            $data_inp['email'] = $data['formdata']['email'];
            $data_inp['role_id'] = $data['formdata']['role_id'];
            $data_inp['password'] = bcrypt($data['formdata']['password']);
            if($data['formdata']['isEdit']) {
                User::where('id',$data['formdata']['prevReferensi'])->update($data_inp);
            }
            else{
                User::create($data_inp);
            }
        }, 5);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JenisPengadaan  $jenisPengadaan
     * @return \Illuminate\Http\Response
     */
    public function show(User $userapp)
    {
        return Response::json($userapp);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JenisPengadaan  $jenisPengadaan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id',$id)->delete();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class ListController extends Controller
{
    public function index()
    {
//        echo $_SERVER['HTTP_REFERER'];
        $CItemmaniaKeyMasterDb = DB::connection("ItemmaniaKeyDbConnection");
        $rgCryptoHelper = $CItemmaniaKeyMasterDb
            -> select("SELECT * FROM crypto_helper");
        return view('admin.index', ['rgData' => $rgCryptoHelper, 'title' => 'KMS 관리자']);
    }
    public function create()
    {


        return view('admin.test', ['rgData' => '', 'title' => '회원 정보']);
    }

    public function first_page()
    {
            if(Auth::check() == true){
                return redirect('/admin');
            }else{
                return redirect('/login');
            }

    }


}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    function index(){
        $a= new MainController;
        $a->verify();
        return view('admin.index');
    }

    /**
     * party controllers
     */


    /**
     * verify if is a admin
     */
    function verify(){
        $me = Auth::user();
        if ($me==null){
            abort('401', 'Unauthorized');
        }
        if ($me->id != 1){
            abort('401', 'Unauthorized');
        }
    }
}

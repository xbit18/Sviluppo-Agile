<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    function user_create(){
    $a= new AdminController;
    $a->verify();
    return view('admin.forms.user.user_create');
    }
    function index(){
        $a= new AdminController;
        $a->verify();
        return view('admin.index');
    }

    function users(){
        $a= new AdminController;
        $a->verify();
        $users = User::all();
        return view('admin.forms.user.all_users',compact('users'));
    }

    protected function user_store(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        $user= new User();
            $user->name=$request['name'];
            $user->email=$request['email'];
            $user->password= Hash::make($request['password']);
            $user->save();
        return view('admin.index');

    }
    function verify(){
        $me = Auth::user();
        if ($me==null){
            abort('401', 'Unauthorized');
        }
        if ($me->email != 'static@e.it'){
            abort('401', 'Unauthorized');
        }

    }
}

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
    return view('admin.forms.user.new_user');
    }
    function index(){
        $a= new AdminController;
        $a->verify();
        return view('admin.index');
    }

    function users(){
        $a= new AdminController;
        $a->verify();
        $users = User::paginate(10);
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
    protected function user_update(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        $user= User::findOrFail($request->id);
        $user->name=$request['name'];
        $user->email=$request['email'];
        if($request->password != 'passwordnoncambiata'){
        $user->password = Hash::make($request['password']);

        }

        $user->save();
        return redirect()->route('users.index');

    }

    protected function user_edit($id)
    {
        $a= new AdminController;
        $a->verify();
        $user= User::findOrFail($id);
        return view('admin.forms.user.new_user',compact('user'));

    }

    protected function user_delete(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        $user=User::findOrFail($request->id);
        $user->delete();
        return back();
    }
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

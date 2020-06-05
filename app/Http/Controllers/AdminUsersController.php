<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUsersController extends Controller
{

    /**
     * user controllers
     */

    function index(){
        $a= new AdminController;
        $a->verify();
        if(request('email')!=null) {
            $key = request('email');
            $users = User::where('email', $key)->get();
            return view('admin.forms.user.index',compact('users'));
        }
        else {
            $users = User::paginate(10);
            return view('admin.forms.user.index',compact('users'));
        }
    }

    function create(){
        $a= new AdminController;
        $a->verify();
        return view('admin.forms.user.create');
    }

    protected function store(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            return \Redirect::back()->withErrors([$emailErr]);
        }
        if (User::where('email',$request->email)->first()) {
            $emailErr = "this mail is already used";
            return \Redirect::back()->withErrors([$emailErr]);
        }
        $user= new User();
        $user->name=$request['name'];
        $user->email=$request['email'];
        $user->email_verified_at = date('Y-m-d');
        $user->password= Hash::make($request['password']);
        $user->save();
        return redirect()->route('users.index')->with('success', 'User '. $request->email.' created!');

    }
    protected function update(Request $request)
    {
        $a = new AdminController;
        $a->verify();
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            return \Redirect::back()->withErrors([$emailErr]);
        }
        if ($request->old_email != $request->email){
            if (User::where('email', $request->email)->first()) {
                $emailErr = "this mail is already used";
                return \Redirect::back()->withErrors([$emailErr]);
            }}

        $user= User::findOrFail($request->id);
        $user->name=$request['name'];
        $user->email=$request['email'];
        if($request->password != 'passwordnoncambiata'){
            $user->password = Hash::make($request['password']);

        }

        $user->save();
        return redirect()->route('users.index')->with('success', 'User '. $request->email.' updated!');

    }

    protected function edit($id)
    {
        $a= new AdminController;
        $a->verify();
        $user= User::findOrFail($id);
        return view('admin.forms.user.edit',compact('user'));

    }

    protected function delete(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        $user=User::findOrFail($request->id);
        $email=$user->email;
        $user->delete();
        return back()->with('success', 'User '. $email.' deleted!');
    }
}

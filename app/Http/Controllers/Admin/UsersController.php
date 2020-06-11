<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Party;
use App\Track;
use App\User;
use App\UserBanUser;
use App\UserParticipatesParty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    /**
     * user controllers
     */

    function index(){
        $a= new MainController;
        $a->verify();
        if(request('email')!=null) {
            $key = request('email');
            $users = User::where('email', $key)->get();
            return view('admin.forms.user.index',compact('users'));
        }
        else {
            $users = User::all();
            return view('admin.forms.user.index',compact('users'));
        }
    }

    function create(){
        $a= new MainController;
        $a->verify();
        return view('admin.forms.user.create');
    }

    protected function store(Request $request)
    {
        $a= new MainController;
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
        $a = new MainController;
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
        $a= new MainController;
        $a->verify();
        $user= User::findOrFail($id);
        return view('admin.forms.user.edit',compact('user'));

    }

    protected function delete(Request $request)
    {
        $a= new MainController;
        $a->verify();
        $user=User::findOrFail($request->id);
        $email=$user->email;
        $user->delete();
        return back()->with('success', 'User '. $email.' deleted!');
    }

    function joinparty(Request $request)
    {
        $a= new MainController;
        $a->verify();
        $party=Party::where('code',$request->code)->first();
        if($party == null) {
            return back()->with('Success','cant find party');
        }
        $ban=UserBanUser::where('user_id',$party->user_id)->where('ban_user_id',$request->id)->first();
        if($ban != null){
            return back()->with('Success','can\'t add, user banned');
        }

        $partecipate = new UserParticipatesParty();
        $partecipate->user_id =$request->id;
        $partecipate->party_id= $party->id;
        $partecipate->save();

        return back()->with('Success','Joined successfully');
    }
    function leaveparty(Request $request)
    {
        $a= new MainController;
        $a->verify();
        $partecipate = UserParticipatesParty::where('user_id',$request->id)->where('party_id',$request->party)->first();
        if($partecipate->vote != null){
            $track = Track::where('id',$partecipate->vote)->first();
            $track->votes = $track->votes-1;
            $track->save();
        }
        $partecipate->delete();
        return back()->with('Success','Kicked successfully');
    }
}

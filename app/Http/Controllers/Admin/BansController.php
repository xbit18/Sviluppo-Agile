<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\UserBanUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $a = new MainController;
        $a->verify();
        if (request('email') != null) {
            $key = request('email');
            $key = User::where('email', $key)->first()->id;
           $bans = UserBanUser::where('user_id', $key)->get();
            return view('admin.forms.ban.index', compact('bans'));
        } else {
            $bans = UserBanUser::all();
            return view('admin.forms.ban.index', compact('bans'));
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $a= new MainController;
        $a->verify();
        return view('admin.forms.ban.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user=User::where('email',$request->user)->first();
        $banned=User::where('email',$request->banned)->first();
        if ($user == null or $banned==null)
        {
            return redirect()->back()->withErrors(['some user not found']);
        }
        if($user->id == $banned->id)
        {
            return redirect()->back()->withErrors(['you cant ban yourself']);
        }
        $ban=UserBanUser::where([
            ['user_id', '=', $user->id],
            ['ban_user_id', '=', $request->user]
        ])->first();
        if($ban==null){
            $ban=new UserBanUser();
            $ban->user_id=$user->id;
            $ban->ban_user_id=$banned->id;
            $ban->save();

            return redirect()->route('admin.ban.index')->with('success', 'User '. $request->banned.' banned by '.$request->user.' successfully!');
        }
        else{
            return redirect()->back()->withErrors(['user already banned']);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::find($request->id);
        $banned = User::where('email', $request->banned)->first();
        if ($user == null or $banned == null) {
            return redirect()->back()->withErrors(['user to ban not found']);
        }
        if ($user->id == $banned->id) {
            return redirect()->back()->withErrors(['you cant ban yourself']);
        }

        $ban = UserBanUser::where([
            ['user_id', '=', $user->id],
            ['ban_user_id', '=', $request->old_ban]
        ])->first();
        if ($ban != null) {
            $ban->user_id=$user->id;
            $ban->ban_user_id=$banned->id;
            $ban->save();
            return redirect()->back()->with('success', 'ban updated!');
        }
        else{
            return redirect()->back()->with(['success','something goes wrong']);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $a= new MainController;
        $a->verify();
        $user=UserBanUser::findOrFail($request->id);
        $user->delete();
        return back()->with('success', 'Ban deleted!');
    }

}

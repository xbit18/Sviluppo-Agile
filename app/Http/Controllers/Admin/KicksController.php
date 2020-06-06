<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\UserParticipatesParty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KicksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $a= new MainController;
        $a->verify();
        $kicks = UserParticipatesParty::where('kick_duration','<>',null);
        return view('admin.forms.kick.index',compact('kicks'));

        /*
        if(request('email')!=null) {
            $key = request('email');
            $users = User::where('email', $key)->get();
            return view('admin.forms.kick.index',compact('users'));
        }
        else {
            $users = User::paginate(10);
            return view('admin.forms.kick.index',compact('users'));
        }*/

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
        return view('admin.forms.kick.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $user=User::where('email',$request->email)->first();
            if(Auth::user()->id == $user->id)
        {
            return redirect()->back()->withErrors(['you cant unkick yourself']);
        }
        $party=UserParticipatesParty::where('user_id',$user->id)->first(); // user che voglio kickare
        if($party == null) {
            return redirect()->back()->withErrors(['User not partecipate in any parties']);
        }
        $party->timestamp_kick=Carbon::now();

        if($request->time < Carbon::now()){
            return redirect()->back()->withErrors(['kick time cannot be earlier than now']);
        }

        $party->kick_duration=$request->time;
        $party->save();
        return redirect()->back()->with('success', 'The user kicked successfully');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

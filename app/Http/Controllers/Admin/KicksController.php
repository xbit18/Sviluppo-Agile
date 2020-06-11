<?php

namespace App\Http\Controllers\Admin;

use App\Party;
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
        if (request('email') != null) {
        $key = request('email');
        $key = User::where('email', $key)->first()->id;
        $kicks = UserParticipatesParty::where('user_id',$key)->where('kick_duration', '<>', null)->get();
        return view('admin.forms.kick.index', compact('kicks'));
    } else {
            $kicks = UserParticipatesParty::where('kick_duration', '<>', null)->get();
            return view('admin.forms.kick.index', compact('kicks'));
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
            return redirect()->back()->withErrors(['you cant kick yourself']);
        }
            $p = Party::where('code',$request->code)->first();
        if($p == null)
        {
            return redirect()->back()->withErrors(['Party not found']);
        }
        $party=UserParticipatesParty::where('user_id',$user->id)->where('party_id',$p->id)->first(); // user che voglio kickare
        if($party == null) {
            return redirect()->back()->withErrors(['User not partecipate in this party']);
        }
        $party->timestamp_kick=Carbon::now();

        if($request->time < Carbon::now()){
            return redirect()->back()->withErrors(['kick time cannot be earlier than now']);
        }

        $party->kick_duration=$request->time;
        $party->save();
        return redirect()->route('admin.kick.index')->with('success', 'The user kicked successfully');
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
        $user=User::find($request->id);
        $p = Party::find($request->party);
        if($p == null)
        {
            return redirect()->back()->withErrors(['Party not found']);
        }
        $party=UserParticipatesParty::where('user_id',$user->id)->where('party_id',$p->id)->first(); // user che voglio kickare
        if($party == null) {
            return redirect()->back()->withErrors(['User not partecipate in this party']);
        }
        $party->timestamp_kick=Carbon::now();

        if($request->time < Carbon::now()){
            return redirect()->back()->withErrors(['kick time cannot be earlier than now']);
        }

        $party->kick_duration=$request->time;
        $party->save();
        return redirect()->route('admin.kick.index')->with('success', 'The user kicked successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $party=UserParticipatesParty::find($request->id); // user che voglio kickare
        if($party == null) {
            return redirect()->back()->with('Success','User not partecipate in any parties');
        }
        $party->timestamp_kick=null;
        $party->kick_duration=null;
        $party->save();
        return redirect()->back()->with('success', 'The user kicked successfully');
    }
}

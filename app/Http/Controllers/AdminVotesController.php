<?php

namespace App\Http\Controllers;

use App\Track;
use App\User;
use App\UserParticipatesParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminVotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $a= new AdminController;
        $a->verify();
        if(request('email')!=null) {
            $key = request('email');
            $users = User::where('email', $key)->get();
            return view('admin.forms.vote.index',compact('users'));
        }
        else {
            $users = User::paginate(10);
            return view('admin.forms.vote.index',compact('users'));
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $a= new AdminController;
        $a->verify();
        return view('admin.forms.vote.create');
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
        if($user==null)
        {
            return redirect()->back()->withErrors(['this email doesn\'t exist']);
        }
        $party=UserParticipatesParty::where('user_id',$user->id)->first();
        if($party == null) {
            return redirect()->back()->withErrors(['User not partecipate in any parties']);
        }
        if($party->vote==false) {
            $party->vote = true;
            $track_to_vote = $request->track_id;

            $track = Track::find($track_to_vote);
            if($track == null)
            {
                return redirect()->back()->withErrors(['this track doesnt exist']);
            }

            $track->vote = $track->vote + 1;
            $party->save();
            $track->save();
            return redirect()->route('admin.vote.index')->with('success', 'track voted!');
        }
        else{
            return redirect()->back()->withErrors(['you have already voted!']);
        }
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

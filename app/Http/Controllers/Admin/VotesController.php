<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Party;
use App\Track;
use App\User;
use App\UserParticipatesParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotesController extends Controller
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
        if(request('email')!=null) {
            $key = request('email');
            $key = User::where('email', $key)->first()->id;
            $votes = UserParticipatesParty::where('user_id',$key)->where('vote', '<>', null)->get();
            return view('admin.forms.vote.index',compact('votes'));
        }
        else {
            $votes = UserParticipatesParty::where('vote', '<>', null)->get();

            return view('admin.forms.vote.index', compact('votes'));
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
        $x=Party::where('code',$request->code)->first();
        if($x == null) {
            return redirect()->back()->withErrors(['Party doesn\'t exist']);
        }
        $party=UserParticipatesParty::where('user_id',$user->id)->where('party_id',$x->id)->first();
        if($party == null) {
            return redirect()->back()->withErrors(['User not partecipate in this party']);
        }
        if($party->vote==false) {
            $party->vote = $request->track_id;
            $track_to_vote = $request->track_id;

            $track = Track::find($track_to_vote);
            if($track == null)
            {
                return redirect()->back()->withErrors(['this track doesnt exist']);
            }

            $track->votes = $track->vote + 1;
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
    public function update(Request $request)
    {
        $vote=UserParticipatesParty::find($request->id);
        $track = Track::where('id',$vote->vote)->first();
        $track->votes -=1;
        $track->save();
         $request->track_id;
        $track = Track::find($request->track_id);
        $track->votes += 1;
        $track->save();
        $vote->vote=$request->track_id;
        $vote->save();
        return redirect()->back()->with('success','vote changed successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $vote=UserParticipatesParty::find($request->id);
        if($vote == null) { return redirect()->back();}
        $track=Track::find($vote->vote);
        $track->votes -= 1;
        $track->save();
        $vote->vote=null;
        $vote->save();
        return redirect()->back()->with('success','vote deleted!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Track;
use App\User;
use App\UserParticipatesParty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartyManagerController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi di request : track_id // traccia che si vuole votare
     */
   public function vote(Request $request){
        $user=Auth::user()->id;
        $party=UserParticipatesParty::where('user_id',$user)->first();
        if($party->vote==false) {
            $party->vote = true;
            $party->save();

            $track_to_vote = $request->track_id;
            $track = Track::findorFail($track_to_vote);
            $track->vote = $track->vote - 1;
            $track->save;
            return redirect()->back()->with('success', 'track voted!');
        }
        else{
            return redirect()->back()->withErrors(['you have already voted!']);
        }
   }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi request : track_id // traccia che si vuole unvotare
     */

    public function unvote(Request $request){
        $user=Auth::user()->id;
        $party=UserParticipatesParty::where('user_id',$user)->first();
        if($party->vote==true) {
            $party->vote = false;
            $party->save();

            $track_to_vote = $request->track_id;
            $track = Track::findorFail($track_to_vote);
            $track->vote = $track->vote - 1;
            $track->save;
            return redirect()->back()->with('success', '');
        }
        else{
            return redirect()->back()->withErrors(['you haven\'t voted yet']);
        }
    }

    /**
     * @param Request $request
     * campi request :
     * user // user che si vuole cacciare
     * time // tempo di kick
     */
   public function kick(Request $request){
       $party=UserParticipatesParty::where('user_id',$request->user)->first(); // user che voglio kickare
       $party->timestamp_kick=Carbon::now();
       if($request->time < Carbon::now()){
           return redirect()->back()->withErrors(['kick time cannot be earlier than now']);
       }

       $party->kick_duration=$request->time;
       $party->save();
       return redirect()->back()->with('success', 'The user kicked successfully');
   }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi request :
     * user // user che si vuole rimettere
     */


    public function unkick(Request $request){
        $party=UserParticipatesParty::where('user_id',$request->user)->first(); // user che voglio kickare
        $party->timestamp_kick=null;
        $party->kick_duration=null;
        $party->save();
        return redirect()->back()->with('success', 'The user kicked successfully');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi request:
     *  user // id user che si vuole bannare
     */


   public function ban(Request $request){
       $user=Auth::user();
       $ban=\UserBanUser::where([
               ['user_id', '=', $user->id],
               ['ban_user_id', '=', $request->user]
           ])->first();
       if($ban==null){
           $ban->user_id=$user->id;
           $ban->ban_user_id=$request->user;
           $ban->save();

           $banned_user = User::find($request->id)->email;
           return redirect()->back()->with('success', 'User '. $banned_user.' banned!');
       }
       else{
           return redirect()->back()->withErrors(['user already banned']);
       }
   }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi request:
     *  user // id user che si vuole unbannare
     */


    public function unban(Request $request){
        $user=Auth::user();
        $ban=\UserBanUser::where([
            ['user_id', '=', $user->id],
            ['ban_user_id', '=', $request->user]
        ])->first();
        if($ban!=null){
            $ban->delete();
            $banned_user = User::findOrFail($request->user)->email;
            return redirect()->back()->with('success', 'User '. $banned_user.' unbanned!');
        }
        else{
            return redirect()->back()->withErrors(['the user is not banned']);
        }
    }
}

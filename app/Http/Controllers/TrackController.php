<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Party;
use App\Track;



class TrackController extends Controller
{
    //
    public function getMostVotedSong($code) {
        $party = Party::where('code', $code)->first();
        
        $res = $party->tracks()->orderByDesc('votes')->first();

        return $res;

    }

    public function deleteTrackFromPlaylist($code, $track_uri) {
        $party = Party::where('code', $code)->first();
        
        if(Auth::user()->id == $party->user->id ) {

            $party->tracks()->where('track_uri', $track_uri)->delete();

            return response()->json('completed');

        }
        abort(401);
    }

    public function addTrackToPlaylist(Request $request, $code){

        $party = Party::where('code', $code)->first();

        if(Auth::user()->id == $party->user->id ) {

            $party->tracks()->create([
                'track_uri' => $request->track_uri
            ]);

            return response()->json(['message' => 'song added']);

        }

        abort(401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi di request : track_id // traccia che si vuole votare
     */
   public function vote($code, $track_uri){

        $user=Auth::user();

        $party = Party::where('code',$code)->first();

        $user_participates = $party->users()->where('user_id','=',$user->id)
        ->where('party_id','=',$party->id)->first()->pivot;

        if(!$user_participates){
            return response()->json([
                'message' => 'You do not participate in this party!'
            ]);
        }


        if($user_participates->vote != true) {

            $track = $party->tracks()->where('track_uri',$track_uri)->first();
            if($track == null) {
                return response()->json([
                    'message' => 'track not found'
                ]);
            }
            $track->votes += 1;
            $track->save();
            $user->participates()->updateExistingPivot($party->id,['vote' => true]);
            return response()->json([
                'message' => 'track voted successfully'
            ]);
        }
        else{
            return response()->json([
                'message' => 'You have already voted'
            ]);
        }

}

/**
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 * campi request : track_id // traccia che si vuole unvotare
 */

public function unvote($code, $track_uri){

        $user=Auth::user();

        $party = Party::where('code',$code)->first();

        $user_participates = $party->users()->where('user_id','=',$user->id)
        ->where('party_id','=',$party->id)->first()->pivot;

        if(!$user_participates){
            return response()->json([
                'message' => 'You do not participate in this party!'
            ]);
        }


        if($user_participates->vote != false) {

            $track = $party->tracks()->where('track_uri',$track_uri)->first();
            if($track == null) {
                return response()->json([
                    'message' => 'track not found'
                ]);
            }
            $track->votes -= 1;
            $track->save();
            $user->participates()->updateExistingPivot($party->id,['vote' => false]);
            return response()->json([
                'message' => 'track voted successfully'
            ]);
        }
        else{
            return response()->json([
                'message' => 'You have already voted'
            ]);
        }
}
}

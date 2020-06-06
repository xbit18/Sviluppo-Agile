<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Party;
use App\Track;
use App\Events\VoteEvent;


class TrackController extends Controller
{
    //
    public function getMostVotedSong($code) {
        $party = Party::where('code', $code)->first();
        
        $res = $party->tracks()->orderByDesc('votes')->first();

        return $res;

    }

    public function deleteTrackFromPlaylist($code, $id) {
        $party = Party::where('code', $code)->first();
        
        if(Auth::user()->id == $party->user->id ) {

            Track::find($id)->delete();
            
            $votes = $party->users()->where('vote',$id)->get();
            foreach($votes as $vote){
                $vote->participates()->updateExistingPivot($party->id,['vote' => null]);
            }
            return response()->json('completed');

        }
        abort(401);
    }

    public function addTrackToPlaylist(Request $request, $code){

        $party = Party::where('code', $code)->first();

        if(Auth::user()->id == $party->user->id ) {

           $track = $party->tracks()->create([
                'track_uri' => $request->track_uri
            ]);

            return response()->json(['id' => $track->id]);

        }

        abort(401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * campi di request : track_id // traccia che si vuole votare
     */
   public function vote($code, $id){

        $user=Auth::user();

        $party = Party::where('code',$code)->first();

        $user_participates = $party->users()->where('user_id','=',$user->id)
        ->first()->pivot;

        if(!$user_participates){
            return response()->json([
                'message' => 'You do not participate in this party!'
            ]);
        }


        if($user_participates->vote === null) {

            $track = Track::find($id);
            if($track == null) {
                return response()->json([
                    'message' => 'track not found'
                ]);
            }
            $track->votes += 1;
            $track->save();
            broadcast(new VoteEvent($party,$track));
            $user->participates()->updateExistingPivot($party->id,['vote' => $id]);
            return response()->json([
                'message' => 'track voted successfully'
            ]);
        }
        else{
            return response()->json([
                'message' => 'You have already voted, remove your vote before voting again',
                'error'  => true
            ]);
        }

}

/**
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 * campi request : track_id // traccia che si vuole unvotare
 */

public function unvote($code, $id){

        $user=Auth::user();

        $party = Party::where('code',$code)->first();

        $user_participates = $party->users()->where('user_id','=',$user->id)
        ->where('party_id','=',$party->id)->first()->pivot;

        if(!$user_participates){
            return response()->json([
                'message' => 'You do not participate in this party!'
            ]);
        }


        if($user_participates->vote !== null && $user_participates->vote == $id) {

            $track = Track::find($id);
            if($track == null) {
                return response()->json([
                    'message' => 'track not found'
                ]);
            }
            $track->votes -= 1;
            $track->save();
            $user->participates()->updateExistingPivot($party->id,['vote' => null]);
            broadcast(new VoteEvent($party,$track));
            return response()->json([
                'message' => 'track unvoted successfully',
                'error' => false,
            ]);
        }
        else{
            return response()->json([
                'message' => 'You have already voted, please remove your vote before voting again',
                'error' => true,
            ]);
        }
}
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Party;
use App\Track;
use App\Events\VoteEvent;
use App\Events\BattleSelectedEvent;


class TrackController extends Controller
{
    //
    public function getMostVotedSong($code) {

        $party = Party::where('code', $code)->first();

        if($party->type == 'Battle'){
            $res = $party->tracks()->where('active', '!=', 0)->orderByDesc('votes')->first();
            if(empty($res)) return $party->tracks()->first();
        } else
            $res = $party->tracks()->orderByDesc('votes')->first();

        return $res;

    }

    public function resetBattle($code) {
        $party = Party::where('code', $code)->first();
        
        if(Auth::user()->id != $party->user->id ) abort(401);

        foreach($party->users as $user) {
            $party->users()->updateExistingPivot($user->id,['vote' => null]);
        }

        foreach($party->tracks as $track) {
            if($track->active != 0) broadcast(new BattleSelectedEvent(null ,$track->active,$party));
            $track->active = 0;
            $track->save();
            
        }

        return $party->tracks;
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
     * Parametri della request:
     * - track_uri
     * - party_code
     */
    public function setTrackActive(Request $request) {

        $uri = $request->track_uri;
        $party = Party::where('code', $request->party_code)->first();
        
        if(Auth::user()->id != $party->user->id ) abort(401);
        if($party->type != 'Battle') abort(400, 'Bad Request : Il party non è di tipo battle');
        if(!$party->tracks()->where('track_uri', $uri)->get()) abort(400, 'Bad Request : Codice della traccia non esistente nel party');
        if($party->tracks()->where('active', '!=', 0)->count() >= 2) abort(400, 'Bad Request : Track has already 2 active tracks');

        if($request->side == 1 || $request->side == 2) {
            $side = $request->side;
            if($party->tracks()->where('active', $side)->count()) abort(400, 'Bad Request: esiste gia un canzone su quel side');

            $mytrack = $party->tracks->where('track_uri', $uri)->first();
            $mytrack->active = $side;
            $mytrack->save();
            broadcast(new BattleSelectedEvent($mytrack,$side,$party));

            return response()->json(['message' => 'Track Activated']);

        }

        abort(400, 'Bad Request');

        

    }

    /**
     * Parametri della request:
     * - track_uri
     * - party_code
     * - side
     */
    private function setTrackNotActive(Request $request) {

        $uri = $request->track_uri;
        $party = Party::where('code', $request->party_code)->first();
        
        if(Auth::user()->id != $party->user->id ) abort(401);
        if($party->type != 'Battle') abort(400, 'Bad Request : Il party non è di tipo battle');
        if(!$party->tracks()->where('track_uri', $uri)->get()) abort(400, 'Bad Request : Codice della traccia non esistente nel party');

        $mytrack = $party->tracks->where('track_uri', $uri)->first();
        if($mytrack->active) {
            $mytrack->active = 0;
            $mytrack->save();
            return response()->json(['message' => 'Track Resetted']);
        }
        else {
            return response()->json(['message' => 'Track is already not active']);
        }

    }

    /*
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
                'error' => 'You do not participate in this party!'
            ]);
        }


        if($user_participates->vote === null) {

            $track = Track::find($id);
            if($track == null) {
                return response()->json([
                    'error' => 'track not found'
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
                'error' => 'You have already voted, remove your vote before voting again',
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
                'error' => 'You do not participate in this party!'
            ]);
        }


        if($user_participates->vote !== null && $user_participates->vote == $id) {

            $track = Track::find($id);
            if($track == null) {
                return response()->json([
                    'error' => 'track not found'
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
                'error' => 'You have already voted, please remove your vote before voting again',
            ]);
        }
}
}

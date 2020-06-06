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

        if($party->type == 'Battle')
            $res = $party->tracks()->where('active', '!=', 0)->orderByDesc('votes')->first();
        else
            $res = $party->tracks()->orderByDesc('votes')->first();

        return $res;

    }

    public function resetBattle($code) {
        $party = Party::where('code', $code)->first();
        
        if(Auth::user()->id != $party->user->id ) abort(401);

        foreach($party->tracks as $track) {
            $track->active = 0;
            $track->save();
        }

        return $party->tracks;
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

}

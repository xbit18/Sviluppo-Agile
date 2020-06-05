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
}

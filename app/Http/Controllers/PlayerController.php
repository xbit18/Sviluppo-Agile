<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\PlayerPaused;
use App\Events\PlayerPlayed;
use App\Events\PlayerSync;
use App\Party;

class PlayerController extends Controller
{
    /* ------------------- EVENTS ------------------ */

    public function pause($code){
        /*
        Prendo il model Party e lo mando all'evento
        */
        $party = Party::where('code',$code)->first();
        broadcast(new PlayerPaused($party))->toOthers();

        return response()->json(['message' => 'song paused']);
    }

    public function play(Request $request, $code){
        /*
            Prendo il model Party e lo mando all'evento
        */

        $track_uri = $request->track_uri;
        $position_ms = $request->position_ms;
        $party = Party::where('code',$code)->first();
        broadcast(new PlayerPlayed($party, $track_uri, $position_ms))->toOthers();

        return response()->json(['message' => 'song played']);

    }

   
    
    public function syncronize(Request $request, $code){

        $track_uri = $request->track_uri;
        $position_ms = $request->position_ms;
        $user_id = $request->user_id;
        $party = Party::where('code',$code)->first();
        broadcast(new PlayerSync($party, $user_id, $track_uri, $position_ms))->toOthers();

        return response()->json(['message' => 'syncronized']);
    }
}

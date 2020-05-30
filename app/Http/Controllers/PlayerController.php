<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\PlayerPaused;
use App\Events\PlayerPlayed;
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
    }

    public function play(Request $request, $code){
        /*
            Prendo il model Party e lo mando all'evento
        */

        $track_uri = $request->track_uri;
        $position_ms = $request->position_ms;
        $party = Party::where('code',$code)->first();
        broadcast(new PlayerPlayed($party, $track_uri, $position_ms));//->toOthers();

    }
}

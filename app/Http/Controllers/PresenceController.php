<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PresenceController extends Controller
{
    



    public function leave_party($code, $user_id) {

        $user = User::where('id', $user_id)->first();
        $party = Party::where('code', $code)->first();

        $user->participates()->toggle($party->id);

        return;
        
    }
}

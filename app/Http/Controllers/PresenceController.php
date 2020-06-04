<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Party;
use App\User;

class PresenceController extends Controller
{
    
    public function join_party($code, $user_id){
        $user = User::where('id', $user_id)->first();
        $party = Party::where('code', $code)->first();
        $user->participates()->sync($party->id);

        return $user->participates;
    }


    public function leave_party($code, $user_id) {

        $user = User::where('id', $user_id)->first();
        $party = Party::where('code', $code)->first();

        $user->participates()->detach();

        return $user->participates;
        
    }
}

<?php

namespace App\Http\Controllers;

use App\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartyController extends Controller
{
    public function store(Request $request){
        $party = Party::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'genre' => $request->genre,
            'mood' => $request->mood,
            'type' => $request->type,
            'source' => $request->source
        ]);

        return response()->view('party', ['party'=>$party],302);
    }
}

<?php

namespace App\Http\Controllers;

use App\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    /**
     * Mostra il form di creazione del party
     */
    public function create() {
        return view('user.pages.create_party');
    }

    /**
     * Effettua la creazione del party
     */
    public function store(Request $request){
        $party = Party::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'genre' => $request->genre,
            'mood' => $request->mood,
            'type' => $request->type,
            'source' => $request->source
        ]);

        return view('user.pages.party', ['party'=>$party]);
    }
}

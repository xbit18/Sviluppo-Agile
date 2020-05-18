<?php

namespace App\Http\Controllers;

use App\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Genre;

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

        $genre = Genre::where('genre',$request->genre)->first();

        /* Se il genero esiste nel db crea la party */

        if($genre){
        $party = Party::create([
                    'user_id' => Auth::id(),
                    'name' => $request->name,
                    'mood' => $request->mood,
                    'type' => $request->type,
                    'source' => $request->source
                ]);

        $party->genre()->attach($genre->id);

        return view('user.pages.party', ['party'=>$party]);

        } else{
            throw ValidationException::withMessages(['genre' => 'This value is incorrect']);
        }

        



      
    }
}

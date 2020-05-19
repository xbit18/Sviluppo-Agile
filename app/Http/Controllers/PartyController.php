<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Genre;
use App\Party;

class PartyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function get_party_by_user() {
        
        // Controllo (anche se non necessario) se l'utente è loggato 
        if(Auth::check()) {

            $me = Auth::user();
            $genres = Genre::paginate(10);
            $my_party = $me->party;

            return view('user.pages.party', ['party' => $my_party, 'genres' => $genres]);            

        }

    }

    /**
     * Mostra il form di creazione del party
     */
    public function create() {
        $genre_list = Genre::orderBy('genre', 'ASC')->get();
        return view('user.pages.create_party', ['genre_list' => $genre_list]);
    }

    /**
     * Effettua la creazione del party
     */
    public function store(Request $request){

        $validatedData = $request->validate([
            'name' => 'required|string',
            'mood' => 'required|string',
            'type' => 'required|in:Battle,Democracy',
            'desc' => 'required|string',
            'genre' =>'required|array'
        ]);

        $genres = $request->genre;
        $genre_ids = array();

        /**
         * Per ogni genere controllo se esiste 
         */
        $validation = true;
        foreach($genres as $genre_in) {
            /**
             * Ottimizzo memorizzando gli id dei generi durante la validazione
             */
            $genre = Genre::where('genre',$genre_in)->first();
            if(!$genre) $validation = false;
            else array_push($genre_ids, $genre->id);
        }

        if(!$validation) return \Redirect::back()->withErrors(['genre' => 'Invalid Genre']);
        
        $party = Party::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'mood' => $request->mood,
            'type' => $request->type,
            'source' => $request->source,
            'description' => $request->desc,
        ]);

        foreach($genre_ids as $id) {
            $party->genre()->attach($id);
        }


        return redirect()->route('me.party.show');
      
    }
}

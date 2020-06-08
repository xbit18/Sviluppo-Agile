<?php

namespace App\Http\Controllers;

use App\Mail\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Genre;
use App\Party;
use App\User;
use App\Track;
use Carbon\Carbon;
use SpotifyWebApi\SpotifyWebApiException;
use SpotifyWebAPI\SpotifyWebAPI as SpotifyWebAPI;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Validator;
use function MongoDB\BSON\toJSON;

class PartyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    /**
     * Methods for User Views
     */


    /**
     * Mostra i party creati dall'utente autenticato
     */
    public function get_parties_by_user() {

        // Controllo (anche se non necessario) se l'utente è loggato
        if(Auth::check()) {

            $me = Auth::user();
            $my_parties = $me->parties()->orderBy('created_at','DESC')->get();

            $my_parties->map(function ($party) {
                $party->genre_id = $party->genre->first()->id;
            });

            return view('user.pages.my_parties', ['parties' => $my_parties]);

        }

    }

    /**
     * Mostra un party in base al codice
     * Gestisce l'inserimento nel database nella tabella partecipanti
     * al momento della join.
     */
    public function show($code){


        $party = Party::where('code','=',$code)->first();

        $user = Auth::user();

        if(!$party){
            return response(['error' => 'This party does not exist'], 404);
        }
        $user->participates()->syncWithOutDetaching($party->id);
        $genre_list = Genre::orderBy('genre', 'ASC')->get();
        $genres = Genre::paginate(10);
        $party->genre_id = ( $party->genre->first()->id % 20 ) + 1; 
        $liked = $party->users()->where('user_id','=',$user->id)->first()->pivot->vote;

        if($party->type == 'Battle') {
            $side1 = $party->tracks()->where('active', 1)->first();
            $side2 = $party->tracks()->where('active', 2)->first();

            return view('user.pages.party_battle', ['party' => $party, 'genres' => $genres, 'genre_list' => $genre_list, 'side_1' => $side1, 'side_2' => $side2, 'liked' => $liked]);
        }

        return view('user.pages.party_democracy', ['party' => $party, 'genres' => $genres, 'genre_list' => $genre_list, 'liked' => $liked]);

    }

    /**
     * Mostra i party attualmente sul sistema dal più recente
     */
    public function index() {
        if(request('name')!=null) {
            $key = request('name');
            $parties = Party::where('name', $key)->get();
            if(!$parties){
                return response(['error' => 'This party does not exist'], 404);
            }
            $parties->map(function ($party) {
                $party->genre_id = $party->genre->first()->id;
            });
            $parties->sortBy('id');

            return view('user.pages.parties',compact('parties'));
        }

        $parties = Party::all();

        if(!$parties){
            return response(['error' => 'This party does not exist'], 404);
        }

        $parties->map(function ($party) {
            $party->genre_id = $party->genre->first()->id;
        });
        $parties->sortBy('id');
        $parties_sorted =  $parties->reverse();

        return view('user.pages.parties',  ['parties' => $parties_sorted]);
    }

    /**
     * Restituisce il form per modificare un party
     */
    public function edit($code){
        $party = Party::where('code','=',$code)->first();
        $party_genres = $party->genre;
        $genre_list = Genre::orderBy('genre', 'ASC')->get();
        return view('user.pages.edit_party', ['party'=> $party,  'party_genres'=>$party_genres, 'genre_list'=>$genre_list]);
    }

    /**
     * Modifica i parametri del party
     */
    public function update(Request $request, $code){

        /**
         * Valida i campi della richiesta
         */

        $rules = array(
            'mood' => 'required|string',
            'type' => 'required|in:Battle,Democracy',
            'desc' => 'required|string',
            'genre' =>'required|array|max:5'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $genres = $request->genre;

        //$genre_ids = array();

        /**
         * Faccio prima tutte le validazioni così evito operazioni inutili
         * Per ogni genere controllo se esiste
         */
        $validation = true;
        foreach($genres as $genre_in) {
            /**
             * Ottimizzo memorizzando gli id dei generi durante la validazione
             *
             * Ottimizzazione usando direttamente gli id e ed evitando tutte le iterazioni
             */

            if(!Genre::find($genre_in)) return redirect()->back()->withErrors(['genre' => 'Invalid Genre ' . $genre_in]);
            //$genre = Genre::where('genre',$genre_in)->first();
            //if(!$genre) $validation = false;
            //else array_push($genre_ids, $genre_in);
        }

        //if(!$validation) return \Redirect::back()->withErrors(['genre' => 'Invalid Genre']);





        $party = Party::where('code','=',$code)->first();
        $party->mood = $request->mood;
        $party->type = $request->type;
        $party->description = $request->desc;
        /**$party->name = $request->name;                Da decidere*/


        $party->genre()->detach();

        foreach($genres as $id) {
            $party->genre()->attach($id);
        }

        /**
         * Se i dati del party sono stati cambiati, salva i cambiamenti
         */
        if($party->isDirty()) $party->save();

        /**
         * Faccio il redirect alla pagina del party aggiornata con le nuove informazioni
         */
        return redirect()->route( 'party.show', [ 'code' => $party->code ] );

        //return response()->json(['success' => 'Party Updated Successfully']);
    }

    /**
     * Mostra la view di creazione del party
     */
    public function create() {
        $user = Auth::user();
        $genre_list = Genre::orderBy('genre', 'ASC')->get();
        return view('user.pages.create_party', ['genre_list' => $genre_list]);


    }

    /**
     * Effettua la creazione del party, quindi anche la creazione della playlist
     * con lo stesso nome del party sull'account spotify
     */
    public function store(Request $request){

        $validatedData = $request->validate([
            'name' => 'required|string',
            'mood' => 'required|string',
            'type' => 'required|in:Battle,Democracy',
            'desc' => 'required|string',
            'genre' =>'required|array|max:5'
        ]);

        $user = Auth::user();
        $api = new SpotifyWebAPI();

        try {

            $api->setAccessToken($user->access_token);
            /*
            $playlist = $api->createPlaylist([
            'name' => $request->name,
            'public' => false
            ]);
                */

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

            /**
             * Genero un nuovo codice finchè sono certo che sia unico all'interno del db
             */
            do{
                $code = Str::random(16);
            }
            while(Party::where('code', '=', $code)->exists());

            $party = Party::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'mood' => $request->mood,
                'type' => $request->type,
                'source' => $request->source,
                'description' => $request->desc,
                'code' => $code,
                //'playlist_id' => $playlist->id
            ]);

            foreach($genre_ids as $id) {
                $party->genre()->attach($id);
            }

            /**
             * Aggiunta della playlist
             */

            /**
             * Popolazione delle tracce
             */
            $songsByGenre = $this->getSongsByGenre($party->code, null);

            foreach($songsByGenre as $song) {
                $track = Track::create([
                    'party_id' => $party->id,
                    'track_uri' => $song
                ]);
                $party->tracks()->save($track);
            }

            //$bool = $api->addPlaylistTracks($playlist->id,$songsByGenre);
            //if($bool){
            return redirect()->route('me.parties.show');

        //}
        } catch (SpotifyWebApiException $e){
            return redirect()->route('spotify.login');
        }


    }

    /**
     * Invia una mail a tutti gli utenti selezionati con il link per accedere al party
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function invite(Request $request, $code) {

        /**
         * Controllo se il party esiste
         */
        $party = Party::where('code', '=', $code)->first();

        /**
         * Logica di analisi stringa di utenti della forma
         * (Nome - email),(Nome - email),(Nome - email)
         *
         * MIGLIORABILE IN TERMINI DI OTTIMIZZAZIONE E FUNZIONALITA'
         *
         * 1 - Ottengo l'array usando explode sul carattere ","
         * 2 - Rimuovo le parentesi dagli elementi
         * 3 - Separo il restante in un miniarray di Nome,-,email
         * 4 - prendo soltanto l'email e ho le email degli utenti
         */
        $users_array = explode(",", $request->invite_list);
        $user_emails = array();
        foreach($users_array as $user_item) {

            /**
             * CONTROLLO TRAMITE REGEX SE IL FORMATO E' CORRETTO
             */
            if(!preg_match('/(\([a-zA-Z]+ - [_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})\)){1}/', $user_item)) abort(400, 'La stringa è malformata : ' . $user_item);
            $user_item = str_replace("(", "", $user_item);
            $user_item = str_replace(")", "", $user_item);
            $mini_arr = explode(" ", $user_item);
            if($mini_arr[2])
                array_push($user_emails, $mini_arr[2]);
            else abort(500);
        }

        /**
         * Scorro le email e vedo se sono valide
         */
        $users = User::whereIn('email', $user_emails)->get();
        //return $users;

        /**
         * LOGICA DI INVIO EMAIL
         */
        $link = 'http://127.0.0.1:8000/party/show/'.$code;

        foreach ($user_emails as $recipient) {
            Mail::to($recipient)->send(new Invite($link));
        }

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function populateParty(Request $request){


        $uris = $this->getSongsByGenre(null, $request->genre_id);
        /*$playlist_id = Party::where('code','=',$request->party_code)->first()->playlist_id;
        $api = new SpotifyWebAPI();
        $api->setAccessToken(Auth::user()->access_token);
        $bool = $api->addPlaylistTracks($playlist_id,$uris);*/

        $party = Party::where('code', $request->party_code)->first();
        if(Auth::user()->id != $party->user->id) abort(401);

        foreach($uris as $song) {
            $track = Track::create([
                'party_id' => $party->id,
                'track_uri' => $song
            ]);
            $party->tracks()->save($track);
        }
        return redirect()->back();
    }

    /**
     * Ritorna un array di Spotify track URIs in base ai generi del party
     * @param $party_code
     * @return array
     */
    public function getSongsByGenre($party_code, $genre_id)
    {

        $URI = 'https://api.spotify.com/v1/recommendations?seed_genres=';

        if($genre_id == null && $party_code == null){ return ;}
        if($genre_id != null){
            $genre = strtolower(Genre::findOrFail($genre_id)->genre);
            $URI .= $genre;
        } else {
            $genres = Party::where('code','=',$party_code)->first()->genre;
            foreach($genres as $genre){
                $URI .= strtolower($genre->genre).',';
            }
        }


        try {
            $user_token = Auth::user()->access_token;

            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $user_token])->get($URI);

            $tracks = array();
            if(!$response['tracks']) return redirect()->route('spotify.login');
            $tracks = $response['tracks'];

            $tracks_uris = array();
            foreach ($tracks as $track) {
                $tracks_uris[] = $track['uri'];
            }
            return $tracks_uris;
        } catch (SpotifyWebApiException $e){
            return redirect()->route('spotify.login');
        }

    }

    /**
     * Cancella il party specificato
     * @param $party_code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($party_code){
        $party = Party::where('code','=',$party_code)->first();
        if(Auth::user()->id == $party->user_id){
            $party->delete();
            return redirect()->route('me.parties.show');
        }
        $error = 'Couldn\'t delete party';
        return redirect()->back()->with($error);
    }

    public function getLatestParties(){
        $parties = Party::orderBy('created_at', 'desc')->take(10)->get();
        return $parties;
    }

}

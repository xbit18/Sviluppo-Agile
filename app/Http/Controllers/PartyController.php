<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Events\PlayerPaused;
use App\Events\PlayerPlayed;
use App\Genre;
use App\Party;
use App\User;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI as SpotifyWebAPI;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PartyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function get_parties_by_user() {

        // Controllo (anche se non necessario) se l'utente è loggato
        if(Auth::check()) {

            $me = Auth::user();
            $my_parties = $me->parties;

            $my_parties->map(function ($party) {
                $party->genre_id = $party->genre->first()->id;
            });

            return view('user.pages.my_parties', ['parties' => $my_parties]);

        }

    }

    /**
     * Mostra un party in base al codice
     */
    public function show($code){


        $party = Party::where('code','=',$code)->first();

        if(!$party){
            return response(['error' => 'This party does not exist'], 404);
        }

        $genres = Genre::paginate(10);
        $party->genre_id = $party->genre->first()->id;

        return view('user.pages.party', ['party' => $party, 'genres' => $genres]);

    }

    /**
     * Mostra i party creati
     */
    public function index() {
        $parties = Party::all();

        if(!$parties){
            return response(['error' => 'This party does not exist'], 404);
        }

        $parties->map(function ($party) {
            $party->genre_id = $party->genre->first()->id;
        });

        return view('user.pages.parties',  ['parties' => $parties]);
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
        ]);

        foreach($genre_ids as $id) {
            $party->genre()->attach($id);
        }

        return redirect()->route('me.parties.show');

    }

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
        return $users;

        /**
         * LOGICA DI INVIO EMAIL MANCANTE
         */


    }


    public function getSong() {
        //$path = storage_path().$song->path.".mp3";
        $path = public_path() . "\audio\dummy-audio.mp3";
        $user = Auth::user();
        if($user) {
            $response = new BinaryFileResponse($path);
            BinaryFileResponse::trustXSendfileTypeHeader();
            return $response;
        }
        abort(400);
    }

    /* ------------------- EVENTS ------------------ */

    public function pause($code){

        /*
        Prendo il model Party e lo mando all'evento
        */
        $party = Party::where('code',$code)->first();
        broadcast(new PlayerPaused($party))->toOthers();
    }

    public function play($code){
          /*
        Prendo il model Party e lo mando all'evento
        */
        $party = Party::where('code',$code)->first();
        broadcast(new PlayerPlayed($party))->toOthers();
    }





    /**
     * Spotify API Interaction
     */
    public function load()
    {
        $session = new Session(
            'e2a5fd9ef8654b19ac499e340f8290fe',
            '5fe477929f9a45aea98df7a59347f21a',
            'http://127.0.0.1:8000/callback'
        );


        $options = [
            'scope' => [
                'playlist-read-private',
                'user-modify-playback-state',
                'user-read-playback-state',
                'user-read-currently-playing',
                'user-read-private',
                'user-read-email',
                'streaming'
            ],
        ];

        header('Location: ' . $session->getAuthorizeUrl($options));
        die();

    }

    public function getAuthCode(Request $request){
        
        /**
         * Spotify Session Parameters
         */
        $session = new Session(
            'e2a5fd9ef8654b19ac499e340f8290fe',
            '5fe477929f9a45aea98df7a59347f21a',
            'http://127.0.0.1:8000/callback'
        );

        // Request a access token using the code from Spotify
        $session->requestAccessToken($_GET['code']);

        $accessToken = $session->getAccessToken();
        $user = Auth::user();
        $user->access_token = $accessToken;
        $user->save();

        // Store the access token somewhere. In a database for example.
        return redirect()->back();
    }

    public function logout(){
        $user = User::all()->first();
        if(!$user) return redirect('/');

        $user->delete();
        return redirect('/');
    }


    public function playpause($state)
    {
        $api = new SpotifyWebAPI();

        $user = User::all()->first();
        // Fetch the saved access token from somewhere. A database for example.
        $api->setAccessToken($user->access_token);

        if($state == 'play'){
            $api->play();
        }

        if($state == 'pause'){
            $api->pause();
        }

        return redirect()->back();
    }

    public function page(){
        $user = User::all()->first();
        if(!$user) return redirect('/');

        return view('playback');
    }
}

<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Genre;
use App\Party;
use App\Track;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SpotifyWebAPI\SpotifyWebAPI as SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIException;

class PartiesController extends Controller
{
    function index(){
        $a= new MainController;
        $a->verify();
        if(request('name')!=null) {
            $key = request('name');
            $parties = Party::where('name', $key)->get();
            return view('admin.forms.party.index',compact('parties'));
        }
        else {
            $parties = Party::paginate(10);
            return view('admin.forms.party.index',compact('parties'));
        }
    }
    protected function delete(Request $request)
    {
        $a= new MainController;
        $a->verify();
        $party=Party::findOrFail($request->id);
        $code=$party->code;
        $party->delete();
        return back()->with('success', 'Party '. $code.' deleted!');
    }

    function create(){
        $a= new MainController;
        $a->verify();
        return view('admin.forms.party.create')->with('success', 'Party Created without the playlist ');
    }

    protected function store(Request $request)
    {
        $a = new MainController;
        $a->verify();
        $user = new User();
        $validatedData = $request->validate([
            'email' => 'required|string',
            'name' => 'required|string',
            'mood' => 'required|string',
            'type' => 'required|in:Battle,Democracy',
            'desc' => 'required|string',
            'genre' => 'required|array|max:5'
        ]);
        $genres = $request->genre;
        $genre_ids = array();

        /**
         * Per ogni genere controllo se esiste
         */
        $validation = true;
        foreach ($genres as $genre_in) {
            /**
             * Ottimizzo memorizzando gli id dei generi durante la validazione
             */
            $genre = Genre::where('genre', $genre_in)->first();
            if (!$genre) $validation = false;
            else array_push($genre_ids, $genre->id);
        }

        if (!$validation) return \Redirect::back()->withErrors(['genre' => 'Invalid Genre']);

        $email = $request->email;
        $user = User::where('email', $request->email)->first();

        if ($user == null) return \Redirect::back()->withErrors(['user ' . $email . ' not found']);

        $code = Str::random(16);
        $user_id = $user->id;


       // try {
            $api = new SpotifyWebAPI();
            if($user->access_token != null ) {
                $api->setAccessToken($user->access_token);
            }
            $party = Party::create([
                'user_id' => $user_id,
                'name' => $request->name,
                'mood' => $request->mood,
                'type' => $request->type,
                'source' => $request->source,
                'description' => $request->desc,
                'code' => $code,
            ]);
            foreach ($genre_ids as $id) {
                $party->genre()->attach($id);
            }
        $api = new SpotifyWebAPI();
        if($user->access_token != null ) {
            $api->setAccessToken($user->access_token);

            $songsByGenre = $this->getSongsByGenre($party->code, null);

            foreach ($songsByGenre as $song) {
                $track = Track::create([
                    'party_id' => $party->id,
                    'track_uri' => $song
                ]);
                $party->tracks()->save($track);
            }
            return redirect()->route('admin.party.index')->with('success', 'Party Created WITH the tracks ');
        }

            return redirect()->route('admin.party.index')->with('success', 'Party Created WITHOUT the tracks, token old or not exists');


        /*
            $p = new \App\Http\Controllers\PartyController();
            $songsByGenre = $p->getSongsByGenre($party->code);
            $bool = $api->addPlaylistTracks($playlist->id, $songsByGenre);
            if ($bool) {
                return redirect()->route('admin.party.index')->with('success', 'Party Created with the playlist');
            }
        } catch (SpotifyWebApiException $e) {
            return redirect()->route('admin.party.index')->with('success', 'Party Created WITHOUT the playlist ');
        }*/
    }
    protected function edit($id)
    {
        $a= new MainController;
        $a->verify();
        $party = Party::where('id','=',$id)->first();
        $party_genres = $party->genre;
        $genre_list = Genre::orderBy('genre', 'ASC')->get();
        return view('admin.forms.party.edit', ['party'=> $party,  'party_genres'=>$party_genres, 'genre_list'=>$genre_list]);

    }

    protected function update(Request $request)
    {
        $a= new MainController;
        $a->verify();
        /**
         * Valida i campi della richiesta
         */
        $rules = array(
            'email' => 'required|string',
            'name' => 'required|string',
            'mood' => 'required|string',
            'type' => 'required|in:Battle,Democracy',
            'desc' => 'required|string',
            'source' => 'required|string',
            'genre' =>'required|array|max:5',
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }


        $genres = $request->genre;
        $genre_ids = array();
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

        $user=User::where('email',$request->email)->first();
        if($user == null ) return \Redirect::back()->withErrors(['user '.$request->email.' not found']);

        $party = Party::where('id','=',$request->id)->first();
        $party->user_id = $user->id;
        $party->name = $request->name;
        $party->mood = $request->mood;
        $party->type = $request->type;
        $party->description = $request->desc;
        $party->source = $request->source;
        $party->playlist_id = $request->playlist_id;

        /**$party->name = $request->name;                Da decidere*/


        $party->genre()->detach();

        foreach($genre_ids as $id) {
            $party->genre()->attach($id);
        }
        if($party->isDirty()) $party->save();
        return redirect()->route('admin.party.index')->with('success', 'Party '. $party->code.' updated!');;

    }
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

}

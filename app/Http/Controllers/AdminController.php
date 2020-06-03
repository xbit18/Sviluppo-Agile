<?php

namespace App\Http\Controllers;

use App\Genre;
use App\Party;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    function index(){
        $a= new AdminController;
        $a->verify();
        return view('admin.index');
    }

    /**
     * user controllers
     */

    function users(){
        $a= new AdminController;
        $a->verify();
        $users = User::paginate(10);
        return view('admin.forms.user.index',compact('users'));
    }

    function user_create(){
    $a= new AdminController;
    $a->verify();
    return view('admin.forms.user.create');
    }

    protected function user_store(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        $user= new User();
            $user->name=$request['name'];
            $user->email=$request['email'];
            $user->email_verified_at = date('Y-m-d');
            $user->password= Hash::make($request['password']);
            $user->save();
        return view('admin.index');

    }
    protected function user_update(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        $user= User::findOrFail($request->id);
        $user->name=$request['name'];
        $user->email=$request['email'];
        if($request->password != 'passwordnoncambiata'){
        $user->password = Hash::make($request['password']);

        }

        $user->save();
        return redirect()->route('users.index');

    }

    protected function user_edit($id)
    {
        $a= new AdminController;
        $a->verify();
        $user= User::findOrFail($id);
        return view('admin.forms.user.edit',compact('user'));

    }

    protected function user_delete(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        $user=User::findOrFail($request->id);
        $user->delete();
        return back();
    }
    /**
     * party controllers
     */

    function parties(){
        $a= new AdminController;
        $a->verify();
        $parties = Party::paginate(10);
        return view('admin.forms.party.index',compact('parties'));
    }
    protected function party_delete(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        $party=Party::findOrFail($request->id);
        $party->delete();
        return back();
    }

    function party_create(){
        $a= new AdminController;
        $a->verify();
        return view('admin.forms.party.create');
    }

    protected function party_store(Request $request)
    {
        $a= new AdminController;
        $a->verify();
        $user= new User();
        $validatedData = $request->validate([
            'email' => 'required|string',
            'name' => 'required|string',
            'mood' => 'required|string',
            'type' => 'required|in:Battle,Democracy',
            'desc' => 'required|string',
            'genre' =>'required|array|max:5'
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

        $email=$request->email;
        $user = User::where('email', $request->email)->first();

        if($user == null ) return \Redirect::back()->withErrors(['user '.$email.' not found']);

        $code = Str::random(16);
        $user_id=$user->id;
        $party = Party::create([
            'user_id' => $user_id,
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
        return view('admin.index');
    }

    protected function party_edit($id)
    {
        $a= new AdminController;
        $a->verify();
        $party = Party::where('id','=',$id)->first();
        $party_genres = $party->genre;
        $genre_list = Genre::orderBy('genre', 'ASC')->get();
        return view('admin.forms.party.edit', ['party'=> $party,  'party_genres'=>$party_genres, 'genre_list'=>$genre_list]);

    }

    protected function party_update(Request $request)
    {
        $a= new AdminController;
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
        /**$party->name = $request->name;                Da decidere*/


        $party->genre()->detach();

        foreach($genre_ids as $id) {
            $party->genre()->attach($id);
        }
        if($party->isDirty()) $party->save();
        return redirect()->route('admin.party.index');

    }


    /**
     * verify if is a admin
     */
    function verify(){
        $me = Auth::user();
        if ($me==null){
            abort('401', 'Unauthorized');
        }
        if ($me->id != 1){
            abort('401', 'Unauthorized');
        }
    }
}

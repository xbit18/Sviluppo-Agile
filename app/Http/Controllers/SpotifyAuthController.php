<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SpotifyWebAPI\Session;

use App\User;

class SpotifyAuthController extends Controller
{


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
                'streaming',
                'playlist-modify-private'
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
        /*session([
            'spotifySession' => $session
        ]);*/
        // Store the access token somewhere. In a database for example.
        return redirect(url()->previous())->with('spotifyLogIn', true);
    }

    public function logout(){

        $me = Auth::user();
        $me->access_token = NULL;
        $me->save();

        return redirect()->back()->with('spotifyLogOut', true);
        /*$user = User::all()->first();
        if(!$user) return redirect('/');

        $user->delete();
        return redirect('/');
        */
    }



}

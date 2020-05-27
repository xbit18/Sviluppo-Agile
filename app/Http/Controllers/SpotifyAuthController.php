<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SpotifyWebAPI\Session;

use App\User;

class SpotifyAuthController extends Controller
{
    public function getAuthCode(Request $request){
        
        /**
         * Spotify Session Parameters
         */
        $session = new Session(
            '03cf764ae57d4984834da3db10d241d2',
            'bc1b66fe0e0f40c7bc5cc62af87a5a50',
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
}

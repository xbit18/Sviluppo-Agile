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
use SpotifyWebAPI\SpotifyWebAPI as SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIException;

class AdminController extends Controller
{
    function index(){
        $a= new AdminController;
        $a->verify();
        return view('admin.index');
    }

    /**
     * party controllers
     */


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

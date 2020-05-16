<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
=======

use Illuminate\Support\Facades\Auth;
>>>>>>> c7183762243e71ebc5194eb3d951a0c897beec83
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
<<<<<<< HEAD
        return view('home');
=======
        $user = Auth::user();
        return view('home', ['user'=>$user]);
>>>>>>> c7183762243e71ebc5194eb3d951a0c897beec83
    }
}

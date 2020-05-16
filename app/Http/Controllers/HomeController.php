<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
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
        /**
         * Se l'utente è loggato restituisce la schermata di home altrimenti quella di login
         */
        if(Auth::check()) 
            return view('user.pages.home', ['user' => Auth::user() ]); 
        else
            return view('auth.login');
    }
}

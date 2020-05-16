<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

<<<<<<< HEAD
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
=======
Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');
Route::post('/party', 'PartyController@store')->name('createparty')->middleware('verified');


>>>>>>> c7183762243e71ebc5194eb3d951a0c897beec83

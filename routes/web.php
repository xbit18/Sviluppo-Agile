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

Route::get('/index', function() {
    return view('user/pages/index');
});

Auth::routes(['verify' => true]);

/**
 * UNAUTHENTICATED ROUTES
 */
Route::get('/', 'HomeController@index')->name('home')->middleware('verified');
Route::get('home', function () {
    return redirect('/');
});

/** 
 * PARTY MANAGEMENT
 */
Route::get('/party/create', 'PartyController@create')->name('party.create')->middleware('verified');
Route::post('/party', 'PartyController@store')->name('party.store')->middleware('verified');

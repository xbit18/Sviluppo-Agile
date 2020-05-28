<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Events\MusicPaused;


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
Route::get('/', 'HomeController@index')->name('home');


/**
 * PARTY MANAGEMENT
 */
Route::get('/party/create', 'PartyController@create')->name('party.create');
Route::post('/party', 'PartyController@store')->name('party.store');
Route::get('/me/party/show', 'PartyController@get_parties_by_user')->name('me.parties.show');
Route::get('/party/show/{code}', 'PartyController@show')->name('party.show');

Route::get('/party/{code}/pause', 'PartyController@pause')->name('party.pause');
Route::post('/party/{code}/play', 'PartyController@play')->name('party.play');
Route::get('/parties/show', 'PartyController@index')->name('parties.index');


/**
 * Invite routes
 */
Route::get('/users/{email}/nome', 'UserController@get_name_by_email')->name('user.namebyemail');
Route::post('/party/{code}/invite/', 'PartyController@invite')->name('party.invite');
Route::get('/song', 'PartyController@getSong');

Route::get('/loginspotify', 'PartyController@load');
Route::get('/logoutspotify', 'PartyController@logout');
Route::get('/callback', 'PartyController@getAuthCode');
Route::get('/callback/auth', 'PartyController@storeCode');
Route::get('/playback/{state}', 'PartyController@playpause');
Route::get('/playback', 'PartyController@page');

Route::get('/seemail', function(){
    return view('invite');
});

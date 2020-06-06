<?php

use Illuminate\Support\Facades\Auth;
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
Route::group(['middleware' => ['auth']], function () {

    Route::get('/', 'HomeController@index')->name('home');


    /**
     * PARTY MANAGEMENT
     */

    Route::get('/party/create', 'PartyController@create')->name('party.create');
    Route::post('/party', 'PartyController@store')->name('party.store');
    Route::get('/me/party/show', 'PartyController@get_parties_by_user')->name('me.parties.show');
    Route::get('/party/show/{code}', 'PartyController@show')->name('party.show');
    Route::get('party/edit/{code}', 'PartyController@edit')->name('party.edit');
    Route::post('/party/update/{code}','PartyController@update')->name('party.update');

    Route::get('/party/show', 'PartyController@index')->name('parties.index');
    Route::get('/party/{code}/getNextTrack', 'TrackController@getMostVotedSong')->name('party.nextTrack');
    Route::post('/party/{code}/tracks/', 'TrackController@addTrackToPlaylist')->name('party.addTrack');
    Route::delete('/party/{code}/tracks/{track_uri}', 'TrackController@deleteTrackFromPlaylist')->name('party.deleteTrack');
    Route::post('/party/active_track', 'TrackController@setTrackActive')->name('party.activeTrack');
    //Route::post('/party/release_track', 'TrackController@setTrackNotActive')->name('party.releaseTrack');



    /* PLAYER MANAGEMENT */
    Route::get('/party/{code}/pause', 'PlayerController@pause')->name('party.pause');
    Route::post('/party/{code}/play', 'PlayerController@play')->name('party.play');
    Route::post('/party/{code}/syncronize', 'PlayerController@syncronize')->name('player.syncronize');


    /** Party Presences **/
    Route::get('party/{code}/join/{user_id}', 'PresenceController@join_party')->name('party.join');
    Route::get('/party/{code}/leave/{user_id}', 'PresenceController@leave_party')->name('party.leave');

    /**Get songs by Genre**/
    Route::post('/party/playlist/populate','PartyController@populateParty')->name('playlist.populate');

    /**
     * Invite routes
     */
    Route::get('/users/{email}/nome', 'UserController@get_name_by_email')->name('user.namebyemail');
    Route::post('/party/{code}/invite/', 'PartyController@invite')->name('party.invite');
    Route::get('/song', 'PartyController@getSong');

    Route::get('/loginspotify', 'SpotifyAuthController@load')->name('spotify.login');
    Route::get('/logoutspotify', 'SpotifyAuthController@logout')->name('spotify.logout');
    Route::get('/callback', 'SpotifyAuthController@getAuthCode');
    Route::get('/callback/auth', 'PartyController@storeCode');


    Route::get('/seemail', function(){
        return view('invite');
    });

    /**
     * Admin routes
     */
    Route::get('/admin','AdminController@index');
    /**
     * Admin users
     */
    Route::get('/admin/users','AdminUsersController@index')->name('users.index');
    Route::post('/admin/user/delete','AdminUsersController@delete');
    Route::get('/admin/user/new','AdminUsersController@create');
    Route::post('/admin/user/store','AdminUsersController@store');
    Route::post('/admin/user/update','AdminUsersController@update');
    Route::get('/admin/user/{id}/edit','AdminUsersController@edit');
    /**
     * Admin party
     */
    Route::get('/admin/parties','AdminPartiesController@index')->name('admin.party.index');
    Route::post('/admin/party/delete','AdminPartiesController@delete');
    Route::get('/admin/party/new','AdminPartiesController@create');
    Route::post('/admin/party/store','AdminPartiesController@store')->name('admin.party.store');;
    Route::post('/admin/party/update','AdminPartiesController@update')->name('admin.party.update');
    Route::get('/admin/party/{id}/edit','AdminPartiesController@edit');
    /**
     * Admin bans
     */
    Route::get('/admin/bans','AdminBansController@index')->name('admin.ban.index');
    Route::post('/admin/ban/delete','AdminBansController@delete');
    Route::get('/admin/ban/new','AdminBansController@create');
    Route::post('/admin/ban/store','AdminBansController@store')->name('admin.ban.store');;
    Route::post('/admin/ban/update','AdminBansController@update')->name('admin.ban.update');
    Route::get('/admin/ban/{id}/edit','AdminBansController@edit');
    /**
     * Admin votes
     */
    Route::get('/admin/votes','AdminVotesController@index')->name('admin.vote.index');
    Route::post('/admin/vote/delete','AdminVotesController@delete');
    Route::get('/admin/vote/new','AdminVotesController@create');
    Route::post('/admin/vote/store','AdminVotesController@store')->name('admin.vote.store');;
    Route::post('/admin/vote/update','AdminVotesController@update')->name('admin.vote.update');
    Route::get('/admin/vote/{id}/edit','AdminVotesController@edit');
    /**
     * Admin kicks
     */
    Route::get('/admin/kicks','AdminKicksController@index')->name('admin.kick.index');
    Route::post('/admin/kick/delete','AdminKicksController@delete');
    Route::get('/admin/kick/new','AdminKicksController@create');
    Route::post('/admin/kick/store','AdminKicksController@store')->name('admin.kick.store');;
    Route::post('/admin/kick/update','AdminKicksController@update')->name('admin.kick.update');
    Route::get('/admin/kick/{id}/edit','AdminKicksController@edit');
    /**
     * Other admin routs
     */
    Route::get('/admin/elements', function(){
        return view('admin.elements');
    });
    Route::get('/admin/panels', function(){
        return view('admin.panels');
    });
    Route::get('/admin/widgets', function(){
        return view('admin.widgets');
    });
    Route::get('/admin/charts', function(){
        return view('admin.charts');
    });
    Route::get('/admin/login', function(){
        return view('admin.login');
    });
    Route::get('/admin/new_user', function(){
        return view('admin.forms.user.new_user');
    });

});

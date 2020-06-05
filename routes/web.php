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
    Route::delete('/party/{code}/tracks/{track_uri}', 'TrackController@deleteTrackFromPlaylist')->name('party.deleteTrack');



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
    Route::get('/admin/users','AdminController@users')->name('users.index');
    Route::post('/admin/user/delete','AdminController@user_delete');
    Route::get('/admin/user/{id}/edit','AdminController@user_edit');
    Route::get('/admin/user/new','AdminController@user_create');
    Route::post('/admin/user/store','AdminController@user_store');
    Route::post('/admin/user/update','AdminController@user_update');
    /**
     * Admin party
     */
    Route::get('/admin/parties','AdminController@parties')->name('admin.party.index');
    Route::post('/admin/party/delete','AdminController@party_delete');
    Route::get('/admin/party/{id}/edit','AdminController@party_edit');
    Route::get('/admin/party/new','AdminController@party_create');
    Route::post('/admin/party/store','AdminController@party_store')->name('admin.party.store');;
    Route::post('/admin/party/update','AdminController@party_update')->name('admin.party.update');

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

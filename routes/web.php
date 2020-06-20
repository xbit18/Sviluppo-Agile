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
Route::get('/ban','Admin\BansController@showban');
/**
 * UNAUTHENTICATED ROUTES
 */
Route::group(['middleware' => ['auth','totalban']], function () {

    Route::get('/', 'HomeController@index')->name('home');


    /**
     * PARTY MANAGEMENT
     */

    Route::get('/party/create', 'PartyController@create')->name('party.create');
    Route::post('/party', 'PartyController@store')->name('party.store');
    Route::get('/me/party/show', 'PartyController@get_parties_by_user')->name('me.parties.show');
    Route::get('/party/show/{code}', 'PartyController@show')->name('party.show')
                ->middleware('access');
    Route::get('party/edit/{code}', 'PartyController@edit')->name('party.edit');
    Route::post('/party/update/{code}','PartyController@update')->name('party.update');
    Route::delete('/party/{code}/delete','PartyController@delete')->name('party.delete');

    Route::get('/party/show', 'PartyController@index')->name('parties.index');

    Route::get('/party/{code}/tracks/{id}/vote', 'TrackController@vote')->name('party.voteTrack');
    Route::get('/party/{code}/tracks/{id}/unvote', 'TrackController@unvote')->name('party.unvoteTrack');
    Route::get('/party/{code}/tracks/{id}/skip', 'TrackController@vote_to_skip')->name('party.skipSong');

    Route::get('/party/{code}/getNextTrack', 'TrackController@getMostVotedSong')->name('party.nextTrack');
    Route::post('/party/{code}/tracks/', 'TrackController@addTrackToPlaylist')->name('party.addTrack');
    Route::delete('/party/{code}/tracks/{track_uri}', 'TrackController@deleteTrackFromPlaylist')->name('party.deleteTrack');
    Route::post('/party/active_track', 'TrackController@setTrackActive')->name('party.activeTrack');
    Route::get('/party/{code}/resetbattle', 'TrackController@resetBattle')->name('party.resetBattle');
    //Route::post('/party/release_track', 'TrackController@setTrackNotActive')->name('party.releaseTrack');
    Route::delete('/party/{code}/tracks/{id}', 'TrackController@deleteTrackFromPlaylist')->name('party.deleteTrack');

    Route::post('/party/{code}/tracks/suggest/add', 'TrackController@suggestSong')->name('party.suggestSong');
    Route::post('/party/{code}/tracks/suggest/remove', 'TrackController@removeSuggestedSong')->name('party.removeSuggestedSong');

    // Route::get('/party/{code}/suggestedTracks/{id}/vote', 'TrackController@voteSuggestedSong')->name('party.voteSuggestedTrack');
    // Route::get('/party/{code}/suggestedTracks/{id}/unvote', 'TrackController@unvoteSuggestedSong')->name('party.unvoteSuggestedTrack');

    /* PARTY KICK & BAN */

    Route::post('/party/{code}/user/{user_id}/kick/', 'PartyManagerController@kick')->name('kick.user');
    Route::get('/party/{code}/user/{user_id}/ban', 'PartyManagerController@ban')->name('ban.user');
    Route::get('/party/{code}/user/{user_id}/unban', 'PartyManagerController@unban')->name('unban.user');


    /* PLAYER MANAGEMENT */
    Route::get('/party/{code}/pause', 'PlayerController@pause')->name('party.pause');
    Route::post('/party/{code}/play', 'PlayerController@play')->name('party.play');
    Route::post('/party/{code}/syncronize', 'PlayerController@syncronize')->name('player.syncronize');


    /** Party Presences **/
    Route::get('party/{code}/join/{user_id}', 'PresenceController@join_party')->name('party.join');
    Route::get('/party/{code}/leave/{user_id}', 'PresenceController@leave_party')->name('party.leave');

    /**Get songs by Genre**/
    Route::post('/party/playlist/populate','PartyController@populateParty')->name('playlist.populate');
    Route::get('/party/{code}/playlist/populate/me','PartyController@populateByPreferences')->name('playlist.populate.me');
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
    Route::get('/admin','Admin\MainController@index');
    /**
     * Admin users
     */
    Route::get('/admin/users','Admin\UsersController@index')->name('users.index');
    Route::post('/admin/user/delete','Admin\UsersController@delete');
    Route::get('/admin/user/new','Admin\UsersController@create');
    Route::post('/admin/user/store','Admin\UsersController@store');
    Route::post('/admin/user/update','Admin\UsersController@update');
    Route::post('/admin/user/joinparty','Admin\UsersController@joinparty');
    Route::post('/admin/user/leaveparty','Admin\UsersController@leaveparty');
    Route::get('/admin/user/{id}/edit','Admin\UsersController@edit');
    /**
     * Admin party
     */
    Route::get('/admin/parties','Admin\PartiesController@index')->name('admin.party.index');
    Route::post('/admin/party/delete','Admin\PartiesController@delete');
    Route::get('/admin/party/new','Admin\PartiesController@create');
    Route::post('/admin/party/store','Admin\PartiesController@store')->name('admin.party.store');;
    Route::post('/admin/party/update','Admin\PartiesController@update')->name('admin.party.update');
    Route::get('/admin/party/{id}/edit','Admin\PartiesController@edit');
    /**
     * Admin bans
     */
    Route::get('/admin/bans','Admin\BansController@index')->name('admin.ban.index');
    Route::post('/admin/ban/delete','Admin\BansController@delete');
    Route::get('/admin/ban/new','Admin\BansController@create');
    Route::post('/admin/ban/store','Admin\BansController@store')->name('admin.ban.store');;
    Route::post('/admin/ban/update','Admin\BansController@update')->name('admin.ban.update');
    Route::get('/admin/ban/{id}/edit','Admin\BansController@edit');

    Route::get('/admin/totalban','Admin\BansController@indextotalban');
    Route::post('/admin/totalban/store','Admin\BansController@totalban');
    Route::post('/admin/totalban/delete','Admin\BansController@totalunban');


    /**
     * Admin votes
     */
    Route::get('/admin/votes','Admin\VotesController@index')->name('admin.vote.index');
    Route::post('/admin/vote/delete','Admin\VotesController@delete');
    Route::get('/admin/vote/new','Admin\VotesController@create');
    Route::post('/admin/vote/store','Admin\VotesController@store')->name('admin.vote.store');;
    Route::post('/admin/vote/update','Admin\VotesController@update')->name('admin.vote.update');
    Route::get('/admin/vote/{id}/edit','Admin\VotesController@edit');
    /**
     * Admin kicks
     */
    Route::get('/admin/kicks','Admin\KicksController@index')->name('admin.kick.index');
    Route::post('/admin/kick/delete','Admin\KicksController@delete');
    Route::get('/admin/kick/new','Admin\KicksController@create');
    Route::post('/admin/kick/store','Admin\KicksController@store')->name('admin.kick.store');;
    Route::post('/admin/kick/update','Admin\KicksController@update')->name('admin.kick.update');
    Route::get('/admin/kick/{id}/edit','Admin\KicksController@edit');

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

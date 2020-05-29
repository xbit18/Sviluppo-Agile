
/* --------------------------- CHANNEL ------------------------ */

/** -------Prendo il codice del party dall'URI ------**/

var party_code = $('#party_code').attr('data-code');
var user_code = $('#user_code').attr('data-code');
var channel = Echo.join(`party.${party_code}`);

/* Music Pause */
/**
 * Comunica a tutti i partecipanti del canale quando un utente si unisce
 */
channel.here((users) => {
    console.log(users);
    $('#joining-list').empty();
    $.each(users, function (index, user) {
        console.log(user);
        var new_partecipant = $('#partecipant-prototype').clone();
        new_partecipant_link = new_partecipant.find('a');
        new_partecipant_link.text(user.name);
        new_partecipant.removeAttr('id');
        new_partecipant_link.attr('data-id', user.id);
        $('#joining-list').append(new_partecipant);
    });

});

/**
 *  Action a utente entrante
 */
channel.joining((user) => {
    //console.log('joining')
    //console.log(user)
    var new_partecipant = $('#partecipant-prototype').clone();
    new_partecipant_link = new_partecipant.find('a');
    new_partecipant.removeAttr('id');
    new_partecipant_link.text(user.name);
    new_partecipant_link.attr('data-id', user.id);
    $('#joining-list').append(new_partecipant);
})

/**
 *  Comunica a tutti che un utente lascia il canale
 */
channel.leaving((leaving_user) => {
    //console.log('leaving')
console.log(leaving_user);
    

    $('#joining-list li').each(function (index, user) {
        console.log(user);
        var partecipant_link = $(this).find('a');
        console.log(partecipant_link);
        if (partecipant_link.attr('data-id') == leaving_user.id) {
            partecipant_link.text(partecipant_link.text() + " (leaving party...)");

            $.ajax({
                url: "/party/" + party_code + "/leave/" + leaving_user.id,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (data) {
                    console.log(leaving_user)

                    // console.log(data);
                    // DEBUGGING
                    // console.log(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    /**
                     * Error Handling
                     */
                    if (xhr.status == 404) {
                        console.log("404 NOT FOUND");
                    } else if (xhr.status == 500) {
                        console.log("500 INTERNAL SERVER ERROR");
                    } else {
                        console.log("errore " + xhr.status);
                    }
                }
            });

            setTimeout(function () {
                user.remove();
            }, 1000);
        }
    });
})




/* --------------------------- WEB PLAYER ------------------------ */


window.onSpotifyWebPlaybackSDKReady = () => {
    //const token = 'BQCuguaURpWrApdQ0lkd0xLCl_W8TEVTE0p7LcnHgj1Bn0Dm9AqbhnogAMRx2oOwL7GemNvloRy73NprTPRCqeQX_ifEOY3fzgmGyH9YW9TP5uZSkOB2Z4rAVVUEHB1BxodMvunn5EfRjmFSLLFhgQBuQ9YJ2t_aaKr6uYVPjplCA5AqBr4KxmXDcHxqiANOOrClo9zb';
    const token = $('#mytoken').text();
    const player = new Spotify.Player({
        name: 'Web Player Party App',
        getOAuthToken: cb => { cb(token); }
    });

    var devId;

    // Error handling
    player.addListener('initialization_error', ({ message }) => { console.log(message) });
    player.addListener('authentication_error', ({ message }) => { window.location.replace('/loginspotify') });
    player.addListener('account_error', ({ message }) => { window.location.replace('/loginspotify') });
    player.addListener('playback_error', ({ message }) => { console.error(message); });

    // Playback status updates
    player.addListener('player_state_changed', state => {
        console.log(state);
        if (!($('#title-player').text() === state.track_window.current_track.name))
            $('#title-player').text(state.track_window.current_track.name);

        var artists = "";
        $.each(state.track_window.current_track.artists, function (index, artist) {
            artists += artist.name;
        });

        if (!($('#artist-player').text() === artists))
            $('#artist-player').text(artists);
    });

    // Ready
    player.addListener('ready', ({ device_id }) => {
        console.log('Ready with Device ID', device_id);
        devId = device_id;
    });

    // Not Ready
    player.addListener('not_ready', ({ device_id }) => {
        console.log('Device ID has gone offline', device_id);
    });

    // Connect to the player!
    player.connect();


/* --------------------------- INIZIALIZZAZIONE PLAYLIST ------------------------ */


    /**
     * GET ME -> I need playlists
     */

    var party_name = $('#party_name').text();

    var my_id;
    var my_party_playlist;


    var instance = axios.create();
    delete instance.defaults.headers.common['X-CSRF-TOKEN'];

    instance({
        url: "https://api.spotify.com/v1/me",
        method: 'GET',
        dataType: "json",
        headers: {
            'Authorization': 'Bearer ' + token,
        },
    }).then(function (data) {
        // DEBUGGING
        console.log(data);
        my_id = data.data.id;
        //actual_context_uri = data.item.uri;
        //$('#title-player').val(data.item.name);

        instance({
            url: "https://api.spotify.com/v1/users/" + my_id + "/playlists",
            method: 'GET',
            dataType: "json",
            headers: {
                'Authorization': 'Bearer ' + token,
            },
        })
            .then(function (data) {

                // DEBUGGING
                console.log('data playlists');
                console.log(data);
                $.each(data.data.items, function (index, playlist) {
                    if (playlist.name.toLowerCase() === party_name.toLowerCase()) my_party_playlist = playlist;
                });
                console.log(my_party_playlist);
                /**
                 * Ho preso la playlist
                 */

                instance({
                    url: "https://api.spotify.com/v1/playlists/" + my_party_playlist.id + "/tracks",
                    method: 'GET',
                    dataType: "json",
                    headers: {
                        'Authorization': 'Bearer ' + token,
                    }
                })
                    .then(function (data) {
                        // DEBUGGING
                        console.log('play tracks');
                        console.log(data);

                        $.each(data.data.items, function (index, item) {
                            var song_item = $('#song-prototype').clone();
                            song_item.removeClass('d-none');
                            song_item_link = song_item.find('a');
                            song_item_link.text(' - ' + item.track.name);
                            song_item_link.attr('data-id', item.track.id);
                            song_item_link.attr('data-uri', item.track.uri);
                            song_item_link.attr('data-number', index);
                            song_item_link.attr('data-playlist-uri', my_party_playlist.uri);
                            song_item_link.addClass('song_link');
                            $('#party-song-list').append(song_item);
                        });

                    })

            });
    });



/* --------------------------- COMANDI ------------------------ */



    /**
     * Listener alle canzoni
     */
    $(document).on('click', '.song_link', function (event) {
        event.preventDefault();

        console.log('clicked');

        /**
         * AJAX CALL FOR PLAY THAT SONG
         */


        var p_uri = $(this).attr('data-playlist-uri');
        var p_numb = $(this).attr('data-number');

        var track_uri = $(this).attr('data-uri');

        instance({
            url: "https://api.spotify.com/v1/me/player/play?device_id=" + devId,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            data: {
                "context_uri": p_uri,
                "offset": {
                    "position": p_numb
                },
                "position_ms": 0
            },
            dataType: 'json',
            success: function (data) {
                // DEBUGGING
                //console.log(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                /**
                 * Error Handling
                 */
                if (xhr.status == 404) {
                    console.log("404 NOT FOUND");
                } else if (xhr.status == 500) {
                    console.log("500 INTERNAL SERVER ERROR");
                } else {
                    console.log("errore");
                }
            }
        }).then(function (data) {

            console.log("uri " + track_uri);

            $.ajax({
                url: "/party/" + party_code + "/play",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "track_uri": track_uri,
                    "position_ms": 0
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    // DEBUGGING
                    //console.log(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    /**
                     * Error Handling
                     */
                    if (xhr.status == 404) {
                        console.log("404 NOT FOUND");
                    } else if (xhr.status == 500) {
                        console.log("500 INTERNAL SERVER ERROR");
                    } else {
                        console.log("errore " + xhr.status);
                    }
                }
            });

        });

    });

    /**
     * BUTTONS CONTROLS
     */

    $('#spotify_play_form').on('submit', function (event) {
        event.preventDefault();

        /**
         * AJAX CALL FOR PLAY
         */

        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];

        console.log(devId);

        player.getCurrentState().then(state => {

            if(state){
                var position = state.position;
                var track_uri = state.track_window.current_track.uri
            } else {
                var position = 0;
            }

            if (!state) {
                console.error('User is not playing music through the Web Playback SDK');
                $('.song_link').first().click();

                return;
            }

            player.resume().then(function (data) {

                console.log("uri " + track_uri);
                console.log('position ' + position);
                $.ajax({
                    url: "/party/" + party_code + "/play",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "track_uri": track_uri,
                        "position_ms": position
                    },
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        // DEBUGGING
                        //console.log(data);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        /**
                         * Error Handling
                         */
                        if (xhr.status == 404) {
                            console.log("404 NOT FOUND");
                        } else if (xhr.status == 500) {
                            console.log("500 INTERNAL SERVER ERROR");
                        } else {
                            console.log("errore " + xhr.status);
                        }
                    }
                });
    
            });;

        });

        /*
        instance({
            url: "https://api.spotify.com/v1/me/player/play?device_id=" + devId,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            data: {},
            dataType: 'json',
            success: function(data){
            },
            error:function (xhr, ajaxOptions, thrownError){ 
                if(xhr.status == 404) {
                    console.log("404 NOT FOUND");
                }else if(xhr.status == 500) {
                    console.log("500 INTERNAL SERVER ERROR");
                }else{
                    console.log("errore");
                }
            }
        });    
*/
    });


    $('#spotify_prev_form').on('submit', function (event) {
        event.preventDefault();
        player.previousTrack()
        .then(player.getCurrentState()
        .then(state => {
            var previous_tracks_length = state.track_window.previous_tracks.length;
            if(previous_tracks_length > 0){
                 var track_uri = state.track_window.previous_tracks[tracks_length-1].uri;
            // console.log(track_uri, 'canzone da riprodurre');
            // console.log(state);
            $.ajax({
                url: "/party/" + party_code + "/play",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "track_uri": track_uri,
                    "position_ms": 0
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    // DEBUGGING
                    //console.log(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    /**
                     * Error Handling
                     */
                    if (xhr.status == 404) {
                        console.log("404 NOT FOUND");
                    } else if (xhr.status == 500) {
                        console.log("500 INTERNAL SERVER ERROR");
                    } else {
                        console.log("errore " + xhr.status);
                    }
                }
            });
            }
           
        })
        )
    })


    $('#spotify_next_form').on('submit', function (event) {
        event.preventDefault();
        player.nextTrack()
        .then(player.getCurrentState()
        .then(state => {
            var next_tracks_length = state.track_window.next_tracks.length;
            if(next_tracks_length == 0){
                $('.song_link').first().click();

                return;
            } else {
                var track_uri = state.track_window.next_tracks[0].uri;
            }
            
            $.ajax({
                url: "/party/" + party_code + "/play",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "track_uri": track_uri,
                    "position_ms": 0
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    // DEBUGGING
                    //console.log(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    /**
                     * Error Handling
                     */
                    if (xhr.status == 404) {
                        console.log("404 NOT FOUND");
                    } else if (xhr.status == 500) {
                        console.log("500 INTERNAL SERVER ERROR");
                    } else {
                        console.log("errore " + xhr.status);
                    }
                }
            });
        })
        )
    });



    $('#spotify_pause_form').on('submit', function (event) {
        event.preventDefault();

        /**
         * AJAX CALL FOR PAUSE
         */
        /*
        instance({
            url: "https://api.spotify.com/v1/me/player/pause?device_id=" + devId,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            dataType: 'json',       
        });*/
        
        player.pause().then(() => {
            $.ajax({
                url: "/party/" + party_code + "/pause",
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    // DEBUGGING
                    //console.log(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    /**
                     * Error Handling
                     */
                    if (xhr.status == 404) {
                        console.log("404 NOT FOUND");
                    } else if (xhr.status == 500) {
                        console.log("500 INTERNAL SERVER ERROR");
                    } else {
                        console.log("errore " + xhr.status);
                    }
                }
            });
        });
    });



    var slider = $("#volume_range");
    var isDragging = false;
    slider.mousedown(function () {
            isDragging = false;
        })
        .mousemove(function () {
            isDragging = true;
            player.setVolume(slider.val() / 100)
        })
        .mouseup(function () {
            isDragging = false;
        });


};


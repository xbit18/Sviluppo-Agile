$( document ).ready( function() {
    'use strict';
  
/* --------------------------- CHANNEL ------------------------ */

/** -------Prendo il codice del party dall'URI e il mio ID utente ------**/

var party_code = $('#party_code').attr('data-code');
var user_code = $('#user_code').attr('data-code');
var slider = $("#volume_range");
var timeline = $('#timeline');
var duration_text = $('.music-duration');
var timer, running = false;
var channel = Echo.join(`party.${party_code}`);
// var snapshot_id;
var playlist_dom = $('#party_playlist');
var actual_dur = 0;
var actual_track;
var playlist_uri;

var paused = true;
var act_pos;
var auto_changed = false;


function millisToMinutesAndSeconds(millis) {
    var minutes = Math.floor(millis / 60000);
    var seconds = ((millis % 60000) / 1000).toFixed(0);
    return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
  }


  function append_song(my_party_playlist, track_id, track_uri, track_name, track_artists, track_img_src, track_album) {
    var item_s = $('#playlist_song_prototype').clone();
    var index = -1;
    if( playlist_dom.children().last().data('number') >= 0) index = playlist_dom.children().last().data('number');
    //let index = $('#party_playlist').children().last().data('number');
    item_s.find('h5').text(track_name);
    
    item_s.find('p').text(track_artists);

    var thumb = item_s.find('img');
    thumb.attr('src', track_img_src);
    
    item_s.children('div').children('div').children('small').text(track_album);
    //item_s.children('div').children('div').children('div').children('small').text(millisToMinutesAndSeconds(track_duration));
    item_s.children('div').children('div').children('div').children('small').children('button').attr('data-uri', track_uri);
    item_s.attr('data-id', track_id);
    item_s.attr('data-uri', track_uri);
    item_s.attr('data-number', index + 1);
    item_s.attr('data-playlist-uri', my_party_playlist.uri);
    item_s.addClass('song_link');
    //console.log(item_s);
    playlist_dom.append(item_s);
  }



/**
 * EDITING DEL PARTY DINAMICO
 */

$('#editPartyForm').on('submit', function(event) {
    event.preventDefault();
    
    $.ajax({
        url: "/party/update/" + party_code,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: "POST",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        success: function(data){
            var html = '';
            if(data.errors)
            {
                html = '<div class="alert alert-danger">';
                for(var count = 0; count < data.errors.length; count++)
                {
                    html += '<p>' + data.errors[count] + '</p>';
                }
                html += '</div>';
                $('#forErrors').html(html); 
            }
            if(data.success)
            {
                html = '<div class="alert alert-success" role="alert"><h4 class="alert-heading">Done!</h4><p>';
                html += data.success;
                html += '</p></div>';
                $('#forErrors').html(html); 
                location.reload();
            }
        },
        error: function (xhr) {
            /**
             * Error Handling
             */
            console.log('party update error');
            console.log(xhr);
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



/* --------------------------- WEB PLAYER ------------------------ */


function increment_timeline(data) {
    //console.log('funzione increment timeline chiamata');
    if(data) {
        if(!running) {
            running = true;
            timer = setInterval( () => { 
                //$('.music-duration').text( millisToMinutesAndSeconds(timeline.val()) );
                timeline.val( parseInt(timeline.val()) + 1000 );
                duration_text.text( millisToMinutesAndSeconds( parseInt(timeline.val()) ) );
                //console.log('incrementing ' + timeline.val()); 
                var v = ( timeline.val() ) / actual_dur;

                timeline.css('background-image', [
                    '-webkit-gradient(',
                      'linear, ',
                      'left top, ',
                      'right top, ',
                      'color-stop(' + v + ', #1DB954), ',
                      'color-stop(' + v + ', #535353)',
                    ')'
                ].join(''));
                timeline.css('background-image', [
                    '-moz-linear-gradient(',
                      'linear, ',
                      'left top, ',
                      'right top, ',
                      'color-stop(' + v + ', #1DB954), ',
                      'color-stop(' + v + ', #535353)',
                    ')'
                ].join(''));
                
            },1000);
        }
    } else {
        //console.log('clearing');
        clearInterval(timer);
        running = false;
    }
}


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
        // console.log(state);


        if (state) {

            
            if(state.track_window.current_track.uri != actual_track) {
                /**
                 * La traccia è sicuramente cambiata
                 */
                
                console.error('actual_track ' + actual_track);
                console.error('state.track_window.current_track ' + state.track_window.current_track.uri);
                console.log('traccia cambiata');
                console.error(state);

                /*
                if(state.track_window.previous_tracks && state.track_window.previous_tracks.length && ( state.track_window.previous_tracks[0].uri == actual_track || state.track_window.previous_tracks[1].uri == actual_track ) ) {
                    act_pos++;
                } else if(state.track_window.next_tracks && state.track_window.next_tracks[0].uri == actual_track) {
                    act_pos--;
                }
                */
                /*
                actual_track = state.track_window.current_track.uri;

                if(!auto_changed)  {
                    instance({
                        url: "https://api.spotify.com/v1/me/player/play?device_id=" + devId,
                        method: 'PUT',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                        },
                        data: {
                                "context_uri": playlist_uri,
                                "offset": {
                                "position": act_pos
                                },
                                "position_ms": 0
                            },
                        dataType: 'json'
                        }).then(function (data) {
                            
                            auto_changed = true;
                            player.setVolume(slider.val() / 100);
                            console.log("uri " + track_uri + ", pos " + act_pos);
                            paused = false;
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
                    /*}
                    else {
                        auto_changed = false;
                    }*/
                
            }

            console.log(state);
            /**
             * Settaggio della timeline
             */
            var dur = state.track_window.current_track.duration_ms;
            $('.total-duration').text( millisToMinutesAndSeconds( dur ) );
            
            timeline.attr('max', dur);

            if (!($('#title-player').text() === state.track_window.current_track.name)) {
                $('#title-player').text(state.track_window.current_track.name);

                var artists = "";
                $.each(state.track_window.current_track.artists, function (index, artist) {
                    artists += " " + artist.name;
                });

            }

            if (!($('#artist-player').text() === artists)) {
                $('#artist-player').text(artists);
            }
            var position = state.position;
            var track_uri = state.track_window.current_track.uri;
            actual_dur = parseInt(state.track_window.current_track.duration_ms);
        } else {
            var position = 0;
        }



        

        






        //console.log(state.paused, 'paused boolean')
        //console.log('hola');
        if (!state.paused) {
            if(position == 0) timeline.val(0);
            increment_timeline(true);

            console.log('position ' + act_pos);
            
            // console.log("uri " + track_uri);
            // console.log('position ' + position);
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
                success: function () {
                    console.log('success play');
                    // console.log(data);
                    // DEBUGGING
                    //console.log(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    /**
                     * Error Handling
                     */
                    console.log('party/code/play error');
                    console.log(xhr);
                    if (xhr.status == 404) {
                        console.log("404 NOT FOUND");
                    } else if (xhr.status == 500) {
                        console.log("500 INTERNAL SERVER ERROR");
                    } else {
                        console.log("errore " + xhr.status);
                    }
                }
            });
        } else if (state.paused) {
            increment_timeline(false);
            $.ajax({
                url: "/party/" + party_code + "/pause",
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function () {
                    console.log('success pause');
                    // console.log(data);
                    // DEBUGGING
                    //console.log(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    /**
                     * Error Handling
                     */
                    console.log('party/code/pause error');
                    console.log(xhr);
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

    /* -------------------CHANNELS LISTENERS ----------------------------------*/
    /**
     * 
 * Comunica a tutti i partecipanti del canale quando un utente si unisce
 */
    channel.here((users) => {
        //console.log(users);
        $('#joining-list').empty();
        $.each(users, function (index, user) {
            //console.log(user);
            var new_partecipant = $('#partecipant-prototype').clone();
            let new_partecipant_link = new_partecipant.find('a');
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
        var new_partecipant_link = new_partecipant.find('a');
        new_partecipant.removeAttr('id');
        new_partecipant_link.text(user.name);
        new_partecipant_link.attr('data-id', user.id);
        $('#joining-list').append(new_partecipant);

        $.ajax({
            type: "GET",
            url: "/party/" + party_code + "/join/" + user.id,
            data: "data",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {
                console.log(user.name + 'presenza registrata')
            },
            error: function(e){
                console.log(e)
            }
        });
        
        setTimeout(function () {

            if (!paused) {
                console.log('syncronizing');

                player.getCurrentState().then(state => {
                    var position = state.position;
                    var track_uri = state.track_window.current_track.uri
                    $.ajax({
                        url: "/party/" + party_code + "/syncronize",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "user_id": user.id,
                            "track_uri": track_uri,
                            "position_ms": position
                        },
                        dataType: 'json',
                        success: function (data) {
                            console.log(data)

                            // console.log(data);
                            // DEBUGGING
                            // console.log(data);
                        },
                        error: function (e) {
                            console.log(e)
                        }
                        //function (xhr, ajaxOptions, thrownError) {
                        //     /**
                        //      * Error Handling
                        //      */
                        //     if (xhr.status == 404) {
                        //         console.log("404 NOT FOUND");
                        //     } else if (xhr.status == 500) {
                        //         console.log("500 INTERNAL SERVER ERROR");
                        //     } else {
                        //         console.log("errore " + xhr.status);
                        //     }
                        // }
                    });
                })
            }

        }, 4000)

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
                     error: function(e){
                         console.log(e)
                     }
                     
                    //function (xhr, ajaxOptions, thrownError) {
                    //     /**
                    //      * Error Handling
                    //      */
                    //     if (xhr.status == 404) {
                    //         console.log("404 NOT FOUND");
                    //     } else if (xhr.status == 500) {
                    //         console.log("500 INTERNAL SERVER ERROR");
                    //     } else {
                    //         console.log("errore " + xhr.status);
                    //     }
                    // }
                });

                setTimeout(function () {
                    user.remove();
                }, 1000);
            }
        });
    })




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

                if(my_party_playlist.images[0]) $('#party_img_genre').attr('src', my_party_playlist.images[0].url);
                
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
                            // OLD
                            /* var song_item = $('#song-prototype').clone();
                            song_item.removeClass('d-none');
                            song_item_link = song_item.find('a');
                            song_item_link.text(' - ' + item.track.name);
                            song_item_link.attr('data-id', item.track.id);
                            song_item_link.attr('data-uri', item.track.uri);
                            song_item_link.attr('data-number', index);
                            song_item_link.attr('data-playlist-uri', my_party_playlist.uri);
                            song_item_link.addClass('song_link');
                            $('#party-song-list').append(song_item); */

                            var artists = "";
                            $.each(item.track.artists, function (index, artist) {
                                artists += " " + artist.name;
                            });

                            append_song(my_party_playlist, item.track.id, item.track.uri, item.track.name, artists, item.track.album.images[0].url, item.track.album.name);

                            // NEW
                            //console.log(item);
                            /*var item_s = $('#playlist_song_prototype').clone();
                            
                            item_s.find('h5').text(item.track.name);
                            

                            var artists = "";
                            $.each(item.track.artists, function (index, artist) {
                                artists += " " + artist.name;
                            });

                            item_s.find('p').text(artists);

                            var thumb = item_s.find('img');
                            thumb.attr('src', item.track.album.images[0].url);
                            
                            item_s.children('div').children('div').children('small').text(item.track.album.name);
                            item_s.children('div').children('div').children('div').children('small').text(millisToMinutesAndSeconds(item.track.duration_ms));
                            item_s.attr('data-id', item.track.id);
                            item_s.attr('data-uri', item.track.uri);
                            item_s.attr('data-number', index);
                            item_s.attr('data-playlist-uri', my_party_playlist.uri);
                            item_s.addClass('song_link');
                            $('#party_playlist').append(item_s);*/
                        });

                    })

            });
    });


   
  


    /* --------------------------- COMANDI ------------------------ */



    /**
     * Delete songs controls
     */

    var song_uri;
    var song_position;
    var parent;

    $(document).on('click', '._delete', function(){
        song_uri = $(this).attr('data-uri');
        parent = $(this).parents('a')
        song_position = parent.data('number');
        console.log(song_position);
        $('#deleteSongModal').modal('show');
        
    });

    $('#deleteSongModal').on('submit', function(event) {
        event.preventDefault();

         /**
         * Logica eliminazione canzone dalla playlist
         */

        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];

        instance({
        url: `https://api.spotify.com/v1/playlists/${my_party_playlist.id}/tracks`,
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer ' + token,
        },
        dataType: 'json',
        contentType: 'application/json',
        data: {
               "tracks": [
                   {
                       "uri": song_uri,
                       "positions": [song_position]
                   }
               ],
                // "snapshot_id": snapshot_id
            }
        })
        .then(function(){
            // snapshot_id = response.data.snapshot_id;

            $('#deleteSongModal').modal('hide');
            parent.fadeOut("slow",function(){
                parent.remove()
            })
            console.log(song_position,' posizione control');
            $.each(playlist_dom.children(), function (index, element) {
                    if( $(element).data('number') > song_position){
                        let current_index = $(element).data('number');
                        console.log(current_index, 'current index');
                        $(element).data('number',parseInt(current_index)-1);
                        $(element).attr('data-number',parseInt(current_index)-1);
                        console.log($(element).data('number'),'new index');
                    } 
            });

        })
        .catch(function(error){
            console.log(error);
        })
           
    })





    /**
     * Listener alle canzoni
     */
    $(document).on('click', '.song_link', function (event) {
        event.preventDefault();




        console.log(event.target);

        // Se ho cliccato su elimina non deve partire
        if( event.target.classList.contains('_delete') ||  event.target.classList.contains('fa-times')) return;

        // console.log('clicked');

        /**
         * AJAX CALL FOR PLAY THAT SONG
         */


        var p_uri = $(this).attr('data-playlist-uri');
        //console.log(p_uri);
        var p_numb = $(this).attr('data-number');

        

        var track_uri = $(this).attr('data-uri');

        act_pos = p_numb;
        actual_track = track_uri;
        playlist_uri = p_uri;

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
            dataType: 'json'
        }).then(function (data) {

            player.setVolume(slider.val() / 100);
            console.log("uri " + track_uri);
            paused = false;
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
        player.getCurrentState().then(state => {
            if (!state) {
                console.error('User is not playing music through the Web Playback SDK');
                $('.song_link').first().click();

                return;
            }
            paused = false;
            player.resume()
        })
    })

    //     /**
    //      * AJAX CALL FOR PLAY
    //      */

    //     var instance = axios.create();
    //     delete instance.defaults.headers.common['X-CSRF-TOKEN'];

    //     console.log(devId);

    //     player.getCurrentState().then(state => {

    //         if(state){
    //             var position = state.position;
    //             var track_uri = state.track_window.current_track.uri
    //         } else {
    //             var position = 0;
    //         }

    //         if (!state) {
    //             console.error('User is not playing music through the Web Playback SDK');
    //             $('.song_link').first().click();

    //             return;
    //         }

    //         player.resume().then(function (data) {

    //             console.log("uri " + track_uri);
    //             console.log('position ' + position);
    //             $.ajax({
    //                 url: "/party/" + party_code + "/play",
    //                 method: 'POST',
    //                 headers: {
    //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                 },
    //                 data: {
    //                     "track_uri": track_uri,
    //                     "position_ms": position
    //                 },
    //                 dataType: 'json',
    //                 success: function (data) {
    //                     console.log(data);
    //                     // DEBUGGING
    //                     //console.log(data);
    //                 },
    //                 error: function (xhr, ajaxOptions, thrownError) {
    //                     /**
    //                      * Error Handling
    //                      */
    //                     if (xhr.status == 404) {
    //                         console.log("404 NOT FOUND");
    //                     } else if (xhr.status == 500) {
    //                         console.log("500 INTERNAL SERVER ERROR");
    //                     } else {
    //                         console.log("errore " + xhr.status);
    //                     }
    //                 }
    //             });

    //         });;

    //     });

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
    // });


    $('#spotify_prev_form').on('submit', function (event) {
        event.preventDefault();

        player.previousTrack()
        // .then(player.getCurrentState()
        // .then(state => {
        //     var previous_tracks_length = state.track_window.previous_tracks.length;
        //     if(previous_tracks_length > 0){
        //          var track_uri = state.track_window.previous_tracks[tracks_length-1].uri;
        //     // console.log(track_uri, 'canzone da riprodurre');
        //     // console.log(state);
        //     $.ajax({
        //         url: "/party/" + party_code + "/play",
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         data: {
        //             "track_uri": track_uri,
        //             "position_ms": 0
        //         },
        //         dataType: 'json',
        //         success: function (data) {
        //             console.log(data);
        //             // DEBUGGING
        //             //console.log(data);
        //         },
        //         error: function (xhr, ajaxOptions, thrownError) {
        //             /**
        //              * Error Handling
        //              */
        //             if (xhr.status == 404) {
        //                 console.log("404 NOT FOUND");
        //             } else if (xhr.status == 500) {
        //                 console.log("500 INTERNAL SERVER ERROR");
        //             } else {
        //                 console.log("errore " + xhr.status);
        //             }
        //         }
        //     });
        //     }

        // })
        // )
    })


    $('#spotify_next_form').on('submit', function (event) {
        event.preventDefault();
        player.nextTrack()
        // .then(player.getCurrentState()
        // .then(state => {
        //     var next_tracks_length = state.track_window.next_tracks.length;
        //     if(next_tracks_length == 0){
        //         $('.song_link').first().click();

        //         return;
        //     } else {
        //         var track_uri = state.track_window.next_tracks[0].uri;
        //     }

        //     $.ajax({
        //         url: "/party/" + party_code + "/play",
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         data: {
        //             "track_uri": track_uri,
        //             "position_ms": 0
        //         },
        //         dataType: 'json',
        //         success: function (data) {
        //             console.log(data);
        //             // DEBUGGING
        //             //console.log(data);
        //         },
        //         error: function (xhr, ajaxOptions, thrownError) {
        //             /**
        //              * Error Handling
        //              */
        //             if (xhr.status == 404) {
        //                 console.log("404 NOT FOUND");
        //             } else if (xhr.status == 500) {
        //                 console.log("500 INTERNAL SERVER ERROR");
        //             } else {
        //                 console.log("errore " + xhr.status);
        //             }
        //         }
        //     });
        // })
        // )
    });



    $('#spotify_pause_form').on('submit', function (event) {
        event.preventDefault();
        paused = true;
        player.pause()
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

        // player.pause()

        // .then(() => {
        //     $.ajax({
        //         url: "/party/" + party_code + "/pause",
        //         method: 'GET',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         dataType: 'json',
        //         success: function (data) {
        //             console.log(data);
        //             // DEBUGGING
        //             //console.log(data);
        //         },
        //         error: function (xhr, ajaxOptions, thrownError) {
        //             /**
        //              * Error Handling
        //              */
        //             if (xhr.status == 404) {
        //                 console.log("404 NOT FOUND");
        //             } else if (xhr.status == 500) {
        //                 console.log("500 INTERNAL SERVER ERROR");
        //             } else {
        //                 console.log("errore " + xhr.status);
        //             }
        //         }
        //     });
        // });
    });


        /**
         * Devo usare la sintassi pure js : hammer js da problemi con selettore $
         */
        var slide2 = document.getElementById('volume_range');

        var mc = new Hammer.Manager(slide2);
        mc.add( new Hammer.Tap({ event: 'singletap' }) );
        mc.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL }));
        mc.add(new Hammer.Pan({ direction: Hammer.DIRECTION_HORIZONTAL }));

        mc.on("singletap pan swipe", function(ev) {
            player.setVolume(slider.val() / 100)
        });
        
        

        /**
         * Compatibility with Desktop Browsers
         */
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








        /**
         * COMPATIBILITà MOBILE
         * Devo usare la sintassi pure js : hammer js da problemi con selettore $
         */
        var timeline_mob = document.getElementById('timeline');

        var mc = new Hammer.Manager(timeline_mob);
        mc.add( new Hammer.Tap({ event: 'singletap' }) );
        mc.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL }));
        mc.add(new Hammer.Pan({ direction: Hammer.DIRECTION_HORIZONTAL }));

        mc.on("singletap pan swipe", function(ev) {
            // SKIP LOGIC
            player.seek(timeline.val()).then(() => {
                //console.log('Changed position mob!');
              });
        }); 
        /**
         * Compatibility with Desktop Browsers
         */

        
        var isDragTime = false;
        timeline.click(function () {

            player.seek(timeline.val()).then(() => {
                ///console.log('Changed position!');
              });
              
        });
          
    
       
        /* ADD SONGS DYNAMICALLY */
    $('#searchSong').on('keyup',function(e){

        var song_name = $('#searchSong').val();
        song_name = encodeURIComponent(song_name.trim());
        var result = $('#result');

        if(song_name.length == 0){
            result.fadeOut("normal",function(){
                result.empty();
            });
        }

        if(song_name.length > 0){

            var instance = axios.create();
            delete instance.defaults.headers.common['X-CSRF-TOKEN'];

            instance({
            url: `https://api.spotify.com/v1/search?q=${song_name}&type=track,artist&limit=5`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            dataType: 'json',

        })
        .then(function (data) {
            result.empty();   
            let tracks = data.data.tracks.items;

            $.each(tracks, function (index, element) { 

               let item = $('#song-prototype').clone();
               let img = item.children('div').children('div').first().find('img');
               img.attr('src',element.album.images[0].url);
                
               let content = item.children('div').children('div').last();
               content.children('div').first().find('h6').text(element.name);
               content.children('div').first().find('small').text(millisToMinutesAndSeconds(element.duration_ms));

               let artists = "";
                $.each(element.artists, function (index, artist) {
                    artists += artist.name +' ';
                });

                content.children('div').last().children().first().text(artists);
                content.children('div').last().children().last().text(element.album.name)

                item.attr('data-id', element.id);
                item.attr('data-uri', element.uri)
                item.attr('data-duration', element.duration_ms)
                item.attr('data-number', index)
                item.addClass('item');
                item.removeAttr('id');
                result.append(item).hide().fadeIn();            
               

            });
   
        })
        .catch(function(error){
            console.log('search error: ');
            console.log(error);
        })
        }
    })


    $(document).on('click','.item',function(event){
        event.preventDefault();
        let track_uri = $(this).data('uri');
        let track_id = $(this).data('id');
        let track_img_src = $(this).children('div').children('div').first().find('img').attr('src');
        let track_duration = $(this).data('duration');
        let track_artists = $(this).children('div').children('div').last().children('div').last().children().first().text();
        let track_album = $(this).children('div').children('div').last().children('div').last().children().last().text();
        let track_name = $(this).children('div').first().find('h6').text();



        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];
       
        instance({
        url: `https://api.spotify.com/v1/playlists/${my_party_playlist.id}/tracks`,
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
        },
        data: {
            'uris' : [track_uri]
        },
        dataType: 'json',
        })
        .then(function(response){
            // snapshot_id = response.data.snapshot_id;
            // console.log(snapshot_id);


            instance({
                url: `https://api.spotify.com/v1/me/player/queue?uri=` + track_uri,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                data: {
                    'uri' : track_uri,
                    'device_id' : devId,
                },
                dataType: 'json',
                })
            .then(function(response){
                console.log('canzone aggiunta alla coda')
            });

            append_song(my_party_playlist, track_id, track_uri, track_name, track_artists, track_img_src, track_album)

            /*
            var item_s = $('#playlist_song_prototype').clone();
            let index = $('#party_playlist').children().last().data('number');
            item_s.find('h5').text(track_name);
            
            item_s.find('p').text(track_artists);

            var thumb = item_s.find('img');
            thumb.attr('src', track_img_src);
            
            item_s.children('div').children('div').children('small').text(track_album);
            item_s.children('div').children('div').children('div').children('small').text(millisToMinutesAndSeconds(track_duration));
            item_s.children('div').children('div').children('div').children('small').children('a').attr('data-id', track_id);
            item_s.attr('data-id', track_id);
            item_s.attr('data-uri', track_uri);
            item_s.attr('data-number', index + 1);
            item_s.attr('data-playlist-uri', my_party_playlist.uri);
            item_s.addClass('song_link');
            $('#party_playlist').append(item_s);
            */
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 6000
            });

            Toast.fire({
                type: 'success',
                title: 'The song has been added to the Playlist!'
                });
        })
        .catch(function(error){
            console.log('adding song error');
            console.log(error);
        })
    })




    


    
/** END SDK PLAYER */
};

/** END DOCUMENT READY */
})

$( document ).ready( function() {
    'use strict';


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
    var act_pos = 0;
    var auto_changed = false;

    

    function millisToMinutesAndSeconds(millis) {
        var minutes = Math.floor(millis / 60000);
        var seconds = ((millis % 60000) / 1000).toFixed(0);
        return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
    }

    
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
                        act_pos = timeline.val();

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



    function populate_song_link(item, track) {
        //console.log('sono in populate')
        //console.log(item)
        //console.log(track)
        item.find('h5').text(track.name);
    
        var artists = "";
        $.each(track.artists, function (index, artist) {
            artists += " " + artist.name;
        });

        item.find('p').text(artists);

        var thumb = item.find('img');
        thumb.attr('src', track.album.images[0].url);
        
        item.children('div').children('div').children('small').text(track.album.name);
        //item.children('div').children('div').children('div').children('small').text(millisToMinutesAndSeconds(track_duration));
        item.children('div').children('div').children('div').children('small').children('button').attr('data-uri', track.uri);
        //item.attr('data-id', track.id);
        //item.attr('data-uri', track.uri);
        //item.attr('data-number', index + 1);
        //item.attr('data-playlist-uri', my_party_playlist.uri);
        item.addClass('song_link');
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

        player.addListener('player_state_changed', state => {

            var track_uri;
            console.log(state, 'state');

            /*
            console.log(actual_track + ' vs ' + state.track_window.current_track.uri && actual_track === state.track_window.current_track.uri);
            if(actual_dur != 0 && state.position == 0) {
                if(actual_track === state.track_window.current_track.uri) {
                    console.log('canzone finita');
                } else {
                    console.log('canzone cambiata');
                }
            }*/

            if (state) {

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
                track_uri = state.track_window.current_track.uri;
                actual_dur = parseInt(state.track_window.current_track.duration_ms);
            } else {
                var position = 0;
            }

            


            if (!state.paused) {
                paused = false;
                if(position == 0) timeline.val(0);
                increment_timeline(true);
    
                
                
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
                //console.log('position ' + act_pos);
                instance({
                    url: "https://api.spotify.com/v1/me/player",
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                    },
                    dataType: 'json',
                }).then(function (data) {
                    //console.log(data, 'track_info');

                    // LA CANZONE è FINITAAAAAAAAAAAAAAAAAAAAAAAAA
                    if(!paused) {
                        //console.error('canzone finita');
                        $.ajax({
                            url: "/party/" + party_code + "/getNextTrack",
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            success: function (data) {
                                // console.log(data);
                                // DEBUGGING
                                //console.log(data);
                                $('.song_link').each( function(index, item) {
                                    if( $(this).attr('data-track') == data.track_uri ) {
                                        $(this).click();
                                    }
                                });
                                data.track_uri
                                console.log(data, 'next_track');
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                /**
                                 * Error Handling
                                 */
                                if (xhr.status == 404) {
                                    console.log("Next Track 404 NOT FOUND");
                                } else if (xhr.status == 500) {
                                    console.log("Next Track 500 INTERNAL SERVER ERROR");
                                } else {
                                    console.log("Next Track errore " + xhr.status);
                                }
                            }
                        });
                    }
    
                });
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

            actual_track = state.track_window.current_track.uri;
    

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


        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];

        /**
         * Popolo i dati della playlist
         */

        $('.song_link').each(function(index, item) {
            var song_link = $(this);
            var track_uri = song_link.attr('data-track'); 
            var track_id = track_uri.replace('spotify:track:', '');
            console.log('track: ' + track_uri)       

            // Chiamo per ottenere i dati della traccia
            instance({
                url: "https://api.spotify.com/v1/tracks/" + track_id,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                dataType: 'json',
            }).then(function (data) {
                console.log(data, 'track_info');
                populate_song_link(song_link, data.data);

            });
        });


        
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
        var track_uri = $(this).attr('data-track');
        var link = $(this);

        instance({
            url: "https://api.spotify.com/v1/me/player/play?device_id=" + devId,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            data: {
                    "uris": [track_uri],
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

                link.fadeOut( 'normal', () => {
                    link.remove();

                    $.ajax({
                        url: "/party/" + party_code + "/tracks/" + track_uri,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            console.log('canzone eliminata');
                            // DEBUGGING
                            //console.log(data);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            /**
                             * Error Handling
                             */
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
                
            });

        });


        // Listener Play Button
        $('#spotify_play_form').on('submit', function (event) {
            event.preventDefault();
            player.getCurrentState().then(state => {
                if (!state) {
                    console.error('User is not playing music through the Web Playback SDK');
                    $('.song_link').first().click();
    
                    return;
                }
                paused = false;
                player.resume();
            })
        })

        // Linstener pause button
        $('#spotify_pause_form').on('submit', function (event) {
            event.preventDefault();
            paused = true;
            player.pause()
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
            if(timeline.val() != 0) {
                player.seek(timeline.val()).then(() => {
                    //console.log('Changed position mob!');
                  });
            }
            
        }); 
        /**
         * Compatibility with Desktop Browsers
         */

        
        var isDragTime = false;
        timeline.click(function () {
            if(timeline.val() != 0) {
                player.seek(timeline.val()).then(() => {
                    ///console.log('Changed position!');
                });
            }
              
        });



    };

    // FINE onSpotifyWebPlaybackSDKReady

    


});
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
    var selected_track;

    var party_type = $('#p_type').attr('data-type');

    var paused = true;
    var act_pos = 0;
    var prec_play = false;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 6000
    });


    
 
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

            if (state) {

                // Setting text duration and timeline max value
                var dur = state.track_window.current_track.duration_ms;
                $('.total-duration').text( millisToMinutesAndSeconds( dur ) );
                timeline.attr('max', dur);
    
                // Setting song details on player
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
                prec_play = true;
                paused = false;
                if(position == 0) timeline.val(0);
                increment_timeline(true);
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
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
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
                /*
                instance({
                    url: "https://api.spotify.com/v1/me/player",
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                    },
                    dataType: 'json',
                }).then(function () {
                */
                    //console.log(data, 'track_info');

                    // LA CANZONE è FINITAAAAAAAAAAAAAAAAAAAAAAAAA
                    if(!paused && prec_play) {
                        play_next_song_battle(devId, token, party_code);
                        prec_play = false;
                    }
                    
    
               // });
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



    function populate_song_link(item, track, bool) {
        item.find('h5').text(track.name);
    
        var artists = "";
        $.each(track.artists, function (index, artist) {
            artists += " " + artist.name;
        });

        item.find('p').text(artists);

        var thumb = item.find('img');
        thumb.attr('src', track.album.images[0].url);
        
        item.children('div').children('div').children('small').text(track.album.name);
        item.children('div').children('div').children('div').children('small').children('button').attr('data-uri', track.uri);
        item.addClass('song_link');

        if(bool){
            return item;
        }
    }


    function refresh_all(tracks, actual_playing_uri) {
        $('#party_playlist').empty();

        $.each(tracks, function(index, track) {
            if(actual_playing_uri != track.track_uri) {
                var elem = $('#playlist_song_prototype').clone();
                var track_id = track.track_uri.replace('spotify:track:', '');
                elem.attr('id', '');
                elem.attr('data-track', track.track_uri);
                elem.attr('data-song-id', track.id);
                elem.addClass('song_link');
                instance({
                    url: "https://api.spotify.com/v1/tracks/" + track_id,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                    },
                    dataType: 'json',
                }).then(function (data) {
                    //console.log(data, 'track_info');
                    populate_song_link(elem, data.data, false);
                });
                $('#party_playlist').append(elem);
            }
        });

    }

    function play_next_song_battle(deviceId, token, code)  {

            console.log(deviceId + " " + token)

            // CALL FOR GETTING NEXT SONG
            $.ajax({
                url: "/party/" + code + "/getNextTrack",
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (data) {
                    var track_real_id = data.id;
                    var track_uri = data.track_uri;
                    console.log(data, 'next_track');
                    var instance = axios.create();
                    delete instance.defaults.headers.common['X-CSRF-TOKEN'];
                    console.log(instance.defaults.headers)
                    

                    // Riproduco la canzone sul player
                    instance({
                        url: "https://api.spotify.com/v1/me/player/play?device_id=" + deviceId,
                        method: 'PUT',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                        },
                        data: {
                                "uris": [track_uri],
                                "offset": {
                                    "uri": track_uri,
                                },
                                "position_ms": 0
                            },
                        dataType: 'json'
                    }).then(function (data) {

                        console.log(data, 'spo_play');

                        player.setVolume(slider.val() / 100);
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
                                
                                $.ajax({
                                    url: "/party/" + party_code + "/resetbattle",
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    dataType: 'json',
                                    success: function (data) {
                                        console.log(data, 'party battle resetted');

                                        $('#left_side').children('img').attr('src', '/img/bg-img/no_song.png');
                                        $('#left_side').find('h5').text('Left Side');
                                        $('#left_side').find('p').text('No song selected');
                                        $('#left_side').find('button').attr('disabled', true);
                                        $('#left_side').find('button').removeClass('voted');
                                        $('#right_side').find('button').removeClass('unlike');
                                        $('#left_side').find('button').find('span').text('0');

                                        $('#right_side').children('img').attr('src', '/img/bg-img/no_song.png');
                                        $('#right_side').find('h5').text('Right Side');
                                        $('#right_side').find('p').text('No song selected');
                                        $('#right_side').find('button').attr('disabled', true);
                                        $('#right_side').find('button').removeClass('voted');
                                        $('#right_side').find('button').removeClass('unlike');
                                        $('#right_side').find('button').find('span').text('0');

                                        $.ajax({
                                            type: "DELETE",
                                            url: `/party/${party_code}/tracks/${track_real_id}`,
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            dataType: "json",
                                            success: function (response) {
                                                refresh_all(data, track_uri);
                                            },
                                            error: function(error){
                                                console.log(error);
                                            }
                                        });

                                        
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

                        
                    }).catch((error)=> {
                        if (error.response) {
                          // The request was made and the server responded with a status code
                          // that falls out of the range of 2xx
                          console.log(error.response.data);
                          console.log(error.response.status);
                          console.log(error.response.headers);
                        } else if (error.request) {
                          // The request was made but no response was received
                          // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                          // http.ClientRequest in node.js
                          console.log(error.request);
                        } else {
                          // Something happened in setting up the request that triggered an Error
                          console.log('Error', error.message);
                        }
                    });

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

    function delete_from_playlist(track_uri) {
        $('#party_playlist a').each( function(index, item) {
            if($(this).attr('data-track') == track_uri) {
                var elem = $(this);
                elem.fadeOut("slow",function(){ elem.remove() });
            }
        });
    }


        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];

        /**
         * Popolo i dati della playlist
         */

        $('.song_link').each(function(index, item) {
            var song_link = $(this);
            var track_uri = song_link.attr('data-track'); 
            var track_id = track_uri.replace('spotify:track:', '');
            //console.log('track: ' + track_uri)       

            // Chiamo per ottenere i dati della traccia
            instance({
                url: "https://api.spotify.com/v1/tracks/" + track_id,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                dataType: 'json',
            }).then(function (data) {
                //console.log(data, 'track_info');
                populate_song_link(song_link, data.data, false);
                

                if($('#track_uri_side_1').length) {
                    var uri = $('#track_uri_side_1').attr('data-track');
                    $('.song_link').each( function(index, item) {
                        
                        if($(item).attr('data-track') == uri) {
                            $('#left_side').children('img').attr('src', $(item).find('img').attr('src'));
                            $('#left_side').find('h5').text($(item).find('h5').text());
                            $('#left_side').find('p').text($(item).find('p').text());
                        }
                    });
                }

                if($('#track_uri_side_2').length) {
                    var uri = $('#track_uri_side_2').attr('data-track');
                    $('.song_link').each( function(index, item) {
                        if($(item).attr('data-track') == uri) {
                            $('#right_side').children('img').attr('src', $(item).find('img').attr('src'));
                            $('#right_side').find('h5').text($(item).find('h5').text());
                            $('#right_side').find('p').text($(item).find('p').text());
                        }
                    });
                }


            });
        });


        




        
    /**
     *  -------------------- Listener alle canzoni --> quando faccio click su un link della canzone ------------------
     */
    $(document).on('click', '.song_link', function (event) {
        event.preventDefault();

        var track_uri = $(this).attr('data-track');
        var link = $(this);
        

        // Se ho cliccato su elimina non deve partire
        if( event.target.classList.contains('_delete') ||  event.target.classList.contains('fa-times')) return;

        if(party_type == 1) {
            $('#battleModal').modal();
            selected_track = track_uri;
        } 

    });


        /** -------------- Play Button Listener ----------------------- */
        $('#spotify_play_form').on('submit', function (event) {
            event.preventDefault();
            player.getCurrentState().then(state => {
                if (!state) {
                    console.error('User is not playing music through the Web Playback SDK');
                    //$('.song_link').first().click();
                    play_next_song_battle(devId, token, party_code);
    
                    return;
                }
                paused = false;
                player.resume();
            })
        })

        /** -------------- Pause Button Listener ----------------------- */
        $('#spotify_pause_form').on('submit', function (event) {
            event.preventDefault();
            paused = true;
            player.pause()
        });


        /** -------------- Volume Listener ----------------------- */

        // HammerJs for mobile
        var slide2 = document.getElementById('volume_range');

        var mc_volume = new Hammer.Manager(slide2);
        mc_volume.add( new Hammer.Tap({ event: 'singletap' }) );
        mc_volume.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL }));
        mc_volume.add(new Hammer.Pan({ direction: Hammer.DIRECTION_HORIZONTAL }));

        mc_volume.on("singletap pan swipe", function(ev) {
            player.setVolume(slider.val() / 100)
        });

        // For Desktop Browsers
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


            
        /** ---------------- TIMELINE Listener ----------------- */

         //COMPATIBILITà MOBILE : Devo usare la sintassi pure js : hammer js da problemi con selettore $
        var timeline_mob = document.getElementById('timeline');

        var mc_timeline = new Hammer.Manager(timeline_mob);
        mc_timeline.add( new Hammer.Tap({ event: 'singletap' }) );
        mc_timeline.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL }));
        mc_timeline.add(new Hammer.Pan({ direction: Hammer.DIRECTION_HORIZONTAL }));

        mc_timeline.on("singletap pan swipe", function(ev) {
            // SKIP LOGIC
            if(timeline.val() != 0) {
                player.seek(timeline.val()).then(() => {
                    //console.log('Changed position mob!');
                  });
            }
            
        }); 

        // Compatibilità Desktop
        var isDragTime = false;
        timeline.click(function () {
            if(timeline.val() != 0) {
                player.seek(timeline.val()).then(() => {
                    ///console.log('Changed position!');
                });
            }
              
        });

    /*----------------------- CERCARE UNA CANZONE --------------------*/
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

    /* AGGIUNGERE LA CANZONE ALLE TRACKS DI UN PARTY */

    $(document).on('click','.item',function(event){
        event.preventDefault();
        let track_uri = $(this).data('uri');
        let track_id = $(this).data('id');
        // let track_img_src = $(this).children('div').children('div').first().find('img').attr('src');
        // let track_duration = $(this).data('duration');
        // let track_artists = $(this).children('div').children('div').last().children('div').last().children().first().text();
        // let track_album = $(this).children('div').children('div').last().children('div').last().children().last().text();
        // let track_name = $(this).children('div').first().find('h6').text();



        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];
       
        $.ajax({
        url: `/party/${party_code}/tracks`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            'track_uri' : track_uri
        },
        dataType: 'json',
        success: function(response){
            // snapshot_id = response.data.snapshot_id;
            // console.log(snapshot_id);
            console.log(response);
            instance({
                url: "https://api.spotify.com/v1/tracks/" + track_id,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                dataType: 'json',
            }).then(function (data) {
                // console.log(data, 'track_info');

                let song_link = $('#playlist_song_prototype').clone();

                let item = populate_song_link(song_link, data.data,true);
                playlist_dom.append(item).hide().fadeIn(1000);


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
                console.log(error);
            });

        }
        })
        
        
    })

    /* ---------------- RIMUOVERE UNA CANZONE --------------*/

    var song_uri;
    var parent;

    $(document).on('click', '._delete', function(){
        song_uri = $(this).attr('data-uri');
        parent = $(this).parents('a')
        $('#deleteSongModal').modal('show');
        
    });

    $('#deleteSongModal').on('submit', function(event) {
        event.preventDefault();

         /**
         * Logica eliminazione canzone dalla playlist
         */

        $.ajax({
            type: "DELETE",
            url: `/party/${party_code}/tracks/${song_uri}`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {
                $('#deleteSongModal').modal('hide');
                parent.fadeOut("slow",function(){
                parent.remove()
            })
            },
            error: function(error){
                console.log(error);
            }
        });

           
    })


    channel.listen('.song.voted',function(data){
        console.log(data);
    })


    /*------------VOTE A SONG ------------ */

    $(document).on('click','.like_bat',function(event){
        event.preventDefault();
        let vote = $(this);
        var song_id;
        if($(this).attr('id') == 'vote_left')
            song_id = $('#track_uri_side_1').attr('data-id');
        else if($(this).attr('id') == 'vote_right')
            song_id = $('#track_uri_side_2').attr('data-id');
        
        $.ajax({
            type: "GET",
            url: `/party/${party_code}/tracks/${song_id}/vote`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!response.error){
                    vote.removeClass('like_bat');
                    vote.addClass('unlike');
                    vote.addClass('voted');
                    vote.children('span').text(parseInt(vote.children('span').text()) + 1);
                }
                else {
                    Toast.fire({
                        type: 'error',
                        title: response.error
                        });
                }
                
            },
            error: function(error){
                console.log(error);
            }
        });

    });

    /* -------------------- UNVOTE A SONG -----------------*/

    $(document).on('click','.unlike',function(event){
        event.preventDefault();
        let vote = $(this);
        var song_id;
        if($(this).attr('id') == 'vote_left')
            song_id = $('#track_uri_side_1').attr('data-id');
        else if($(this).attr('id') == 'vote_right')
            song_id = $('#track_uri_side_2').attr('data-id');
        
        $.ajax({
            type: "GET",
            url: `/party/${party_code}/tracks/${song_id}/unvote`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!response.error){
                    vote.removeClass('unlike');
                    vote.addClass('like_bat');
                    vote.removeClass('voted');
                    vote.children('span').text(parseInt(vote.children('span').text()) - 1);
                }
                
            },
            error: function(error){
                console.log(error);
            }
        });

    });



    /** ----------- Gestione dell'aggiunta delle canzoni sul ring in modalità Battle ------------ */

        if($('#left_side_button').length) {
            $('#left_side_button').click( function() {
                $('.song_link').each( function(index, item) {
                    if($(item).attr('data-track') == selected_track) {
                        $('#battleModal').modal('hide');
                        setSongActive(selected_track, party_code, 1, item, $(item).attr('data-song-id'));
                        delete_from_playlist(selected_track);
                    }
                });
            });
        }

        if($('#left_side_button').length) {
            $('#right_side_button').click( function() {
                $('.song_link').each( function(index, item) {
                    if($(item).attr('data-track') == selected_track) {
                        $('#battleModal').modal('hide');

                        setSongActive(selected_track, party_code, 2, item, $(item).attr('data-song-id'));
                        delete_from_playlist(selected_track);
                    }
                });
            });
        }


        function setSongActive(track_uri, code, side, item, track_real_id) {
            $.ajax({
                type: "POST",
                url: '/party/active_track',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                data: {
                    'track_uri' : track_uri,
                    'party_code' : code,
                    'side' : side
                },
                success: function (response) {
                    console.log(response, 'setactiveresponse');
                    if(side == 1) {
                        $('#left_side').children('img').attr('src', $(item).find('img').attr('src'));
                        $('#left_side').find('h5').text($(item).find('h5').text());
                        $('#left_side').find('p').text($(item).find('p').text());
                        $('#left_side').prepend('<span id="track_uri_side_1" data-id="' + track_real_id + '" data-track="' + track_uri + '"></span>');
                        
                        $('#left_side').find('button').attr('disabled', false);
                        console.log('<span id="track_uri_side_1" data-id="' + track_real_id + '" data-track="' + track_uri + '></span>')
                    } else {
                        $('#right_side').children('img').attr('src', $(item).find('img').attr('src'));
                        $('#right_side').find('h5').text($(item).find('h5').text());
                        $('#right_side').find('p').text($(item).find('p').text());
                        $('#right_side').prepend('<span id="track_uri_side_2" data-id="' + track_real_id + '" data-track="' + track_uri + '"></span>');
                        $('#right_side').find('button').attr('disabled', false);
                    }
                },
                error: function(error){
                    Toast.fire({
                        type: 'error',
                        title: error.responseJSON.message
                    });
                    console.log(error);
                }
            });
        }



    };

    // FINE onSpotifyWebPlaybackSDKReady

    


});
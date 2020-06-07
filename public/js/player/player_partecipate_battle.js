$( document ).ready( function() {
    'use strict';

    var party_code = $('#party_code').attr('data-code');
    var channel = Echo.join(`party.${party_code}`);
    var my_id = $('#my_id').data('id');

    var user_code = $('#user_code').attr('data-code');
    var slider = $("#volume_range");
    var timeline = $('#timeline');
    var duration_text = $('.music-duration');
    var timer, running = false;
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
            var new_partecipant_link = new_partecipant.find('a');
            new_partecipant_link.find('.name').text(user.name);
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
        new_partecipant_link.find('.name').text(user.name);
        new_partecipant_link.attr('data-id', user.id);
        $('#joining-list').append(new_partecipant);
    })

    /**
     *  Comunica a tutti che un utente lascia il canale
     */
    channel.leaving((leaving_user) => {
        //console.log('leaving')
        console.log(leaving_user)
        $('#joining-list li').each(function (index, user) {
            console.log(user);
            var partecipant_link = $(this).find('a');
            console.log(partecipant_link);
            if (partecipant_link.attr('data-id') == leaving_user.id) {
                partecipant_link.find('.name').text(partecipant_link.text() + " (leaving party...)");
                setTimeout(function () {
                    user.remove();
                }, 1000);
            }
        });
    })


    

    

    function millisToMinutesAndSeconds(millis) {
        var minutes = Math.floor(millis / 60000);
        var seconds = ((millis % 60000) / 1000).toFixed(0);
        return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
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

            if(state) {
                var dur = state.track_window.current_track.duration_ms;
                $('.total-duration').text( millisToMinutesAndSeconds( dur ) );
                var position = state.position;
                var track_uri = state.track_window.current_track.uri;
                actual_dur = parseInt(state.track_window.current_track.duration_ms);
            }

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
    
            /** SINCRONIZAZZIONE DEL PLAYER CON QUELLO DELL'HOST **/
    
            var privateChannel = Echo.private(`party-sync.${my_id}`);
            
            privateChannel.listen('.player.syncronize', function (data) {
    
                /* Esco dal channel poiché non serve più essere connesso a esso*/
                Echo.leave(`party-sync.${my_id}`);
                
                /* Sincronizzo */
                var instance = axios.create();
                delete instance.defaults.headers.common['X-CSRF-TOKEN'];
                console.log(data.position_ms, 'SYNCRONIZING');
                instance({
                    url: "https://api.spotify.com/v1/me/player/play?device_id=" + devId,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                    },
                    data: {
                        "uris": [data.track_uri],
                        "position_ms": data.position_ms
                    },
                    dataType: 'json',
                }).then(function (data) {
                    
                });
             
            });
        });

        // Not Ready
        player.addListener('not_ready', ({ device_id }) => {
            console.log('Device ID has gone offline', device_id);
        });

        // Connect to the player!
        player.connect();

        /**
         * Per i partecipanti : ascolta l'evento play
         */
        channel.listen('.player.played', (data) => {
            var instance = axios.create();
            delete instance.defaults.headers.common['X-CSRF-TOKEN'];
            console.log(devId);
            console.log(data.position_ms);
            instance({
                url: "https://api.spotify.com/v1/me/player/play?device_id=" + devId,
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                data: {
                    "uris": [data.track_uri],
                    "position_ms": data.position_ms
                },
                dataType: 'json',
            }).then(function (data) {

            });
        });

        /**
         * Per i partecipanti : ascolta l'evento paused
        */

        channel.listen('.player.paused', (data) => {
            player.pause();
        });

        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];

        if($('#track_uri_side_1').length) {
            var uri = $('#track_uri_side_1').attr('data-track');
            var track_id = uri.replace('spotify:track:', '');
            var track_real_id = $('#track_uri_side_1').attr('data-song-id');
            instance({
                url: "https://api.spotify.com/v1/tracks/" + track_id,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                dataType: 'json',
            }).then(function (res) {
                
                var track = res.data;
                var artists = "";
                $.each(track.artists, function (index, artist) {
                    artists += " " + artist.name;
                });

                $('#left_side').children('img').attr('src', track.album.images[0].url);
                $('#left_side').find('h5').text(track.name);
                $('#left_side').find('p').text(artists);
                $('#left_side').prepend('<span id="track_uri_side_1" data-id="' + track_real_id + '" data-track="' + track.uri + '></span>');
                $('#left_side').find('button').attr('disabled', false);
                

            });
        }

        if($('#track_uri_side_2').length) {
            var uri = $('#track_uri_side_2').attr('data-track');
            var track_id = uri.replace('spotify:track:', '');
            var track_real_id = $('#track_uri_side_2').attr('data-song-id');
            instance({
                url: "https://api.spotify.com/v1/tracks/" + track_id,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                dataType: 'json',
            }).then(function (res) {
                
                var track = res.data;
                var artists = "";
                $.each(track.artists, function (index, artist) {
                    artists += " " + artist.name;
                });

                $('#right_side').children('img').attr('src', track.album.images[0].url);
                $('#right_side').find('h5').text(track.name);
                $('#right_side').find('p').text(artists);
                $('#right_side').prepend('<span id="track_uri_side_1" data-id="' + track_real_id + '" data-track="' + track.uri + '></span>');
                $('#right_side').find('button').attr('disabled', false);
                

            });
        }
        



        
        
        channel.listen('.battle.selected',function(data){
            if(data.track == null) {
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
            } else {
                var track_id = data.track.track_uri.replace('spotify:track:', '');
                instance({
                    url: "https://api.spotify.com/v1/tracks/" + track_id,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                    },
                    dataType: 'json',
                }).then(function (res) {
                    var track = res.data;
                    var artists = "";
                    $.each(track.artists, function (index, artist) {
                        artists += " " + artist.name;
                    });

                    if(data.side == "1") {
                        $('#left_side').children('img').attr('src', track.album.images[0].url);
                        $('#left_side').find('h5').text(track.name);
                        $('#left_side').find('p').text(artists);
                        $('#left_side').prepend('<span id="track_uri_side_1" data-id="' + data.track.id + '" data-track="' + data.track.track_uri + '></span>');
                        $('#left_side').find('button').attr('disabled', false);
                    }
                    else if(data.side == "2") {
                        $('#right_side').children('img').attr('src', track.album.images[0].url);
                        $('#right_side').find('h5').text(track.name);
                        $('#right_side').find('p').text(artists);
                        $('#right_side').prepend('<span id="track_uri_side_1" data-id="' + data.track.id + '" data-track="' + data.track.track_uri + '></span>');
                        $('#right_side').find('button').attr('disabled', false);
                    }
                    
    
                });
                
            }
            console.log(data);
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


        
    /**
     *  -------------------- Listener alle canzoni --> quando faccio click su un link della canzone ------------------
     */
    $(document).on('click', '.song_link', function (event) {
        event.preventDefault();
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



    /*------------VOTE A SONG ------------ */

    $(document).on('click','.vote',function(event){
        event.preventDefault();
        let vote = $(this);
        vote.addClass('voted');
    });



    };

    // FINE onSpotifyWebPlaybackSDKReady

    


});
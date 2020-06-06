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
        console.log(leaving_user)
        $('#joining-list li').each(function (index, user) {
            console.log(user);
            var partecipant_link = $(this).find('a');
            console.log(partecipant_link);
            if (partecipant_link.attr('data-id') == leaving_user.id) {
                partecipant_link.text(partecipant_link.text() + " (leaving party...)");
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


    function populate_song_link(item, track, bool) {
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

        if(bool){
            return item;
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
                populate_song_link(song_link, data.data);
                
                
                /**
                 * POPOLAZIONE RING SE ESISTE
                 */
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
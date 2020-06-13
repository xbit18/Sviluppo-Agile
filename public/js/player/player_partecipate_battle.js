
    'use strict';


    if($('#party_code').attr('data-code').length) {
        // Sono in una delle pagine del party
        $('footer').hide();
    }


    var party_code = $('#party_code').attr('data-code');
    var channel = Echo.join(`party.${party_code}`);
    var my_id = $('#my_id').data('id');
    var user_code = $('#user_code').attr('data-code');
    var slider = $("#volume_range");
    var timeline = $('#timeline');
    var duration_text = $('.music-duration');
    var timer, running = false;
    var playlist_dom = $('#party_playlist');
    var actual_dur = 0;
    var actual_track;
    var playlist_uri;
    var selected_song_id;
    var suggested_songs = $('#suggested-songs');


    var selected_track;
    var scrolling;

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
    channel.here((users) => {
        $('#joining-list').empty();
        $.each(users, function (index, user) {
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
        $('#joining-list li').each(function (index, user) {
            var partecipant_link = $(this).find('a');
            if (partecipant_link.attr('data-id') == leaving_user.id) {
                partecipant_link.find('.name').text(partecipant_link.text() + " (leaving party...)");
                setTimeout(function () {
                    user.remove();
                }, 1000);
            }
        });
    })


    var channel_management = Echo.private(`party.${party_code}.${my_id}`);

    channel_management.listen('.user.kicked',function(response){
        if(response.kicked){

            Toast.fire({
                type: 'warning',
                title: 'You have been kicked from the party'
            })
            setTimeout(function(){
                location.replace('/party/show');
                
            },2000)
        }
    })

    channel_management.listen('.user.banned',function(response){
        if(response.banned){

            Toast.fire({
                type: 'warning',
                title: 'The host has banned you permanently'
            })
            setTimeout(function(){
                location.replace('/party/show');
            },2000)
        }
    })

    channel_management.listen('.song.accepted.refused', function(data){
        console.log(data);
        let suggested_song = $('#suggestedSong');
           suggested_song.fadeOut(300,function(){
                suggested_song.remove();
            })
            if(data.accepted){
                Toast.fire({
                    type: 'success',
                    title: 'You suggestion has been accepted by the host'
                })
            } else if(!data.refused){
                Toast.fire({
                    type: 'error',
                    title: 'You suggestion has been refused by the host'
                })
            }
    });
    

    
    function vote_to_skip(code, track_id) {
        $.ajax({
            type: "GET",
            url: `/party/${code}/tracks/${track_id}/skip`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {

                if(!response.error){
                    Toast.fire({
                        type: 'success',
                        title: 'Voted Successfully'
                    });
                }
                else {
                    Toast.fire({
                        type: 'error',
                        title: response.error
                    });
                }
            },
            error: function(error){
                console.log(error, 'skip error');
            }
        });
    }
    

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
        player.addListener('initialization_error', ({ message }) => { console.log(message, 'spotify player initialization error') });
        player.addListener('authentication_error', ({ message }) => { window.location.replace('/loginspotify') });
        player.addListener('account_error', ({ message }) => { window.location.replace('/loginspotify') });
        player.addListener('playback_error', ({ message }) => { console.error(message, 'spotify playback error'); });

        // Playback status updates
        player.addListener('player_state_changed', state => {

            if(state) {
                var dur = state.track_window.current_track.duration_ms;
                $('.total-duration').text( millisToMinutesAndSeconds( dur ) );
                var position = state.position;
                var track_uri = state.track_window.current_track.uri;
                actual_dur = parseInt(state.track_window.current_track.duration_ms);

                if($('#animation-container .wrapper').length) {
                    if(state.paused) {
                        $('#animation-container .wrapper').addClass('wrapper_hidden');
                    }
                    else {
                        $('#animation-container .wrapper').removeClass('wrapper_hidden');
                    }
                }
                
            }

            if (!($('#title-player').text() === state.track_window.current_track.name))
                $('#title-player').text(state.track_window.current_track.name);

            var artists = "";
            $.each(state.track_window.current_track.artists, function (index, artist) {
                artists += artist.name;
            });

            if (!($('#artist-player').text() === artists)) {
                $('#artist-player').text(artists);

                /** AUTOSCROLLING CODE */
                var text = $('.song-details-container > div');
                var text_len = parseInt(text.width());
                var inner_len;
                if(parseInt($(document).width()) <= 768) {
                    inner_len = ( parseInt($('.song-details-container > div > h3').width()) + parseInt($('.song-details-container > div > span').width()) + 3);
                    var pos = 0;
                    text.css('left', '0px');
                    if( text_len < inner_len) {
                        text.css('justify-content', 'unset');
                        var diff = inner_len - text_len;
                        if (!scrolling) {
                            scrolling = setInterval(function() {
                                pos = (pos+1) % (diff + 50);
                                if(pos <= diff) text.css('left', parseInt(0 - pos) + 'px');
                            }, 1000 / 20);
                        }
                        
                    }
                    else {
                        text.css('justify-content', 'center');
                        clearInterval(scrolling);
                        scrolling = null;
                    }
                } else if(scrolling) {
                    text.css('justify-content', 'center');
                    clearInterval(scrolling);
                    scrolling = null;
                }
                
                /*** END */
            }  

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
                actual_track = data.track_uri;
                console.log(data.track_uri)
             
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
            if(data.track != null){
                actual_track = data.track.track_uri;
                selected_song_id = data.track.id;
            }
            instance({
                url: "https://api.spotify.com/v1/me/player/play?device_id=" + devId,
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                data: {
                    "uris": [actual_track],
                    "position_ms": data.position_ms
                },
                dataType: 'json',
            }).then(function (data) {
                console.log(data, 'error on play');
            })
        });

 

        $(document).on('click','.button-skip',function(event){
            event.preventDefault();
            event.stopPropagation();
            vote_to_skip(party_code, selected_song_id);
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
        

        $(document).on('click', '.genre', function (event) {
            event.preventDefault();
        });

        $(document).on('click', '.search', function (event) {
            event.preventDefault();
        });

        $(document).on('submit','#editPartyForm', function(event){
            event.preventDefault();
        })
        
        
        channel.listen('.battle.selected',function(data){
            if(data.track == null) {
                $('#left_side').children('img').attr('src', '/img/bg-img/no_song.png');
                $('#left_side').children('span').remove();
                $('#left_side').find('h5').text('Left Side');
                $('#left_side').find('p').text('No song selected');
                $('#left_side').find('button').attr('disabled', true);
                $('#left_side').find('button').removeClass('voted');
                $('#left_side').find('button').removeClass('unlike');
                $('#left_side').find('button').find('span').text('0');

                $('#right_side').children('img').attr('src', '/img/bg-img/no_song.png');
                $('#right_side').find('h5').text('Right Side');
                $('#right_side').children('span').remove();
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
                        $('#left_side').prepend('<span id="track_uri_side_1" data-id="' + data.track.id + '" data-track="' + data.track.track_uri + '"></span>');
                        $('#left_side').find('button').addClass('like_bat');
                        $('#left_side').find('button').attr('disabled', false);
                    }
                    else if(data.side == "2") {
                        $('#right_side').children('img').attr('src', track.album.images[0].url);
                        $('#right_side').find('h5').text(track.name);
                        $('#right_side').find('p').text(artists);
                        $('#right_side').prepend('<span id="track_uri_side_2" data-id="' + data.track.id + '" data-track="' + data.track.track_uri + '"></span>');
                        $('#right_side').find('button').addClass('like_bat');
                        $('#right_side').find('button').attr('disabled', false);
                    }
                    
    
                });
                
            }
        })



        

        channel.listen('.song.voted',function(data){
        let left = $('#track_uri_side_1');
        let right = $('#track_uri_side_2')
        
        if(left.data('id') == data.song_id){
            $('#left_side').find('button').children('span').text(data.likes)
        } else if(right.data('id') == data.song_id) {
            $('#right_side').find('button').children('span').text(data.likes);
        }
        })
    
        channel.listen('.refresh.party',function(){
            location.reload();
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
                    if(!response.error){
                        vote.removeClass('like_bat');
                        vote.addClass('unlike');
                        vote.addClass('voted');
                        //vote.children('span').text(parseInt(vote.children('span').text()) + 1);
                    }
                    else {
                        Toast.fire({
                            type: 'error',
                            title: response.error
                            });
                    }
                    
                },
                error: function(error){
                    console.log(error, 'like button error');
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
                    if(!response.error){
                        vote.removeClass('unlike');
                        vote.addClass('like_bat');
                        vote.removeClass('voted');
                        //vote.children('span').text(parseInt(vote.children('span').text()) - 1);
                    }
                    else {
                        Toast.fire({
                            type: 'error',
                            title: response.error
                            });
                    }
                    
                },
                error: function(error){
                    console.log(error, 'unvote error');
                }
            });
    
        });


        
    /**
     *  -------------------- Listener alle canzoni --> quando faccio click su un link della canzone ------------------
     */
    $(document).on('click', '.song_link', function (event) {
        event.preventDefault();
    });

    $(document).on('click', '.partecipant', function (event) {
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

               /* Inizializzo la lista delle canzone suggerite */


    $('.suggested-song').each(function (index, element) {


        let track_uri = $(this).data('track-uri');
        if(track_uri){
            let track_id = track_uri.replace('spotify:track:', '');

        instance({
            url: "https://api.spotify.com/v1/tracks/" + track_id,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            dataType: 'json',
        }).then(function (data) {

            let img = $(element).children('div').children('div').first().find('img');
            img.attr('src', data.data.album.images[0].url);

            let content = $(element).children('div').children('div').last();
            content.children('div').eq(1).children().first().find('h6').text(data.data.name);
            content.children('div').eq(1).children().first().find('small').text(millisToMinutesAndSeconds(data.data.duration_ms));

            let artists = "";
            $.each(data.data.artists, function (index, artist) {
                artists += artist.name + ' ';
            });

            content.children('div').eq(1).children().last().children().first().text(artists);
            content.children('div').eq(1).children().last().children().last().text(data.data.album.name)

            $(element).addClass('suggest-item');
            $(element).attr('id','suggestedSong')
            $(element).removeClass('d-none');
            // $(element).removeClass('d-none');
            // $(element).removeAttr('id');

        })
            .catch(function(e){
                console.log(e)
            })
 
        }
       
    });

    
    /*----------------------- CERCARE UNA CANZONE --------------------*/
    $('#searchSong').on('keyup', function (e) {

        var song_name = $('#searchSong').val();
        song_name = encodeURIComponent(song_name.trim());
        var result = $('#result');

        if (song_name.length == 0) {
            result.fadeOut("normal", function () {
                result.empty();
            });
        }

        if (song_name.length > 0) {

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
                        img.attr('src', element.album.images[0].url);

                        let content = item.children('div').children('div').last();
                        content.children('div').first().find('h6').text(element.name);
                        content.children('div').first().find('small').text(millisToMinutesAndSeconds(element.duration_ms));

                        let artists = "";
                        $.each(element.artists, function (index, artist) {
                            artists += artist.name + ' ';
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
                .catch(function (error) {
                    console.log('search error: ');
                    console.log(error);
                })
        }
    })

    /* SUGGERIRE UNA CANZONE */

    $(document).on('click', '.item', function (event) {
        event.preventDefault();
        let track_uri = $(this).data('uri');
        let track_id = $(this).data('id');
        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];

        $.ajax({
            url: `/party/${party_code}/tracks/suggest/add`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'track_uri': track_uri
            },
            dataType: 'json',
            success: function (response) {
                if (response.warning) {
                    Toast.fire({
                        type: 'warning',
                        title: response.warning,
                    })
                } else {

                    let prototype = $('#suggested-prototype').clone();

                    instance({
                        url: "https://api.spotify.com/v1/tracks/" + track_id,
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                        },
                        dataType: 'json',
                    }).then(function (data) {
        
                        let img = prototype.children('div').children('div').first().find('img');
                        img.attr('src', data.data.album.images[0].url);
        
                        let content = prototype.children('div').children('div').last();
                        content.children('div').eq(1).children().first().find('h6').text(data.data.name);
                        content.children('div').eq(1).children().first().find('small').text(millisToMinutesAndSeconds(data.data.duration_ms));
        
                        let artists = "";
                        $.each(data.data.artists, function (index, artist) {
                            artists += artist.name + ' ';
                        });
        
                        content.children('div').eq(1).children().last().children().first().text(artists);
                        content.children('div').eq(1).children().last().children().last().text(data.data.album.name)
        
                        prototype.attr('data-track-uri', track_uri)
                        prototype.addClass('suggest-item');
                        prototype.addClass('suggested-song');
                        prototype.removeClass('d-none');
                        prototype.attr('id','suggestedSong');
                        suggested_songs.append(prototype).hide().fadeIn(200);
        
                        Toast.fire({
                            type: 'success',
                            title: 'Song suggested'
                        })
                    })
                        .catch(function(e){
                            console.log(e)
                        })
                }

            },
            error: function (e) {
                console.log(e);
            }
        })


    })

    
    $(document).on('click','.suggested-delete',function(event){
        event.preventDefault();
        event.stopPropagation();
       
        
        let parent = $(this).parents('.suggested-song');
        
        $.ajax({
            type: "POST",
            url: `/party/${party_code}/tracks/suggest/remove`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{

            },  
            dataType: "json",
            success: function (response) {
                console.log('removed');
                parent.fadeOut(300,function(){
                    parent.remove();
                })

            },
            error: function (error) {
               
            }
        });
    })

  // FINE onSpotifyWebPlaybackSDKReady



    };

  
    
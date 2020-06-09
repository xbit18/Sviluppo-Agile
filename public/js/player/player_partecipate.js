
    'use strict';


    var party_code = $('#party_code').attr('data-code');
    var user_code = $('#user_code').attr('data-code');
    var slider = $("#volume_range");
    var timeline = $('#timeline');
    var duration_text = $('.music-duration');
    var my_id = $('#my_id').data('id');
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

    
    var channel_management = Echo.private(`party.${party_code}.${my_id}`);

    channel_management.listen('.user.kicked',function(response){
        console.log(response);
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
        console.log(response);
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



    
    
    function order_playlist() {
        playlist_dom.children().sort(sort_li).appendTo(playlist_dom).hide().fadeIn(500);
        function sort_li(a, b) {
          return ($(b).find('button').eq(0).find('span').text()) < ($(a).find('button').eq(0).find('span').text()) ? -1 : 1;
        }
      };
    

    

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



    function populate_song_link(item, track, id, bool = false) {
        //console.log('sono in populate')
        //console.log(item)
        //console.log(track)
        item.find('h5').text(track.name);
        item.data('song-id',id);
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
        player.addListener('authentication_error', ({ message }) => { /* window.location.replace('/loginspotify')*/ });
        player.addListener('account_error', ({ message }) => { /*window.location.replace('/loginspotify') */ });
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

            if($('#animation-container .wrapper').lenght) {
                if(state.paused) {
                    $('#animation-container .wrapper').addClass('wrapper_hidden');
                }
                else {
                    $('#animation-container .wrapper').removeClass('wrapper_hidden');
                }
            }

            


            if (!state.paused) {
                prec_play = true;
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
                                console.log(data.track_uri);
                                $('.song_link').each( function(index, item) {
                                    if( $(this).attr('data-track') == data.track_uri ) {
                                        $(this).click();
                                    }
                                });
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

    $(document).on('click', '.song_link', function (event) {
        event.preventDefault();
    });

    $(document).on('click', '.partecipant', function (event) {
        event.preventDefault();
    });

    $(document).on('click', '.genre', function (event) {
        event.preventDefault();
    });

    $(document).on('click', '.search', function (event) {
        event.preventDefault();
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
            let song_id = $(this).data('song-id');
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


    

        /** -------------- Volume Listener ----------------------- */

        // HammerJs for mobile
        // var slide2 = document.getElementById('volume_range');

        // var mc_volume = new Hammer.Manager(slide2);
        // mc_volume.add( new Hammer.Tap({ event: 'singletap' }) );
        // mc_volume.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL }));
        // mc_volume.add(new Hammer.Pan({ direction: Hammer.DIRECTION_HORIZONTAL }));

        // mc_volume.on("singletap pan swipe", function(ev) {
        //     player.setVolume(slider.val() / 100)
        // });

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


            
        // /** ---------------- TIMELINE Listener ----------------- */

        //  //COMPATIBILITà MOBILE : Devo usare la sintassi pure js : hammer js da problemi con selettore $
        // var timeline_mob = document.getElementById('timeline');

        // var mc_timeline = new Hammer.Manager(timeline_mob);
        // mc_timeline.add( new Hammer.Tap({ event: 'singletap' }) );
        // mc_timeline.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL }));
        // mc_timeline.add(new Hammer.Pan({ direction: Hammer.DIRECTION_HORIZONTAL }));

        // mc_timeline.on("singletap pan swipe", function(ev) {
        //     // SKIP LOGIC
        //     if(timeline.val() != 0) {
        //         player.seek(timeline.val()).then(() => {
        //             //console.log('Changed position mob!');
        //           });
        //     }
            
        // }); 

        // // Compatibilità Desktop
        // var isDragTime = false;
        // timeline.click(function () {
        //     if(timeline.val() != 0) {
        //         player.seek(timeline.val()).then(() => {
        //             ///console.log('Changed position!');
        //         });
        //     }
              
        // });

              /*----------------------- CERCARE UNA CANZONE --------------------*/
    // $('#searchSong').on('keyup',function(e){

    //     var song_name = $('#searchSong').val();
    //     song_name = encodeURIComponent(song_name.trim());
    //     var result = $('#result');

    //     if(song_name.length == 0){
    //         result.fadeOut("normal",function(){
    //             result.empty();
    //         });
    //     }

    //     if(song_name.length > 0){

    //         var instance = axios.create();
    //         delete instance.defaults.headers.common['X-CSRF-TOKEN'];

    //         instance({
    //         url: `https://api.spotify.com/v1/search?q=${song_name}&type=track,artist&limit=5`,
    //         method: 'GET',
    //         headers: {
    //             'Authorization': 'Bearer ' + token,
    //         },
    //         dataType: 'json',

    //     })
    //     .then(function (data) {
    //         result.empty();   
    //         let tracks = data.data.tracks.items;

    //         $.each(tracks, function (index, element) { 

    //            let item = $('#song-prototype').clone();
    //            let img = item.children('div').children('div').first().find('img');
    //            img.attr('src',element.album.images[0].url);
                
    //            let content = item.children('div').children('div').last();
    //            content.children('div').first().find('h6').text(element.name);
    //            content.children('div').first().find('small').text(millisToMinutesAndSeconds(element.duration_ms));

    //            let artists = "";
    //             $.each(element.artists, function (index, artist) {
    //                 artists += artist.name +' ';
    //             });

    //             content.children('div').last().children().first().text(artists);
    //             content.children('div').last().children().last().text(element.album.name)

    //             item.attr('data-id', element.id);
    //             item.attr('data-uri', element.uri)
    //             item.attr('data-duration', element.duration_ms)
    //             item.attr('data-number', index)
    //             item.addClass('item');
    //             item.removeAttr('id');
    //             result.append(item).hide().fadeIn();            
               

    //         });
   
    //     })
    //     .catch(function(error){
    //         console.log('search error: ');
    //         console.log(error);
    //     })
    //     }
    // })

    /* AGGIUNGERE LA CANZONE ALLE TRACKS DI UN PARTY */

    // $(document).on('click','.item',function(event){
    //     event.preventDefault();
    //     let track_uri = $(this).data('uri');
    //     let track_id = $(this).data('id');
    //     let song_id;
    //     // let track_img_src = $(this).children('div').children('div').first().find('img').attr('src');
    //     // let track_duration = $(this).data('duration');
    //     // let track_artists = $(this).children('div').children('div').last().children('div').last().children().first().text();
    //     // let track_album = $(this).children('div').children('div').last().children('div').last().children().last().text();
    //     // let track_name = $(this).children('div').first().find('h6').text();



    //     var instance = axios.create();
    //     delete instance.defaults.headers.common['X-CSRF-TOKEN'];
       
    //     $.ajax({
    //     url: `/party/${party_code}/tracks`,
    //     method: 'POST',
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     },
    //     data: {
    //         'track_uri' : track_uri
    //     },
    //     dataType: 'json',
    //     success: function(response){
    //         // snapshot_id = response.data.snapshot_id;
    //         // console.log(snapshot_id);
    //         console.log(response);
    //         song_id = response.id;
    //         instance({
    //             url: "https://api.spotify.com/v1/tracks/" + track_id,
    //             method: 'GET',
    //             headers: {
    //                 'Authorization': 'Bearer ' + token,
    //             },
    //             dataType: 'json',
    //         }).then(function (data) {
    //             // console.log(data, 'track_info');

    //             let song_link = $('#playlist_song_prototype').clone();

    //             let item = populate_song_link(song_link, data.data,song_id,true);
    //             playlist_dom.append(item).hide().fadeIn(1000);


    //             const Toast = Swal.mixin({
    //                 toast: true,
    //                 position: 'top-end',
    //                 showConfirmButton: false,
    //                 timer: 6000
    //             });
    
    //             Toast.fire({
    //                 type: 'success',
    //                 title: 'The song has been added to the Playlist!'
    //                 });

    //         })
    //         .catch(function(error){
    //             console.log(error);
    //         });


         
            
    //                // instance({
    //         //     url: `https://api.spotify.com/v1/me/player/queue?uri=` + track_uri,
    //         //     method: 'POST',
    //         //     headers: {
    //         //         'Authorization': 'Bearer ' + token,
    //         //     },
    //         //     data: {
    //         //         'uri' : track_uri,
    //         //         'device_id' : devId,
    //         //     },
    //         //     dataType: 'json',
    //         //     })
    //         // .then(function(response){
    //         //     console.log('canzone aggiunta alla coda')
    //         // });
    //         /*
    //         var item_s = $('#playlist_song_prototype').clone();
    //         let index = $('#party_playlist').children().last().data('number');
    //         item_s.find('h5').text(track_name);
            
    //         item_s.find('p').text(track_artists);
    //         // append_song(my_party_playlist, track_id, track_uri, track_name, track_artists, track_img_src, track_album)

    //         var thumb = item_s.find('img');
    //         thumb.attr('src', track_img_src);
            
    //         item_s.children('div').children('div').children('small').text(track_album);
    //         item_s.children('div').children('div').children('div').children('small').text(millisToMinutesAndSeconds(track_duration));
    //         item_s.children('div').children('div').children('div').children('small').children('a').attr('data-id', track_id);
    //         item_s.attr('data-id', track_id);
    //         item_s.attr('data-uri', track_uri);
    //         item_s.attr('data-number', index + 1);
    //         item_s.attr('data-playlist-uri', my_party_playlist.uri);
    //         item_s.addClass('song_link');
    //         $('#party_playlist').append(item_s);
    //         */
    //     }
    //     })
        
        
    // })

    /* ---------------- RIMUOVERE UNA CANZONE --------------*/

    // var parent;
    // var song_id;
    // $(document).on('click', '._delete', function(){
    //     parent = $(this).parents('a');
    //     song_id = parent.data('song-id');
    //     console.log(parent);
    //     $('#deleteSongModal').modal('show');
        
    // });

    // $('#deleteSongModal').on('submit', function(event) {
    //     event.preventDefault();

    //      /**
    //      * Logica eliminazione canzone dalla playlist
    //      */

    //     $.ajax({
    //         type: "DELETE",
    //         url: `/party/${party_code}/tracks/${song_id}`,
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         dataType: "json",
    //         success: function (response) {
    //             $('#deleteSongModal').modal('hide');
    //             parent.fadeOut("slow",function(){
    //             parent.remove()
    //         })
    //         },
    //         error: function(error){
    //             console.log(error);
    //         }
    //     });

           
    // })
    /* ------------------------------LISTENER AL CHANNEL DELLE VOTAZIONI ------------------------- */

    channel.listen('.song.voted', function (data) {
        // console.log(data,'evento');
        //    let first = playlist_dom.children().first().data('song-id');
        let current = playlist_dom.find("[data-song-id='" + data.song_id + "']");
        console.log(current,'current!');
        let current_likes = current.find('button').eq(0).find('span').text(data.likes);
        console.log(current_likes);
        order_playlist();
        //    let next;
        //    let next_likes;
        //    let prev;
        //    let prev_likes;

        //    if(first != current.data('song-id')){

        //     //    console.log(current);
        //         prev = current.prev();
        //         prev_likes = prev.find('button').eq(1).find('span').text();

        //         next = current.next();
        //         next_likes = next.find('button').eq(1).find('span').text();

        //          if(current_likes.text() < next_likes ){
        //             current.fadeOut("slow",function(){
        //                 current.remove()
        //                 current.hide().insertAfter(next).fadeIn("slow");
        //             })

        //          } else if(current_likes.text() > prev_likes ){
        //              current.fadeOut("slow",function(){
        //                  current.remove();
        //                  current.hide().insertBefore(prev).fadeIn("slow");
        //              });

        //          }

        //    }

    })




    /*------------VOTE A SONG ------------ */

    $(document).on('click','.like',function(event){
        event.preventDefault();
        event.stopPropagation()
        let vote = $(this);
        let parent = $(this).parents('a.song_link');
        let song_id = parent.data('song-id');
        
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
                    vote.removeClass('like');
                    vote.addClass('unlike');
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
        event.stopPropagation()
        let vote = $(this);
        let parent = $(this).parents('a.song_link');
        let song_id = parent.data('song-id');
        
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
                    vote.addClass('like');
                }
                
            },
            error: function(error){
                console.log(error);
            }
        });

    });

/* ---------------------------------------------------------------- */

        if($('#left_side_button').length) {
            $('#left_side_button').click( function() {
                $('.song_link').each( function(index, item) {
                    if($(item).attr('data-track') == selected_track) {
                        $('#battleModal').modal('hide');

                        setSongActive(selected_track, party_code, 1, item);
                        
                    }
                });
            });
        }

        if($('#left_side_button').length) {
            $('#right_side_button').click( function() {
                $('.song_link').each( function(index, item) {
                    if($(item).attr('data-track') == selected_track) {
                        $('#battleModal').modal('hide');

                        setSongActive(selected_track, party_code, 2, item);
                    }
                });
            });
        }


        function setSongActive(track_uri, code, side, item) {
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
                    } else {
                        $('#right_side').children('img').attr('src', $(item).find('img').attr('src'));
                        $('#right_side').find('h5').text($(item).find('h5').text());
                        $('#right_side').find('p').text($(item).find('p').text());
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        }



    };

    // FINE onSpotifyWebPlaybackSDKReady

    



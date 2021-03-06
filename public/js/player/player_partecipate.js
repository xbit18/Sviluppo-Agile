'use strict';


if ($('#party_code').attr('data-code').length) {
    // Sono in una delle pagine del party
    $('footer').hide();
}

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
var scrolling;
var selected_song_id;
var suggested_songs = $('#suggested-songs');


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

channel_management.listen('.user.kicked', function (response) {
    if (response.kicked) {
        Toast.fire({
            type: 'warning',
            title: 'You have been kicked from the party'
        })
        setTimeout(function () {
            location.replace('/party/show');

        }, 2000)
    }
})

channel_management.listen('.user.banned', function (response) {
    if (response.banned) {

        Toast.fire({
            type: 'warning',
            title: 'The host has banned you permanently'
        })
        setTimeout(function () {
            location.replace('/party/show');
        }, 2000)
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


function populate_song_link(item, track, id, bool = false) {
    //console.log('sono in populate')
    item.find('h5').text(track.name);
    item.data('song-id', id);
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

    if (bool) {
        return item;
    }
}

function vote_to_skip(code, track_id) {
    $.ajax({
        type: "GET",
        url: `/party/${code}/tracks/${track_id}/skip`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        success: function (response) {

            if (!response.error) {
                Toast.fire({
                    type: 'success',
                    title: 'Voted Successfully'
                });
                console.log(response);
            } else {
                Toast.fire({
                    type: 'error',
                    title: response.error
                });
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}


window.onSpotifyWebPlaybackSDKReady = () => {
    //const token = 'BQCuguaURpWrApdQ0lkd0xLCl_W8TEVTE0p7LcnHgj1Bn0Dm9AqbhnogAMRx2oOwL7GemNvloRy73NprTPRCqeQX_ifEOY3fzgmGyH9YW9TP5uZSkOB2Z4rAVVUEHB1BxodMvunn5EfRjmFSLLFhgQBuQ9YJ2t_aaKr6uYVPjplCA5AqBr4KxmXDcHxqiANOOrClo9zb';
    const token = $('#mytoken').text();
    const player = new Spotify.Player({
        name: 'Web Player Party App',
        getOAuthToken: cb => {
            cb(token);
        }
    });


    var devId;

    // Error handling
    player.addListener('initialization_error', ({ message }) => {
        console.log(message)
    });
    player.addListener('authentication_error', ({ message }) => {
        window.location.replace('/loginspotify')
    });
    player.addListener('account_error', ({ message }) => {
        window.location.replace('/loginspotify')
    });
    player.addListener('playback_error', ({ message }) => {
        console.error(message);
    });

    player.addListener('player_state_changed', state => {

        if (state) {

            var track_uri;

            /**
             * Settaggio della timeline
             */
            var dur = state.track_window.current_track.duration_ms;
            $('.total-duration').text(millisToMinutesAndSeconds(dur));

            timeline.attr('max', dur);

            if (!($('#title-player').text() === state.track_window.current_track.name)) {
                $('#title-player').text(state.track_window.current_track.name);
                $('#vote_ad').removeClass('d-none');


                var artists = "";
                $.each(state.track_window.current_track.artists, function (index, artist) {
                    artists += " " + artist.name;
                });

            }

            if (!($('#artist-player').text() === artists)) {
                $('#artist-player').text(artists);

                /** AUTOSCROLLING CODE */
                var text = $('.song-details-container > div');
                var text_len = parseInt(text.width());
                var inner_len;
                if (parseInt($(document).width()) <= 768) {
                    inner_len = (parseInt($('.song-details-container > div > h3').width()) + parseInt($('.song-details-container > div > span').width()) + 3);
                    //console.log(inner_len + '    ' + text_len, 'autoscroll debug');
                    var pos = 0;
                    text.css('left', '0px');
                    if (text_len < inner_len) {
                        text.css('justify-content', 'unset');
                        var diff = inner_len - text_len;
                        if (!scrolling) {
                            scrolling = setInterval(function () {
                                pos = (pos + 1) % (diff + 50);
                                if (pos <= diff) text.css('left', parseInt(0 - pos) + 'px');
                            }, 1000 / 20);
                        }

                    } else {
                        text.css('justify-content', 'center');
                        clearInterval(scrolling);
                        scrolling = null;
                    }
                } else if (scrolling) {
                    text.css('justify-content', 'center');
                    clearInterval(scrolling);
                    scrolling = null;
                }

                /*** END */
            }
            var position = state.position;
            track_uri = state.track_window.current_track.uri;
            actual_dur = parseInt(state.track_window.current_track.duration_ms);

            if ($('#animation-container .wrapper').length) {
                if (state.paused) {
                    $('#animation-container .wrapper').addClass('wrapper_hidden');
                } else {
                    $('#animation-container .wrapper').removeClass('wrapper_hidden');
                }
            }


        } else {
            var position = 0;
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
            actual_track = data.track_uri;

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
        if (data.track != null) {
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
            console.log(data);
        })
    });

    /**
     * Per i partecipanti : ascolta l'evento paused
     */

    channel.listen('.song.added', function (data) {


        if (data.tracks.length > 1) {
            $.each(data.tracks, function (index, element) {

                let track_id = element.track_uri.replace('spotify:track:', '');
                let song_id = element.id;

                instance({
                    url: "https://api.spotify.com/v1/tracks/" + track_id,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                    },
                    dataType: 'json',
                }).then(function (data) {
                    let song_link = $('#playlist_song_prototype').clone();
                    song_link.find('button').eq(0).find('span').addClass('like');
                    song_link.removeAttr('id');
                    song_link.attr('data-track', data.data.uri);
                    song_link.attr('data-song-id', song_id);
                    let item = populate_song_link(song_link, data.data, song_id, true);
                    playlist_dom.append(item).hide().fadeIn();

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 6000
                    });

                    Toast.fire({
                        type: 'success',
                        title: 'A new son has been added to the playlist!'
                    });

                })
                    .catch(function (error) {
                        console.log(error);
                    });

            });
        } else {
            let track_id = data.tracks.track_uri.replace('spotify:track:', '');
            let song_id = data.tracks.id;
            instance({
                url: "https://api.spotify.com/v1/tracks/" + track_id,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                dataType: 'json',
            }).then(function (data) {
                let song_link = $('#playlist_song_prototype').clone();
                song_link.removeAttr('id');
                song_link.attr('data-track', data.data.uri);
                song_link.attr('data-song-id', song_id);
                let item = populate_song_link(song_link, data.data, song_id, true);
                playlist_dom.append(item).hide().fadeIn();

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 6000
                });

                Toast.fire({
                    type: 'success',
                    title: 'A new son has been added to the playlist!'
                });

            })
                .catch(function (error) {
                    console.log(error);
                });
        }


    })

    channel.listen('.song.removed', function (data) {
        $('.song_link').each(function (index, item) {

            if ($(item).attr('data-song-id') == data.track.id) {
                $(item).fadeOut(300, function () {
                    $(item).remove();
                });

            }
        });

    })

    $(document).on('submit', '#editPartyForm', function (event) {
        event.preventDefault();
    })

    channel.listen('.refresh.party', function () {
        location.reload();
    })

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

    $('.song_link').each(function (index, item) {
        var song_link = $(this);
        var track_uri = song_link.attr('data-track');
        var track_id = track_uri.replace('spotify:track:', '');
        let song_id = $(this).data('song-id');

        // Chiamo per ottenere i dati della traccia
        instance({
            url: "https://api.spotify.com/v1/tracks/" + track_id,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            dataType: 'json',
        }).then(function (data) {
            populate_song_link(song_link, data.data);


            /**
             * POPOLAZIONE RING SE ESISTE
             */
            if ($('#track_uri_side_1').length) {
                var uri = $('#track_uri_side_1').attr('data-track');
                $('.song_link').each(function (index, item) {

                    if ($(item).attr('data-track') == uri) {
                        $('#left_side').children('img').attr('src', $(item).find('img').attr('src'));
                        $('#left_side').find('h5').text($(item).find('h5').text());
                        $('#left_side').find('p').text($(item).find('p').text());
                    }
                });
            }

            if ($('#track_uri_side_2').length) {
                var uri = $('#track_uri_side_2').attr('data-track');
                $('.song_link').each(function (index, item) {
                    if ($(item).attr('data-track') == uri) {
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

    //COMPATIBILITà MOBILE : Devo usare la sintassi pure js : hammer js da problemi con selettore $
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

    // Compatibilità Desktop
    var isDragTime = false;
    timeline.click(function () {
        if (timeline.val() != 0) {
            player.seek(timeline.val()).then(() => {
                ///console.log('Changed position!');
            });
        }

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


    /* ------------------------------LISTENER AL CHANNEL DELLE VOTAZIONI ------------------------- */

    channel.listen('.song.voted', function (data) {
        let current = playlist_dom.find("[data-song-id='" + data.song_id + "']");
        let current_likes = current.find('button').eq(0).find('span').text(data.likes);
        order_playlist();
    })


    /*------------VOTE TO SKIP SONG ------------ */

    $(document).on('click', '.button-skip', function (event) {
        event.preventDefault();
        event.stopPropagation();
        vote_to_skip(party_code, selected_song_id);
    });


    /*------------VOTE A SONG ------------ */

    $(document).on('click', '.like', function (event) {
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
                if (!response.error) {
                    vote.removeClass('like');
                    vote.addClass('unlike');
                    $('#vote_ad').addClass('d-none');
                }

            },
            error: function (error) {
                console.log(error);
            }
        });

    });

    /* -------------------- UNVOTE A SONG -----------------*/

    $(document).on('click', '.unlike', function (event) {
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
                if (!response.error) {
                    vote.removeClass('unlike');
                    vote.addClass('like');
                    $('#vote_ad').removeClass('d-none');
                }

            },
            error: function (error) {
                console.log(error);
            }
        });

    });

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

    /* ---------------------------------------------------------------- */

};

// FINE onSpotifyWebPlaybackSDKReady





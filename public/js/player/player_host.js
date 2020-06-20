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
var timer, running = false;
var channel = Echo.join(`party.${party_code}`);
var playlist_dom = $('#party_playlist');
var actual_dur = 0;
var actual_track;
var playlist_uri;
var selected_track;
var selected_song_id;
var scrolling;
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


function millisToMinutesAndSeconds(millis) {
    var minutes = Math.floor(millis / 60000);
    var seconds = ((millis % 60000) / 1000).toFixed(0);
    return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
}


function increment_timeline(data) {
    //console.log('funzione increment timeline chiamata');
    if (data) {
        if (!running) {
            running = true;
            timer = setInterval(() => {
                //$('.music-duration').text( millisToMinutesAndSeconds(timeline.val()) );
                timeline.val(parseInt(timeline.val()) + 1000);
                duration_text.text(millisToMinutesAndSeconds(parseInt(timeline.val())));
                //console.log('incrementing ' + timeline.val());
                var v = (timeline.val()) / actual_dur;
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

            }, 1000);
        }
    } else {
        //console.log('clearing');
        clearInterval(timer);
        running = false;
    }
}

function order_playlist() {
    playlist_dom.children().sort(sort_li).appendTo(playlist_dom).hide().fadeIn(500);
    ;

    function sort_li(a, b) {
        return ($(b).find('button').eq(1).find('span').text()) < ($(a).find('button').eq(1).find('span').text()) ? -1 : 1;
    }
};


function populate_song_link(item, track, id = null, bool = false) {
    var artists = "";
    $.each(track.artists, function (index, artist) {
        artists += " " + artist.name;
    });

    var thumb = item.find('img');
    thumb.attr('src', track.album.images[0].url);
    item.find('h5').text(track.name);
    item.find('p').text(artists);
    item.children('div').children('div').children('small').text(track.album.name);
    item.children('div').children('div').children('div').children('small').children('button').attr('data-uri', track.uri);
    item.addClass('song_link');

    if (bool && id) {
        item.data('song-id', id);
        return item;
    }
}

function delete_song(code, id) {
    $.ajax({
        url: "/party/" + code + "/tracks/" + id,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function (data) {
            console.log('canzone eliminata');
        },
        error: function (xhr, ajaxOptions, thrownError) {
            /** Error Handling */
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

function get_next_song(code) {


    $.ajax({
        url: "/party/" + code + "/getNextTrack",
        method: 'GET',
        // async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function (data) {

            $('.song_link').each(function (index, item) {
                if ($(this).attr('data-track') == data.track_uri) {
                    $(this).click()
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

function accept_suggested_song_if_major(elements, new_uri) {
    
    // Dopo 5 suggerimenti viene aggiunta la canzone
    var count = 0, size = 5; 
    $.each(elements.children('div'), function (index, element) {
        if($(element).attr('data-track-uri') == new_uri) count++;
    });

    if(count >= size) {
        // Accetto
        elements.find(".suggest-item[data-track-uri='" + new_uri + "']").first().click();
        elements.find(".suggest-item[data-track-uri='" + new_uri + "']").find(".suggested-delete").click();
    }
}

const play_song = function (code, id, position_ms) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "/party/" + code + "/play",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "track_id": id,
                "position_ms": position_ms
            },
            dataType: 'json',
            success: function (data) {

                resolve(data);
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
                reject(xhr);
            }
        });
    });

}


window.onSpotifyWebPlaybackSDKReady = () => {
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

        var track_uri;

        if (state) {

            /**
             * Settaggio della timeline
             */
            var dur = state.track_window.current_track.duration_ms;
            $('.total-duration').text(millisToMinutesAndSeconds(dur));

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
        } else {
            var position = 0;
        }


        if (state) {
            if (!state.paused) {
                prec_play = true;
                paused = false;
                if (position == 0) timeline.val(0);
                increment_timeline(true);
                play_song(party_code, selected_song_id, position)

            } else if (state.paused) {


                // LA CANZONE è FINITAAAAAAAAAAAAAAAAAAAAAAAAA
                if (!paused && prec_play) {
                    get_next_song(party_code);
                    prec_play = false;
                } else if (paused && prec_play) {
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
                }


            }
            actual_track = state.track_window.current_track.uri;
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


    /**
     * Listener alle canzoni
     */
    $(document).on('click', '.song_link', function (event) {
        event.preventDefault();

        // Se ho cliccato su elimina non deve partire
        if (event.target.classList.contains('_delete') || event.target.classList.contains('fa-times') || event.target.classList.contains('like') || event.target.classList.contains('unlike')) return;

        var track_uri = $(this).attr('data-track');
        var link = $(this);
        selected_song_id = $(this).data('song-id');
        var song_id = selected_song_id;


        /**
         * AJAX CALL FOR PLAY THAT SONG
         */
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
            paused = false;

            play_song(party_code, song_id, 0).then((response) => {
                delete_song(party_code, song_id);
            })
                .catch((e) => {
                    console.log(e);
                });


            link.fadeOut('normal', () => {
                link.remove();
            });

        });


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


    $(document).on('click', 'i.kick', function (event) {
        event.preventDefault();
        let user_id = $(this).parents('a').data('id');
        let kick_form = $('#kick_form');
        kick_form.unbind('submit');
        $('#kickModal').modal();

        kick_form.on('submit', function (event) {
            event.preventDefault();
            let date = $("input[name='date']").val();
            let hour = $("input[name='hour']").val();
            let kick_duration = date + ' ' + hour + ':00'
            $.ajax({
                type: "POST",
                url: `/party/${party_code}/user/${user_id}/kick`,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "kick_duration": kick_duration
                },
                dataType: "json",
                success: function (response) {
                    $('#kickModal').modal('hide');

                    if (response.error) {
                        Toast.fire({
                            type: 'warning',
                            title: response.message
                        })
                    } else {
                        Toast.fire({
                            type: 'success',
                            title: response.message
                        })
                    }


                },
                error: function (error) {
                    console.log(error);
                }
            });
        })

    });

    $(document).on('click', 'i.ban', function (event) {
        event.preventDefault();
        var user_id = $(this).parents('a').data('id');
        var user_name = $(this).parents('a').find('.name').text();
        let ban_form = $('#ban_form');
        ban_form.unbind('submit');
        $('#banModal').modal();

        ban_form.on('submit', function (event) {
            event.preventDefault();
            $.ajax({
                type: "GET",
                url: `/party/${party_code}/user/${user_id}/ban`,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function (response) {

                    $('#banModal').modal('hide');
                    if (response.error) {
                        Toast.fire({
                            type: 'warning',
                            title: response.message
                        })

                    } else {
                        Toast.fire({
                            type: 'success',
                            title: response.message
                        })

                        let user_prototype = $('#user-prototype').clone();
                        user_prototype.removeAttr('id');
                        user_prototype.removeClass('d-none');
                        user_prototype.attr('data-id', user_id);
                        user_prototype.find('.ban-name').text(user_name);
                        $('#ban-list').append(user_prototype);

                    }
                },
                error: function (error) {

                    console.log(error);
                }
            });
        })

    });

    $(document).on('click', '.user', function (event) {


        let user = $(this)
        $.ajax({
            type: "GET",
            url: `/party/${party_code}/user/${user.data('id')}/unban`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {

                if (response.error) {
                    Toast.fire({
                        type: 'warning',
                        title: response.message
                    })

                } else {
                    Toast.fire({
                        type: 'success',
                        title: response.message
                    })

                    user.fadeOut(800, function () {
                        user.remove()
                    })
                }
            },
            error: function (error) {

                console.log(error);
            }
        });


    });


    /** -------------- Play Button Listener ----------------------- */
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

    /** -------------- Pause Button Listener ----------------------- */
    $('#spotify_pause_form').on('submit', function (event) {
        event.preventDefault();
        paused = true;
        player.pause()
    });

    /** ---------------- NEXT TRACK  ***/

    $('#spotify_next_form').on('submit', function (event) {
        event.preventDefault();
        get_next_song(party_code);
    });


    /** -------------- Volume Listener ----------------------- */

    // HammerJs for mobile
    var slide2 = document.getElementById('volume_range');

    var mc_volume = new Hammer.Manager(slide2);
    mc_volume.add(new Hammer.Tap({ event: 'singletap' }));
    mc_volume.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL }));
    mc_volume.add(new Hammer.Pan({ direction: Hammer.DIRECTION_HORIZONTAL }));

    mc_volume.on("singletap pan swipe", function (ev) {
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
    mc_timeline.add(new Hammer.Tap({ event: 'singletap' }));
    mc_timeline.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_HORIZONTAL }));
    mc_timeline.add(new Hammer.Pan({ direction: Hammer.DIRECTION_HORIZONTAL }));

    mc_timeline.on("singletap pan swipe", function (ev) {
        // SKIP LOGIC
        if (timeline.val() != 0) {
            player.seek(timeline.val()).then(() => {
                //console.log('Changed position mob!');
            });
        }

    });

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
            // $(element).removeClass('d-none');
            // $(element).removeAttr('id');

        })
            .catch(function (e) {
                console.log(e)
            })


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

    /* AGGIUNGERE LA CANZONE ALLE TRACKS DI UN PARTY */

    $(document).on('click', '.item', function (event) {
        event.preventDefault();
        let track_uri = $(this).data('uri');
        let track_id = $(this).data('id');
        let song_id;
        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];

        $.ajax({
            url: `/party/${party_code}/tracks`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'track_uri': track_uri
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
            },
            error: function (e) {
                console.log(e);
            }
        })


    })

    $(document).on('click', '.suggest-item', function (event) {
        event.preventDefault();
        // Se ho cliccato su elimina non deve partire
        if (event.target.classList.contains('suggested-delete')) return;

        let track_uri = $(this).data('track-uri');
        let user_id = $(this).data('user-id');
        let item = $(this)

        $.ajax({
            url: `/party/${party_code}/tracks`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'track_uri': track_uri,
                'user_id': user_id,
            },
            dataType: 'json',
            success: function (response) {

                item.fadeOut(300, function () {
                    item.remove()
                })

                Toast.fire({
                    type: 'success',
                    title: 'Song added to the playlist'
                })
            },
            error: function (e) {
                console.log(e);
            }
        })


    })

    /* ---------------- RIMUOVERE UNA CANZONE --------------*/

    var parent;
    var song_id;
    $(document).on('click', '._delete', function () {
        parent = $(this).parents('a');
        song_id = parent.data('song-id');
        $('#deleteSongModal').modal('show');

    });

    $('#deleteSongModal').on('submit', function (event) {
        event.preventDefault();

        /**
         * Logica eliminazione canzone dalla playlist
         */

        $.ajax({
            type: "DELETE",
            url: `/party/${party_code}/tracks/${song_id}`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {
                $('#deleteSongModal').modal('hide');
                parent.fadeOut("slow", function () {
                    parent.remove()
                })
            },
            error: function (error) {
                console.log(error);
            }
        });


    })

    /**
     * Comunica a tutti i partecipanti del canale quando un utente si unisce
     */


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

        $.ajax({
            type: "GET",
            url: "/party/" + party_code + "/join/" + user.id,
            data: "data",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {
            },
            error: function (e) {
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
                        },
                        error: function (e) {
                            console.log(e)
                        }
                    });
                })
            }

        }, 4000)
    })

    /**
     *  Comunica a tutti che un utente lascia il canale
     */

    /* ------------------------------LISTENER AL CHANNEL DELLE VOTAZIONI ------------------------- */

    channel.listen('.song.voted', function (data) {
        let current = playlist_dom.find("[data-song-id='" + data.song_id + "']");
        let current_likes = current.find('button').eq(1).find('span').text(data.likes);
        order_playlist();
    })

    channel.listen('.song.suggested', function (data) {


        if (data.suggested) {

            let track_uri = data.track_uri;
            let track_id = track_uri.replace('spotify:track:', '');
            let prototype = $('#suggested-prototype').clone();
            let user_id = data.user_id;

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

                prototype.attr('data-user-id', user_id);
                prototype.attr('data-track-uri', track_uri)
                prototype.addClass('suggest-item');
                prototype.addClass('suggested-song');
                prototype.removeClass('d-none');
                prototype.removeAttr('id');
                suggested_songs.append(prototype).hide().fadeIn(200);


                Toast.fire({
                    type: 'success',
                    title: 'A new song has been suggested'
                })

                accept_suggested_song_if_major(suggested_songs, track_uri);
            })
                .catch(function (e) {
                    console.log(e)
                })


        } else if (!data.suggested) {
            $('.suggested-song').each(function (index, element) {
                if ($(element).data('user-id') == data.user_id) {
                    $(element).fadeOut(300, function () {
                        $(element).remove()
                        return;
                    })
                }
            })
        }

    })



$(document).on('submit', '#playlistPopolate', function (event) {
    event.preventDefault();

    let genre = $('#genre').val();


    $.ajax({
        type: "POST",
        url: "/party/playlist/populate",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        data: {
            "genre_id": genre,
            "party_code": party_code
        },
        success: function (response) {

        },
        error: function (error) {
            console.log(error);
        }
    });

})

channel.listen('.song.auto-skip', function () {
    get_next_song(party_code)
})


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
                    title: 'A new song has been added to the playlist!'
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
            }

        },
        error: function (error) {
            console.log(error);
        }
    });

});

/**  Remove a suggested song **/

$(document).on('click', '.suggested-delete', function (event) {
    event.preventDefault();
    event.stopPropagation();


    let parent = $(this).parents('.suggested-song');
    let user_id = parent.data('user-id');

    $.ajax({
        type: "POST",
        url: `/party/${party_code}/tracks/suggest/remove`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            "user_id": user_id
        },
        dataType: "json",
        success: function (response) {
            console.log('removed');
            parent.fadeOut(300, function () {
                parent.remove();
            })

        },
        error: function (error) {

        }
    });
})

/* ---------------------------------------------------------------- */

$(document).on('click','#management',function(event){
    event.preventDefault()
})


}
;

// FINE onSpotifyWebPlaybackSDKReady





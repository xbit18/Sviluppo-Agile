$( document ).ready( function() {
    'use strict';

    function populate_song_link(item, track) {
        console.log('sono in populate')
        console.log(item)
        console.log(track)
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


    };

    // FINE onSpotifyWebPlaybackSDKReady

    


});
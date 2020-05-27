

/** -------Prendo il codice del party dall'URI ------**/

var party_code = window.location.href.slice(33);
var channel = Echo.join(`party.${party_code}`);

/* Music Pause */ 

/**
 * Comunica a tutti i partecipanti del canale
 */
channel.here((users) => {
  console.log(users)
});

/**
 *  Action a utente entrante
 */
channel.joining((user) => {
  console.log('joining')
  console.log(user)
})

/**
 *  Comunica a tutti che un utente lascia il canale
 */
channel.leaving((user) => {
  console.log('leaving')
  console.log(user)
})

/* Music Pause */

/**
 * Per i partecipanti : ascolta l'evento paused
 */
channel.listen('.player.paused', () => {
  console.log('player paused')
})




  /**
   * INITIALIZATION SPOTIFY PLAYER
   */
  window.onSpotifyWebPlaybackSDKReady = () => {
    //const token = 'BQCuguaURpWrApdQ0lkd0xLCl_W8TEVTE0p7LcnHgj1Bn0Dm9AqbhnogAMRx2oOwL7GemNvloRy73NprTPRCqeQX_ifEOY3fzgmGyH9YW9TP5uZSkOB2Z4rAVVUEHB1BxodMvunn5EfRjmFSLLFhgQBuQ9YJ2t_aaKr6uYVPjplCA5AqBr4KxmXDcHxqiANOOrClo9zb';
    const token = $('#mytoken').text();
    const player = new Spotify.Player({
        name: 'Web Player Party App',
        getOAuthToken: cb => { cb(token); }
    });

    var devId;

    // Error handling
    player.addListener('initialization_error', ({ message }) => { console.error(message); });
    player.addListener('authentication_error', ({ message }) => { console.error(message); });
    player.addListener('account_error', ({ message }) => { console.error(message); });
    player.addListener('playback_error', ({ message }) => { console.error(message); });

    // Playback status updates
    player.addListener('player_state_changed', state => { 
        console.log(state);
        if(!($('#title-player').text() === state.track_window.current_track.name))
            $('#title-player').text(state.track_window.current_track.name);

        var artists = "";
        $.each( state.track_window.current_track.artists, function( index, artist ){
            artists += artist.name;
        });

        if(!($('#artist-player').text() === artists))
            $('#artist-player').text(artists);
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



    /**
   * Per i partecipanti : ascolta l'evento play
   */
    channel.listen('.player.played', (data) => {
        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];
        console.log(devId);
        instance({
            url: "https://api.spotify.com/v1/me/player/play?device_id=" + devId,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            data: {
                "uris": [data.track_uri],
                "position_ms": 0
            },
            dataType: 'json',
        }).then( function(data) {
        
        });    
    });



};


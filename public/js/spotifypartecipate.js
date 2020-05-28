

/** -------Prendo il codice del party dall'URI ------**/

var party_code = $('#party_code').attr('data-code');
var channel = Echo.join(`party.${party_code}`);

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
    //console.log(user)
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

/* Music Pause */





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
    player.addListener('initialization_error', ({ message }) => { window.location.replace('/loginspotify') });
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
    });

    // Not Ready
    player.addListener('not_ready', ({ device_id }) => {
        console.log('Device ID has gone offline', device_id);
    });

    // Connect to the player!
    player.connect();



    var slider = $("#volume_range");

    slider.on('click', function () {
        player.setVolume(slider.val() / 100);
    });



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
        }).then(function (data) {

        });
    });

    /**
     * Per i partecipanti : ascolta l'evento paused
    */

    channel.listen('.player.paused', (data) => {
        player.pause();
    });


    var slider = $("#volume_range");
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

};


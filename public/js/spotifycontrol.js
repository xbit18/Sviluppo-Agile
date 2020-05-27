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
     * BUTTONS CONTROLS
     */

    $('#spotify_play_form').on('submit', function(event) {
        event.preventDefault();
    
        /**
         * AJAX CALL FOR PLAY
         */
        
        var instance = axios.create();
        delete instance.defaults.headers.common['X-CSRF-TOKEN'];

        var actual_context_uri;
        var actual_position;


        console.log(devId);

        // Mi Prendo la traccia corrente
        $.ajax({
            url: "https://api.spotify.com/v1/me/player",
            method: 'GET',
            dataType: "json",
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            success: function(data){
                // DEBUGGING
                console.log("data");
                console.log(data);
                //actual_context_uri = data.item.uri;
                //$('#title-player').val(data.item.name);
                
                instance({
                    url: "https://api.spotify.com/v1/me/player/play?device_id=" + devId,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                    },
                    data: {
                        "context_uri": "spotify:album:6OwvO40ahugJE5PH4TjqTg",
                        "offset": {
                            "position": 5
                        },
                        "position_ms": 0
                        },
                    dataType: 'json',
                    success: function(data){
                        // DEBUGGING
                        //console.log(data);
                    },
                    error:function (xhr, ajaxOptions, thrownError){ 
                        /**
                         * Error Handling
                         */
                        if(xhr.status == 404) {
                            console.log("404 NOT FOUND");
                        }else if(xhr.status == 500) {
                            console.log("500 INTERNAL SERVER ERROR");
                        }else{
                            console.log("errore");
                        }
                    }
                });    

            },
            error:function (xhr, ajaxOptions, thrownError){ 
                /**
                 * Error Handling
                 */
                if(xhr.status == 404) {
                    console.log("404 NOT FOUND");
                }else if(xhr.status == 500) {
                    console.log("500 INTERNAL SERVER ERROR");
                }else{
                    console.log("errore");
                }
            }
        });

        


        


    }); 


























    $('#spotify_pause_form').on('submit', function(event) {
        event.preventDefault();

        /**
         * AJAX CALL FOR PAUSE
         */

        $.ajax({
            url: "https://api.spotify.com/v1/me/player/pause?device_id=" + devId,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
            },
            dataType: 'json',
            success: function(data){
                // DEBUGGING
                //console.log(data);
            },
            error:function (xhr, ajaxOptions, thrownError){ 
                /**
                 * Error Handling
                 */
                if(xhr.status == 404) {
                    console.log("404 NOT FOUND");
                }else if(xhr.status == 500) {
                    console.log("500 INTERNAL SERVER ERROR");
                }else{
                    console.log("errore");
                }
            }
        });

    }); 



};


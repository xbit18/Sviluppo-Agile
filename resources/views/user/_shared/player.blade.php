<div class="poca-music-area mt-40 d-flex align-items-center flex-wrap" data-animation="fadeInUp" data-delay="900ms">
    
    <div class="poca-music-content poca-music-content-play">
        <!-- Single Widget Area -->
        {{-- <form id="add_to_party" action="#" method="post">
            @csrf
            <input readonly="readonly" id="invite_list" type="text" name="song_name" class="form-control email">
            <button id="add_to_party_btn" type="submit" class="btn">Add</button>
        </form> --}}
        <span id="artist-player" class="music-published-date">Artist</span>
        <h2 id="title-player">Play your music</h2>
        <div class="music-meta-data">
        <p>By <a href="#" class="music-author">Admin</a> | <a href="#" class="music-catagory">Tutorials</a> | <a href="#" class="music-duration">00:02:56</a></p>
        </div>
        
        <!-- Music Player -->
        <div class="row">
            @if(Auth::user()->id == $party->user->id)    
            <form  id="spotify_prev_form" action="#">
                <button class="btn player-button-adders" id="prev-song" type="submit"><i class="fa fa-step-backward" aria-hidden="true"></i></button>
            </form> 
            <form  id="spotify_play_form" action="#">
                <button class="btn player-button-play" id="play" type="submit"><i class="fa fa-play" aria-hidden="true"></i></button>
            </form>
            <form  id="spotify_pause_form" action="#">
                <button class="btn player-button-stop" id="pause" type="submit"><i class="fa fa-pause" aria-hidden="true"></i></button>
            </form> 
            <form  id="spotify_next_form" action="#">
                <button class="btn player-button-adders" id="next-song" type="submit"><i class="fa fa-step-forward" aria-hidden="true"></i></button>
            </form> 
            
            
            @endif

            <div class="slidecontainer">
                <label><i class="fa fa-volume-up" aria-hidden="true"></i></label>
                <input type="range" min="0" max="100" value="50" class="slider" id="volume_range">
            </div>

            
        </div>
        
        <!-- Likes, Share & Download -->
        <div class="likes-share-download d-flex align-items-center justify-content-between">
        <a href="#"><i class="fa fa-heart" aria-hidden="true"></i> Like (29)</a>
        <div>
            <a href="#" class="mr-4"><i class="fa fa-share-alt" aria-hidden="true"></i> Share(04)</a>
            <a href="#"><i class="fa fa-download" aria-hidden="true"></i> Download (12)</a>
        </div>
        </div>
          {{-- PLAYLIST --}}
    <div class="single-widget-area catagories-widget mt-5 mb-40">
        <h5 class="widget-title">Songs</h5>

        <!-- catagories list -->
        <ul id="party-song-list" class="catagories-list">
            <li class="d-none" id="song-prototype"><a href="#"></a></li>
        </ul>
    </div>
    </div>
    

    <form id="spotify_login_form" action="/loginspotify" method="GET">
        <button type="submit" class="btn spotfy-style-play"><i class="fa fa-spotify" aria-hidden="true"></i> Login</button>
        <p id="device_id" class="d-none"></p>
    </form>
    
    <form  id="spotify_logout_form" action="/logoutspotify" method="GET">
        <button type="submit" class="btn spotfy-style-play"><i class="fa fa-sign-out" aria-hidden="true"></i> EXIT</button>
    </form>
</div>
<p id="mytoken" class="d-none">{{ Auth::user()->access_token }}</p>



<div class="poca-music-area mt-100 d-flex align-items-center flex-wrap" data-animation="fadeInUp" data-delay="900ms">
    
    

    <div class="poca-music-content poca-music-content-play">
        <!-- Single Widget Area -->
        <div class="single-widget-area catagories-widget mb-80">
            <h5 class="widget-title">Songs</h5>

            <!-- catagories list -->
            <ul id="party-song-list" class="catagories-list">
                <li class="d-none" id="song-prototype"><a href="#"></a></li>
            </ul>
        </div>
        <span id="artist-player" class="music-published-date">Artist</span>
        <h2 id="title-player">Play your music</h2>
        <div class="music-meta-data">
        <p>By <a href="#" class="music-author">Admin</a> | <a href="#" class="music-catagory">Tutorials</a> | <a href="#" class="music-duration">00:02:56</a></p>
        </div>
        <!-- Music Player -->
        <div class="row">
            <form  id="spotify_play_form" action="/playback/play">
                <button class="btn player-button-play" id="play" type="submit"><i class="fa fa-play" aria-hidden="true"></i></button>
            </form>
            <form  id="spotify_pause_form" action="/playback/pause">
                <button class="btn player-button-play" id="pause" type="submit"><i class="fa fa-pause" aria-hidden="true"></i></button>
            </form>
            <form id="spotify_login_form" action="/loginspotify" method="GET">
                <button type="submit" class="btn spotfy-style-play">Login</button>
                <p id="device_id"></p>
            </form>
            
            <form  id="spotify_logout_form" action="/logoutspotify" method="GET">
                <button type="submit" class="btn spotfy-style-play">Logout</button>
            </form>
        </div>
        <!-- Likes, Share & Download -->
        <div class="likes-share-download d-flex align-items-center justify-content-between">
        <a href="#"><i class="fa fa-heart" aria-hidden="true"></i> Like (29)</a>
        <div>
            <a href="#" class="mr-4"><i class="fa fa-share-alt" aria-hidden="true"></i> Share(04)</a>
            <a href="#"><i class="fa fa-download" aria-hidden="true"></i> Download (12)</a>
        </div>
        </div>
    </div>
</div>
<p id="mytoken" class="d-none">{{ Auth::user()->access_token }}</p>
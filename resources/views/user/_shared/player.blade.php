<div class="poca-music-area mt-40 d-flex align-items-center flex-wrap player-container" data-animation="fadeInUp" data-delay="900ms">
    
    <div class="poca-music-content poca-music-content-play">
        <div class="text-right"><p><i class="fa fa-spotify" aria-hidden="true"></i> Spotify</p></div>
        <!-- Single Widget Area -->
        {{-- <form id="add_to_party" action="#" method="post">
            @csrf
            <input readonly="readonly" id="invite_list" type="text" name="song_name" class="form-control email">
            <button id="add_to_party_btn" type="submit" class="btn">Add</button>
        </form> --}}



        <div class="player-box">
            <span id="artist-player" class="music-published-date">Artist</span>
            <h3 id="title-player" class="title_track"><i class="fa fa-caret-right mr-1" aria-hidden="true"></i> Play your music</h3>
            <div class="music-meta-data">
                <p>By <a href="#" class="music-author">Admin</a> | <a href="#" class="music-duration">0:00</a> | <a href="#" class="total-duration">0:00</a></p>
            </div>

            @if(Auth::user()->id == $party->user->id)  
            <div id="timeline_container">
                <input type="range" min="0" value="0" class="slider" id="timeline"> 
            </div>
            @endif
            
            <!-- Music Player -->
            <div class="row justify-content-center">
                @if(Auth::user()->id == $party->user->id)   
                <div class="col-sm-9 player-buttons-container">
                
                    <form class="d-inline" id="spotify_prev_form" action="#">
                        <button class="btn player-button-adders" id="prev-song" type="submit"><i class="fa fa-step-backward" aria-hidden="true"></i></button>
                    </form> 
                    <form class="d-inline" id="spotify_play_form" action="#">
                        <button class="btn player-button-play" id="play" type="submit"><i class="fa fa-play" aria-hidden="true"></i></button>
                    </form>
                    <form class="d-inline" id="spotify_pause_form" action="#">
                        <button class="btn player-button-stop" id="pause" type="submit"><i class="fa fa-pause" aria-hidden="true"></i></button>
                    </form> 
                    <form class="d-inline" id="spotify_next_form" action="#">
                        <button class="btn player-button-adders" id="next-song" type="submit"><i class="fa fa-step-forward" aria-hidden="true"></i></button>
                    </form> 
                
                
                </div>
                @endif
                <div class="@if(Auth::user()->id == $party->user->id) col-sm-3 @endif slidecontainer">
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="desktop-vol-lab"><i class="fa fa-volume-up" aria-hidden="true"></i></label>
                        </div>
                        <div class="col-sm-9">
                            <label class="d-none mobile-vol-lab mr-2"><i class="fa fa-volume-up" aria-hidden="true"></i></label>
                            <input type="range" min="0" max="100" value="50" class="slider" id="volume_range">  
                        </div>
                    </div>
                </div>

                
            </div>

        </div>
        
        <!-- Likes, Share & Download -->
        <!-- <div class="likes-share-download d-flex align-items-center justify-content-between">
        <a href="#"><i class="fa fa-heart" aria-hidden="true"></i> Like (29)</a>
        <div>
            <a href="#" class="mr-4"><i class="fa fa-share-alt" aria-hidden="true"></i> Share(04)</a>
            <a href="#"><i class="fa fa-download" aria-hidden="true"></i> Download (12)</a>
        </div>
        </div> -->
          {{-- PLAYLIST --}}
    <div class="single-widget-area catagories-widget mt-5 mb-40">
        <h5 class="widget-title">Songs</h5>

        <div class="d-none">    
            <a id="playlist_song_prototype" href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="row song_row">
                    <div class="col-sm-3 album_img_container">
                        <img class="album_img"/>
                    </div>
                    <div class="col-sm-9">
                        <div class="d-flex w-100 justify-content-between title_song" >
                            <h5 class="mb-1"></h5>
                            <small> 
                                <button class="btn btn-danger _delete">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </button>
                            </small>
                        </div>
                        <p class="mb-1"></p>
                        <small></small>
                    </div>
                </div>
                
            </a>
        </div>
        <div class="list-group" id="party_playlist">
        </div>

    </div>
    </div>
    
</div>
<p id="mytoken" class="d-none">{{ Auth::user()->access_token }}</p>


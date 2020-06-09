<div class="poca-music-area mt-40 d-flex align-items-center flex-wrap player-container" data-animation="fadeInUp" data-delay="900ms">
    
    <div class="poca-music-content poca-music-content-play">
        
        
        <div class="player-box d-flex justify-content-between">

            <div class="song-details-container d-flex justify-row align-self-lg-center w-100">
                <div class="align-self-lg-center">
                    <span id="artist-player" class="music-published-date">Artist</span>
                    <h3 id="title-player" class="title_track"><i class="fa fa-caret-right mr-1" aria-hidden="true"></i> Play your music</h3>
                    <div class="music-meta-data">
                    </div>
                </div>
                @if(Auth::user()->id != $party->user->id)
                <div class="align-self-lg-center ml-2">
                    <form class="d-inline align-self-center" id="spotify_next_form" action="#">
                        <button title="Vote To Skip" class="btn button-skip" type="submit"><i class="fa fa-step-forward" aria-hidden="true"></i></button>
                    </form> 
                </div>
                @endif
            </div>
            
            @if(Auth::user()->id == $party->user->id)   
            <div id="player-controls-container" class="align-self-lg-center flex-even">
                
                <div class="player-buttons-container d-flex justify-content-center">
                    @if($party->type == "Battle")
                    <form class="d-inline align-self-center" id="spotify_play_form" action="#">
                        <button class="btn player-button-play" id="play" type="submit"><i class="fa fa-play" aria-hidden="true"></i></button>
                    </form>
                    <form class="d-inline align-self-center" id="spotify_pause_form" action="#">
                        <button class="btn player-button-stop" id="pause" type="submit"><i class="fa fa-pause" aria-hidden="true"></i></button>
                    </form> 
                    @else
                    <form class="d-inline align-self-center" id="spotify_prev_form" action="#">
                        <button class="btn player-button-adders" disabled id="prev-song" type="submit"><i class="fa fa-step-backward" aria-hidden="true"></i></button>
                    </form> 
                    <form class="d-inline align-self-center" id="spotify_play_form" action="#">
                        <button class="btn player-button-play" id="play" type="submit"><i class="fa fa-play" aria-hidden="true"></i></button>
                    </form>
                    <form class="d-inline align-self-center" id="spotify_pause_form" action="#">
                        <button class="btn player-button-stop" id="pause" type="submit"><i class="fa fa-pause" aria-hidden="true"></i></button>
                    </form> 
                    <form class="d-inline align-self-center" id="spotify_next_form" action="#">
                        <button class="btn player-button-adders"   id="next-song" type="submit"><i class="fa fa-step-forward" aria-hidden="true"></i></button>
                    </form> 
                    @endif 
                </div>

                <div id="timeline_container" class="d-flex justify-content-between flex-even">
                    <p class="music-duration mr-2">0:00</p>
                    <input type="range" min="0" value="0" class="slider mr-2" id="timeline"> 
                    <p class="total-duration">0:00</p>
                </div>

            </div>
            @else
            <div id="animation-container" class="align-self-lg-center flex-even text-center">
                <div class="music_participant_animation">
                    <div class="wrapper wrapper_hidden">
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            @endif

            <div class="slidecontainer d-flex flex-row-reverse align-self-center w-100">
                
                <div class="d-flex justify-content-between">
                    <label class="mobile-vol-lab mr-2 align-self-center"><i class="fa fa-volume-up" aria-hidden="true"></i></label>
                    <input type="range" min="0" max="100" value="50" class="slider align-self-center" id="volume_range">  
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
          
    </div>
    
</div>
<p id="mytoken" class="d-none">{{ Auth::user()->access_token }}</p>


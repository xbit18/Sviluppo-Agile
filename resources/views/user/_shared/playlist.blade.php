@if(!isset($battle))

{{-- --------------------- DEMOCRACY ---------------------- --}}

{{-- PLAYLIST --}}
<div class="single-widget-area catagories-widget mt-3 mb-40 h-50">
    @if($party->type == 'Democracy' || Auth::user()->id == $party->user->id)
    <div class="row justify-content-between  mt-20 mb-2">
        <div class="col-md-4 col-12 text-center">
            <h5 class="widget-title">SONGS</h5>
        </div>
        <div class="col-md-4 col-12 text-center">
            @if(Auth::user()->id == $party->user->id) <button type="button" class="btn poca-btn" data-toggle="modal"
                data-target="#addSongsModal">+10 songs</button> @endif
        </div>
    </div>
    @endif

    <!-- Prototype for adding -->
    <div class="d-none" class="playlist-proto">
        <a id="playlist_song_prototype" href="#"
            class="list-group-item list-group-item-action flex-column align-items-start">
            <div class="row song_row">
                <div class="col-md-3 col-sm-2 col-2 album_img_container align-self-center">
                    <img class="album_img" />
                </div>
                <div class="col-md-7 col-sm-8 col-8">
                    <div class="d-flex w-100 justify-content-between title_song">
                        <h5 class="mb-1"></h5>
                        <small>
                            @if(Auth::id() == $party->user->id)
                            <button class="btn btn-danger _delete">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                            @endif
                        </small>
                    </div>
                    <p class="mb-1"></p>
                    <small class="d-none"></small>
                </div>
                <div class="col-sm-2 col-2">
                    <button @if($party->type == 'Battle') disabled="disabled" @endif class="btn btn-default like"><i
                            class="fa fa-heart mr-1" aria-hidden="true"></i><span>0</span></button>
                </div>
            </div>

        </a>
    </div>
    <div class="party_playlist_container">
        <div class="list-group" id="party_playlist">
            <!-- Actual playlist-->
            @if($party->type == 'Democracy' || Auth::user()->id == $party->user->id)
            @forelse($party->tracks->sortBy('votes')->reverse() as $song)

            <a href="#"
                class="list-group-item list-group-item-action flex-column align-items-start song_link @if($party->type == 'Battle' && $song->active != 0) d-none @endif"
                data-track="{{ $song->track_uri }}" data-song-id="{{ $song->id }}">

                <div class="row song_row">
                    <div class="col-md-3 col-sm-2 col-2 album_img_container align-self-center">
                        <img class="album_img" />
                    </div>
                    <div class="col-md-7 col-sm-8 col-8">
                        <div class="d-flex w-100 justify-content-between title_song">
                            <h5 class="mb-1"></h5>
                            <small>
                                @if(Auth::id() == $party->user->id)
                                <button class="btn btn-danger _delete">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </button>
                                @endif
                            </small>
                        </div>
                        <p class="mb-1"></p>
                        <small class="d-none"></small>
                    </div>
                    <div class="col-sm-2 col-2">
                        <button @if($party->type == 'Battle') disabled="disabled" @endif class="btn btn-default
                            {{$liked == $song->id ? 'unlike' : 'like'  }}"><i class="fa fa-heart mr-1"
                                aria-hidden="true"></i> <span>{{$song->votes}}</span></button>
                    </div>
                </div>

            </a>

            @empty
            <p>No songs</p>
            @endforelse
            @endif
        </div>
    </div>

</div>


@else

{{-- ---------------- BATTLE ------------------------ --}}

{{-- PLAYLIST --}}
<div class="single-widget-area catagories-widget h-50" id="battle-playlist">
    @if($party->type == 'Democracy' || Auth::user()->id == $party->user->id)
    <div class="row justify-content-around  mt-12 mb-2 ">
        <div class="col-md-4 col-12 text-center">
            <h5 class="widget-title">SONGS</h5>
        </div>
        <div class="col-md-4 col-12 text-center">
            @if(Auth::user()->id == $party->user->id) <button type="button" class="btn poca-btn" data-toggle="modal"
                data-target="#addSongsModal">+10 songs</button> @endif
        </div>
    </div>
    @endif

    @if($party->type == 'Democracy' || Auth::user()->id == $party->user->id)

    <!-- Prototype for adding -->
    <div class="d-none">
        <a id="playlist_song_prototype" href="#"
            class="list-group-item list-group-item-action flex-column align-items-start">
            <div class="row song_row">
                <div class="col-sm-2 col-2 col-md-3 album_img_container align-self-center">
                    <img class="album_img" />
                </div>
                <div class="col-sm-10 col-10 col-md-9">
                    <div class="d-flex w-100 justify-content-between title_song">
                        <h5 class="mb-1"></h5>
                        <small>
                            <button class="btn btn-danger _delete">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                        </small>
                    </div>
                    <p class="mb-1"></p>
                    <small class="d-none"></small>
                </div>
            </div>

        </a>
    </div>
    <div class="party_playlist_container">
        <div class="list-group" id="party_playlist">
            <!-- Actual playlist-->
            
            @forelse($party->tracks->sortBy('votes')->reverse() as $song)

            <a href="#"
                class="list-group-item list-group-item-action flex-column align-items-start song_link @if($party->type == 'Battle' && $song->active != 0) d-none @endif"
                data-track="{{ $song->track_uri }}" data-song-id="{{ $song->id }}">

                <div class="row song_row">
                    <div class="col-md-3 col-sm-2 col-2 album_img_container align-self-center">
                        <img class="album_img" />
                    </div>
                    <div class="col-md-9 col-sm-10 col-10">
                        <div class="d-flex w-100 justify-content-between title_song">
                            <h5 class="mb-1"></h5>
                            <small>
                                <button class="btn btn-danger _delete">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </button>
                            </small>
                        </div>
                        <p class="mb-1"></p>
                        <small class="d-none"></small>
                    </div>
                </div>

            </a>

            @empty
            <p>No songs</p>
            @endforelse
            
        </div>
    </div>

    @endif
</div>

@endif
<div class="ring mt-5">
  <h4><i class="fa fa-star" aria-hidden="true"></i> Battle Mode Ring <i class="fa fa-star" aria-hidden="true"></i></h4>
  <div class="vs-cont d-none d-sm-none d-md-none d-lg-block d-xl-block"
    style="background-image: url({{ asset('/img/bg-img/vs.png') }})">
  </div>
  <div class="row mt-5 battle-box">
    <div
      class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 p-2 p-sm-2 p-md-4 p-lg-5 p-xl-5 mb-2 mb-sm-2 mb-md-2 mb-lg-2 mb-xl-2">
      <div id="left_side" class="card side">
        <img class="card-img-top" src="{{ asset('/img/bg-img/no_song.png') }}" alt="Card image cap">
        @if(isset($side_1) && !empty($side_1))
        <span id="track_uri_side_1" data-id="{{$side_1->id}}" data-track="{{$side_1->track_uri}}"></span>
        @endif
        <div class="card-body">
          <h5>Left Side</h5>
          <p class="card-text">No song selected</p>
          @if(!isset($side_1) || empty($side_1))
          <button id="vote_left" disabled type="button" class="btn poca-back like_bat">
            <i class="fa fa-heart mr-1" aria-hidden="true"></i> <span class="badge badge-light">@if(isset($side_1) &&
              !empty($side_1)) {{$side_1->votes}} @else 0 @endif</span>
          </button>
          @else
          <button id="vote_left" type="button"
            class="btn poca-back @if($liked == $side_1->id) unlike @else like_bat @endif">
            <i class="fa fa-heart mr-1" aria-hidden="true"></i> <span class="badge badge-light">@if(isset($side_1) &&
              !empty($side_1)) {{$side_1->votes}} @else 0 @endif</span>
          </button>
          @endif

        </div>
      </div>
    </div>
    <!-- <div class="col-2" id="vs_cont"><img class="vs_img" src="{{ asset('/img/bg-img/vs.png') }}"></div> -->
    <div
      class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 p-2 p-sm-2 p-md-4 p-lg-5 p-xl-5 mb-2 mb-sm-2 mb-md-2 mb-lg-2 mb-xl-2">
      <div id="right_side" class="card side">
        <img class="card-img-top" src="{{ asset('/img/bg-img/no_song.png') }}" alt="Card image cap">
        @if(isset($side_2) && !empty($side_2))
        <span id="track_uri_side_2" data-id="{{$side_2->id}}" data-track="{{$side_2->track_uri}}"></span>
        @endif
        <div class="card-body">
          <h5>Right Side</h5>
          <p class="card-text">No song selected</p>
          @if(!isset($side_2) || empty($side_2))
          <button id="vote_right" disabled type="button" class="btn poca-back like_bat">
            <i class="fa fa-heart mr-1" aria-hidden="true"></i> <span class="badge badge-light">0</span>
          </button>
          @else
          <button id="vote_right" type="button"
            class="btn poca-back @if($liked == $side_2->id) unlike @else like_bat @endif">
            <i class="fa fa-heart mr-1" aria-hidden="true"></i> <span
              class="badge badge-light">{{$side_2->votes}}</span>
          </button>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
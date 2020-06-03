@extends('user.layouts.layout')

@section('content')


  <!-- ***** Breadcrumb Area Start ***** -->
  <div class="breadcumb-area bg-img bg-overlay" style="background-image: url({{ asset('img/bg-img/2.jpg') }});">
    <div class="container h-100">
      <div class="row h-100 align-items-center">
        <div class="col-12">
          <h2 class="title mt-70">Party Details</h2>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcumb--con">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">...</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- ***** Breadcrumb Area End ***** -->


@isset($party)
@include('user._shared.invite', ['code' => $party->code ])

<!-- ***** Blog Details Area Start ***** -->
<section class="blog-details-area">
    <div class="container">
      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="podcast-details-content d-flex mt-5 mb-80">

            <!-- Post Share -->
            <div class="post-share">
              <p>Share</p>
              <div class="social-info">
                <a href="#" class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                <a href="#" class="twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                <a href="#" class="google-plus"><i class="fa fa-google-plus" aria-hidden="true"></i></a>
                <a href="#" class="pinterest"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                <a href="#" class="thumb-tack"><i class="fa fa-thumb-tack" aria-hidden="true"></i></a>
              </div>
            </div>

            <!-- Post Details Text -->
            <div class="post-details-text">
              <div class="row">
                <div class="col-sm-3 party_img_container">
                  <img id="party_img_genre" src="{{ asset('img/bg-img/genres/' . $party->genre_id . '.jpg') }}" class="col-sm-3 mb-30 party_img" alt="">
                </div>


                <div class="post-content col-sm-9 ">
                  <span class="d-none" data-code="{{$party->code}}" id="party_code"></span>
                  <span class="d-none" data-code="{{Auth::user()->id}}" id="user_code"></span>
                  <a href="#" class="post-date">{{ $party->created_at }}</a>
                  @if(Auth::user()->id == $party->user->id) <button type="button" class="btn poca-btn setting-button" data-toggle="modal" data-target="#editPartyModal"><i class="fa fa-cogs" aria-hidden="true"></i></button> @endif
                  <h2 class="text-uppercase">{{ $party->name }}</h2>
                  <p id="party_name" class="d-none">{{ $party->name }}</p>
                  <div class="post-meta">
                    <a href="#" class="post-author">CREATED BY {{ $party->user->name }}</a>
                  </div>

                </div>
              </div>

              
              <p class="mt-30"><i>Description: </i>{{ $party->description }}</p>

              <h5>Music Source: {{ $party->source }}</h5>

              <!-- Blockquote -->
              <blockquote class="poca-blockquote d-flex">
                <div class="icon">
                  <i class="fa fa-quote-left" aria-hidden="true"></i>
                </div>
                <div class="text">
                  <h5><b>{{ $party->mood }}</b></h5>
                </div>
              </blockquote>

              <!-- Single Widget Area -->
              <div class="single-widget-area tags-widget mb-80">
                <ul class="tags-list">
                      @foreach($party->genre as $genre)
                      <li><a href="#">{{ $genre->genre }}</a></li>
                      @endforeach
                </ul>
              </div>

              {{-- <!-- Post Catagories 
              <div class="post-catagories d-flex align-items-center">
                <h6>Genres:</h6>
                <ul class="d-flex flex-wrap align-items-center">
                    @foreach($party->genre as $genre)
                    <li><a href="#"><i class="fa fa-play mr-1" aria-hidden="true"></i> {{ $genre->genre }}</a></li>
                    @endforeach
                </ul>
              </div>
                
                <div class="welcome-btn-group">
                    <a href="{{ route('party.edit', [ 'code' => $party->code]) }}" class="btn poca-btn m-2 ml-0 active" data-animation="fadeInUp" data-delay="200ms">Edit Party</a>
                </div> 

                @if(Auth::user()->id == $party->user->id) 
                <button type="button" class="btn poca-btn m-2 ml-0 active" data-toggle="modal" data-target="#editPartyModal">
                  Edit Party
                </button>
                @endif -->
                --}}

                @include('user._shared.player')

            </div>
          </div>
        </div>



        <!-- COLONNA DI DESTRA -->

        <div class="col-12 col-lg-4">
          <div class="sidebar-area mt-5">

            <!-- Single Widget Area -->
            <div class="single-widget-area search-widget-area mb-80">
              <form action="#" method="" autocomplete="off">
                <input id="searchSong" type="search" name="search" class="form-control" placeholder="Search ...">
                <button type="submit"><i class="fa fa-search"></i></button>
              </form>
             
              <div class="d-none">
                <div id="song-prototype" class="list-group-item list-group-item-action flex-column align-items-start p-0">
                  <div class="row align-items-center">
                    <div class="col-sm-3">
                      <img src="https://i.scdn.co/image/ab67616d0000b2731f7077ae1018b5fbab08dfa8" alt="">
                    </div>
                    <div class="col-sm-9">
                      <div class="d-flex w-100 justify-content-between">
                        <h6>Nome canzone</h6>
                        <small class="mr-1">2:23</small> 
                      </div>
                      <div class="d-flex w-100 justify-content-between">
                        <small>Artista</small>
                        <small class="mr-1">album</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div id="result"></div>

              

            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area catagories-widget mb-80">
              <h5 class="widget-title">Participants</h5>

              <ul class="d-none"><li id="partecipant-prototype" class="partecipant"><a href="#"></a></li></ul>

              <!-- catagories list -->
              <ul id="joining-list" class="catagories-list">
              </ul>

              <span id="my_id" class="d-none" data-id="{{Auth::user()->id}}"></span>
            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area news-widget mb-80">
              <h5 class="widget-title">Other Similar Parties</h5>

              <!-- Single News Area -->
              <div class="single-news-area d-flex">
                <div class="blog-thumbnail">
                  <img src="{{ asset('img/bg-img/11.jpg')}}" alt="">
                </div>
                <div class="blog-content">
                  <a href="#" class="post-title">Party Rock: Season Finale</a>
                  <span class="post-date">December 9, 2018</span>
                </div>
              </div>

              <!-- Single News Area -->
              <div class="single-news-area d-flex">
                <div class="blog-thumbnail">
                  <img src="{{ asset('img/bg-img/12.jpg')}}" alt="">
                </div>
                <div class="blog-content">
                  <a href="#" class="post-title">Party Techno: SoundCloud Example</a>
                  <span class="post-date">December 9, 2018</span>
                </div>
              </div>

              <!-- Single News Area -->
              <div class="single-news-area d-flex">
                <div class="blog-thumbnail">
                  <img src="{{ asset('img/bg-img/13.jpg')}}" alt="">
                </div>
                <div class="blog-content">
                  <a href="#" class="post-title">Party Jazz: Best Mics for Podcasting</a>
                  <span class="post-date">December 9, 2018</span>
                </div>
              </div>

            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area adds-widget mb-80">
              <a href="#"><img class="w-100" src="./img/bg-img/banner.png" alt=""></a>
            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area tags-widget mb-80">
              <h5 class="widget-title">Popular Genres</h5>

              <ul class="tags-list">
                    @foreach($genres as $genre)
                    <li><a href="#">{{ $genre->genre }}</a></li>
                    @endforeach
              </ul>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ***** Blog Details Area End ***** -->

  <!-- Button trigger modal -->
  

  <!-- Modal -->
  <div class="modal fade" id="editPartyModal" tabindex="-1" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalTitle">Edit your party's settings!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          
                  
    <div class="container">
        <div class="row justify-content-center">

                <div class="card-body">
                    <div class="contact-form">
                        <div class="contact-heading">
                            <h2>Edit your party</h2>
                            <!-- <h5></h5> -->
                        </div>
                        <form method="POST" action="{{ route('party.update', [ 'code' => $party->code]) }}" id="editPartyForm">
                            @csrf
                            <div class="form-group">
                                <label for="partymood">Party Mood</label>
                                <input type="text" class="form-control" id="partymood" aria-describedby="partymood_help" placeholder="es. 90's, Cartoon Songs" name="mood" required value="{{$party->mood}}"/>
                                <small id="partymood_help" class="form-text text-muted">The Party Mood suggests the party theme</small>
                            </div>
                            <div class="form-group">
                                <label class="description" for="partytype">Party Type</label>
                                <select class="form-control form-control-sm" id="partytype" name="type">
                                    @if($party->type === 'Battle')
                                    <option value="Battle" selected>BATTLE <small>(pick two songs and let users vote for one of them)</small></option>
                                    <option value="Democracy">DEMOCRACY <small>(play the playlist’s most voted song)</small></option>
                                    @else
                                    <option value="Battle">BATTLE <small>(pick two songs and let users vote for one of them)</small></option>
                                    <option value="Democracy" selected>DEMOCRACY <small>(play the playlist’s most voted song)</small></option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="description" for="partygenre">Party Genre <small>(You can choise multiple genres)</small></label>
                                <select class="form-control form-control-sm" id="partygenre" name="genre[]" multiple="multiple">
                                    @foreach($genre_list as $genre)
                                        <option value="{{ $genre->id }}"
                                                    @if ($party->genre->contains($genre))
                                                    selected="selected"
                                                    @endif
                                        >{{ $genre->genre }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="form-group">
                                <label class="description" for="source">Music Source </label>
                                <select class="form-control form-control-sm" id="source" name="source">
                                    <option value="Spotify">Spotify</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="desc">Party Description</label>
                                <textarea class="form-control" rows="5" id="desc" name="desc">{{ $party->description }}</textarea>
                            </div>

                            <div id="forErrors"></div>

                            <button type="submit" class="btn poca-btn">Save Changes</button>
                        </form>

                    </div>

                </div>
        </div>
    </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


  
    <!-- Delete Modal HTML -->
    <div id="deleteSongModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteSongForm" method="post" class="form-horizontal">
                    <div class="modal-header">						
                        <h4 class="modal-title">Delete Song</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">					
                        <p>The song will be deleted from party playlist. Are you sure?</p>
                        <p class="text-warning"><small>This action cannot be undone.</small></p>
                    </div>
                    <div class="modal-footer">
                        <input id="delButton" type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                        <input type="submit" class="btn btn-danger" value="Delete">
                    </div>
                </form>
            </div>
        </div>
    </div> 
  @endisset


@include('user._shared.events.partyEvents')
@endsection

@extends('user.layouts.layout')

@section('content')


<!-- ***** Breadcrumb Area Start ***** -->
<div class="breadcumb-area bg-img bg-overlay"
    style="background-image: url({{ asset('img/bg-img/party-type/democracy.jpg') }});">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
            <h2 class="title mt-70">{{ $party->name }}</h2>
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
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">...</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- ***** Breadcrumb Area End ***** -->


@isset($party)

@if(Auth::user()->id == $party->user->id)
@include('user._shared.invite', ['code' => $party->code ])
@endif

<p id="party_name" class="d-none">{{ $party->name }}</p>
<span class="d-none" data-code="{{$party->code}}" id="party_code"></span>
<span class="d-none" data-code="{{Auth::user()->id}}" id="user_code"></span>

<div class="container party-container mt-2 p-3">

    <div class="row h-100">

        <div class="col-8 h-100">
            <div class="row h-100">

               

                <div class="row h-25">
                     {{-- CARD --}}
                    @include('user._shared.card',['party' => $party])
                </div>


                <div class="col-12 h-75">
                    
                    {{-- PLAYLIST --}}
                    @include('.user._shared.playlist', ['party' => $party, 'liked' => $liked])
                    
                </div>

            </div>
        </div>

        <div class="col-4 h-100">
            {{-- CERCA --}}
            @include('.user._shared.cerca')
            {{-- LISTA PARTECIPANTI --}}
            @include('.user._shared.lista_partecipanti')
            @include('.user._shared.player', ['party' => $party])
            
        </div>


    </div>



</div>
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
                                    <option value="Battle" selected>BATTLE (pick two songs and let users vote for one of them)</option>
                                    <option value="Democracy">DEMOCRACY (play the playlist’s most voted song)</option>
                                    @else
                                    <option value="Battle">BATTLE (pick two songs and let users vote for one of them)</option>
                                    <option value="Democracy" selected>DEMOCRACY (play the playlist’s most voted song)</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="description" for="partygenre">Party Genre (You can choise multiple genres)</label>
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

    {{-- Add songs Modal --}}

    <div class="modal fade" id="addSongsModal" tabindex="-1" role="dialog" aria-labelledby="AddSongsModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalTitle">Select the genere</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
    
    
        <div class="container">
            <div class="row justify-content-center">
    
                    <div class="card-body">
                        <div class="contact-form">
                        
                            <form method="POST" action="{{ route('playlist.populate') }}" id="playlistPopolate" class="form-row">
                                @csrf
                                
                               
                                <div class="form-group col-12">
                                    <label class="description-inline" for="genre">Add 10 songs based on the genre selected</label>
                                    <select class="form-control form-control-sm" id="genre" name="genre_id">
                                        @foreach($genre_list as $genre)
                                            <option value="{{ $genre->id }}"
                                            >{{ $genre->genre }}</option>
                                        @endforeach
                                    </select>
    
                                </div>
                                <input type="hidden" value="{{ $party->code }}" name="party_code">

                                <div id="forErrors"></div>
    
                                <button type="submit" class="btn poca-btn ml-auto mr-auto">Add songs</button>
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
    


@endisset


@include('user._shared.events.partyEvents')
@endsection
@extends('user.layouts.layout')

@section('content')



  <!-- ***** Breadcrumb Area Start ***** -->
  <div class="breadcumb-area bg-img bg-overlay" style="background-image: url({{ asset('img/bg-img/2.jpg') }});">
    <div class="container h-100">
      <div class="row h-100 align-items-center">
        <div class="col-12">
          <h2 class="title mt-70">Create Party</h2>
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
              <li class="breadcrumb-item active" aria-current="page">New Party</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- ***** Breadcrumb Area End ***** -->

    <div class="main-container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                    <div class="card-body">
                        <div class="contact-form">
                            <div class="contact-heading">
                                <h2>Create your party</h2>
                                <h5>Share your music with others!</h5>
                                <small>Ti raccomandiamo di eseguire l'accesso a spotify prima della creazione del party</small>
                            </div>

                            <form method="POST" action="{{ route('party.store') }}">
                            @csrf
                                <div class="form-group">
                                    <label for="partyname">Party Name</label>
                                    <input type="text" class="form-control" id="partyname" aria-describedby="partyname_help" placeholder="es. My Rock Party" name="name" required>
                                    <small id="partyname_help" class="form-text text-muted">The Party Name will be used by your friends to find your party</small>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="partymood" aria-describedby="partymood_help" placeholder="es. 90's, Cartoon Songs" name="mood" required />
                                    <small id="partymood_help" class="form-text text-muted">The Party Mood suggests the party theme</small>
                                </div>
                                <div class="form-group">
                                    <label class="description" for="partytype">Party Type </label>
                                    <select class="form-control form-control-sm" id="partytype" name="type">
                                        <option value="Battle">BATTLE (pick two songs and let users vote for one of them)</option>
                                        <option value="Democracy">DEMOCRACY (play the playlist’s most voted song)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="description" for="partygenre">Party Genre <small>(You can choise multiple genres)</small></label>
                                    <select class="form-control form-control-sm" id="partygenre" name="genre[]" multiple="multiple">
                                      @foreach($genre_list as $genre)
                                        <option value="{{ $genre->genre }}">{{ $genre->genre }}</option>
                                      @endforeach
                                    </select>

                                </div>

                                <div class="form-group">
                                    <label class="description" for="source">Music Source </label>
                                    <select class="form-control form-control-sm" id="source" name="source">
                                        <option>Spotify</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                  <label for="desc">Party Description</label>
                                  <textarea class="form-control" rows="5" id="desc" name="desc"></textarea>
                                </div>

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <button type="submit" class="btn poca-btn">Create</button>
                            </form>



                        </div>

                    </div>
                </div>
        </div>
    </div>


@endsection

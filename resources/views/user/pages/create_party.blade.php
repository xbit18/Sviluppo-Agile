@extends('user.layouts.layout')

@section('content')



  <!-- ***** Breadcrumb Area Start ***** -->
  <div class="breadcumb-area bg-img bg-overlay" style="background-image: url({{ asset('img/bg-img/2.jpg') }});">
    <div class="container h-100">
      <div class="row h-100 align-items-center">
        <div class="col-12">
          <h2 class="title mt-70">Create new Party</h2>
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
                            </div>

                            <form method="POST" action="{{ route('party.store') }}">
                            @csrf
                                <div class="form-group">
                                    <label for="partyname">Party Name</label>
                                    <input type="text" class="form-control" id="partyname" aria-describedby="partyname_help" placeholder="es. My Rock Party" name="name">
                                    <small id="partyname_help" class="form-text text-muted">Party Name will be user by your friends to search your party</small>
                                </div>
                                <div class="form-group">
                                    <label for="partymood">Party Mood</label>
                                    <input type="tet" class="form-control" id="partymood" aria-describedby="partymood_help" placeholder="es. 90's, Cartoon Songs" name="mood">
                                    <small id="partymood_help" class="form-text text-muted">Party mood tell party theme</small>
                                </div>
                                <div class="form-group">
                                    <label class="description" for="partytype">Party Type </label>
                                    <select class="form-control form-control-sm" id="partytype" name="type">
                                        <option value="Battle">BATTLE <small>(pick two songs and let users vote for one of them)</small></option>
                                        <option value="Democracy">DEMOCRACY <small>(play the playlist’s most voted song)</small></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="description" for="partygenre">Party Genre </label>
                                    <select class="form-control form-control-sm" id="partygenre" name="genre">
                                        <option>Rock</option>
                                        <option>Classic</option>
                                        <option>Metal</option>
                                        <option>EDM</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="description" for="source">Party Genre </label>
                                    <select class="form-control form-control-sm" id="source" name="source">
                                        <option>YouTube</option>
                                        <option>Spotify</option>
                                        <option>SoundCloud</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn poca-btn">Create</button>
                            </form>

                        </div>

                    </div>
                </div>
        </div>
    </div>

      
@endsection
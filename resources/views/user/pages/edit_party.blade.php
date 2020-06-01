@extends('user.layouts.layout')

@section('content')



    <!-- ***** Breadcrumb Area Start ***** -->
    <div class="breadcumb-area bg-img bg-overlay" style="background-image: url({{ asset('img/bg-img/2.jpg') }});">
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <div class="col-12">
                    <h2 class="title mt-70">Edit Party</h2>
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
                            <li class="breadcrumb-item active" aria-current="page">Edit Party</li>
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
                            <h2>Edit your party</h2>
                            <h5>Edit your party's settings!</h5>
                        </div>

                        <form method="POST" action="{{ route('party.update', [ 'code' => $party->code]) }}">
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
                                <label class="description" for="partygenre">Party Genre<small>(You can choise multiple genres)</small></label>
                                <select class="form-control form-control-sm" id="partygenre" name="genre[]" multiple="multiple">
                                    @foreach($genre_list as $genre)
                                        <option value="{{ $genre->genre }}"
                                                @foreach($party_genres as $party_genre)
                                                    @if ($genre->genre == old('genre[]', $party_genre->genre))
                                                    selected="selected"
                                                    @endif
                                                @endforeach
                                        >{{ $genre->genre }}</option>
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
                                <textarea class="form-control" rows="5" id="desc" name="desc">{{ $party->description }}</textarea>
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

                            <button type="submit" class="btn poca-btn">Save Changes</button>
                        </form>



                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection

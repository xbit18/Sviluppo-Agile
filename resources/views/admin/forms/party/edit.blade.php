@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Party Update</h1>
        </div>
    </div>
    <hr/>
    <div class="panel panel-default">
        <div class="panel-body">
                        <form method="POST" action="{{ route('admin.party.update') }}">
                            @csrf
                            <input name="id" value="{{$party->id}}" hidden>
                            <div class="form-group">
                                <label for="partyname">Creator Email</label>
                                <input type="text" class="form-control" id="email" aria-describedby="email_help" placeholder="example@example.com" name="email" required value="{{$party->user->email}}">
                                <small id="email_help" class="form-text text-muted">The Party must have a creator</small>
                            </div>
                            <div class="form-group">
                                <label for="partyname">Party Name</label>
                                <input type="text" class="form-control" id="partyname" aria-describedby="partyname_help" placeholder="es. My Rock Party" name="name" required value="{{$party->name}}">
                                <small id="partyname_help" class="form-text text-muted">The Party Name will be used by your friends to find your party</small>
                            </div>
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

                            <div class="form-group" hidden>
                                <label class="description" for="source">Music Source </label>
                                <select class="form-control form-control-sm" id="source" name="source">
                                    <option>Spotify</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="desc">Party Description</label>
                                <textarea class="form-control" rows="5" id="desc" name="desc">{{ $party->description }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="playlist_id">Playlist id</label>
                                <input class="form-control small" rows="5" id="playlist_id" name="playlist_id" placeholder='fill to assign a playlist id' value="{{ $party->playlist_id }}">
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

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>

@endsection

@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Create a new party</h1>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
    <form method="POST" action="{{ route('admin.party.store') }}">
        @csrf
        <div class="form-group" hidden>
            <select class="form-control form-control-sm" id="source" name="source">
                <option>Spotify</option>
            </select>
        </div>
        <div class="form-group">
            <label for="partyname">Creator Email</label>
            <input type="text" class="form-control" id="email" aria-describedby="email_help" placeholder="example@example.com" name="email" required>
            <small id="email_help" class="form-text text-muted">The Party must have a creator</small>
        </div>
        <div class="form-group">
            <label for="partyname">Party Name</label>
            <input type="text" class="form-control" id="partyname" aria-describedby="partyname_help" placeholder="es. My Rock Party" name="name" required>
            <small id="partyname_help" class="form-text text-muted">The Party Name will be used by your friends to find your party</small>
        </div>
        <div class="form-group">
            <label for="partymood">Party Mood</label>
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
                @foreach(\App\Genre::all() as $genre)
                    <option value="{{ $genre->genre }}">{{ $genre->genre }}</option>
                @endforeach
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

        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    <p></p>
        </div>
    </div>

@endsection

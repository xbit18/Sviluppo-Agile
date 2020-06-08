@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Create a vote</h1>
        </div>
    </div>
    <hr/>

    <div class="panel panel-default">
        <div class="panel-body">
            <form method="POST" action="/admin/vote/store">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" placeholder="user email" name="email">
                </div>
                <div class="form-group">
                    <label>Party Code</label>
                    <input class="form-control" placeholder="party code" name="code">
                </div>
                <div class="form-group">
                    <label>Track id</label>
                    <input class="form-control" placeholder="track id" name="track_id">
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
                <button type="submit" class="btn btn-primary">Create now</button>
            </form>
        </div>
    </div>

@endsection

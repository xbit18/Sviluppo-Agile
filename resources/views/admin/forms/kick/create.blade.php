@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Create a Kick</h1>
        </div>
    </div>
    <hr/>

    <div class="panel panel-default">
        <div class="panel-body">
            <form method="POST" action="/admin/kick/store">
                @csrf
                <div class="form-group">
                    <label>User to kick</label>
                    <input class="form-control" placeholder="Email" name="email">
                </div>
                <div class="form-group">
                    <label>Party Code</label>
                    <input class="form-control" placeholder="Party" name="code">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    @php
                    $now = \Carbon\Carbon::now();
                    @endphp
                    <input class="form-control" value="{{$now}}" placeholder="2020-06-06 13:59:35" name="time">
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

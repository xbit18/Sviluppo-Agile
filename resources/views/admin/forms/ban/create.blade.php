@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Create a Ban</h1>
        </div>
    </div>
    <hr/>

    <div class="panel panel-default">
        <div class="panel-body">
            <form method="POST" action="/admin/ban/store">
                @csrf
                <div class="form-group">
                    <label>User</label>
                    <input type="email" class="form-control" placeholder="example@example.com" name="user">
                </div>
                <div class="form-group">
                    <label>Banned</label>
                    <input type="email" class="form-control" placeholder="example@example.com" name="banned">
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

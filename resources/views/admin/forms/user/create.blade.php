@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add a new user</h1>
        </div>
    </div>
    <hr/>

    <div class="panel panel-default">
        <div class="panel-body">
                <form method="POST" action="/admin/user/store">
                    @csrf
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" placeholder="Name" name="name">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" placeholder="Email" name="email">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" placeholder="password" name="password">
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

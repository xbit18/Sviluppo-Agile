@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add a new user</h1>
        </div>
    </div>
    <hr/>
    <div class="panel panel-default">
        <div class="panel-heading">Forms</div>
        <div class="panel-body">
            @if(empty($user))
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
                <button type="submit" class="btn btn-primary">Create now</button>
                </form>
            @else
                <form method="POST" action="/admin/user/update">
                    @csrf
                    <input name="id" value="{{$user->id}}" hidden>
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" value="{{$user->name}}" name="name" >
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" value="{{$user->email}}" name="email" >
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" value="passwordnoncambiata" name="password" >
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    if you don't change your password, it stays the same
                </form>
            @endif
        </div>
    </div>
@endsection

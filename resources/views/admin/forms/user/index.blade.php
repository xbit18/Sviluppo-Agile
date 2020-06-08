@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">All users</h1>
        </div>
    </div>
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ \Session::get('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="panel panel-container" style="background-color: #F1F4F7">
        <div class="row">
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Name
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Email
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                        Parties created
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Join Party
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Leave Party
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-1 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Edit
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-1 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Delete
                </div>
            </div>
        </div>
    </div>
    @foreach($users as $user)
        @php
        $id= $user->id;
        @endphp
    <div class="panel panel-container">
        <div class="row">
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                   {{$user->name}}
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    {{$user->email}}
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    @if($user->parties->first() == null)
                        No party
                    @else
                        <select class="form-control">
                            @foreach($user->parties as $party)
                            <option>{{$party->name}}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right" style="color: green">
                    <form  id='joinparty-form-{{$id}}' action="/admin/user/joinparty" method="POST">
                        @csrf
                        <em onclick="insertFunc{{$id}}()" class="fa fa-xl fa-plus color-green" style="cursor: pointer" ></em>
                        <script>
                            function insertFunc{{$id}}() {
                                var x = prompt('please enter party code');
                                    document.getElementById("party_code{{$id}}").value = x;
                                    document.getElementById('joinparty-form-{{$id}}').submit();
                            }
                        </script>
                        <input name="id" value="{{$user->id}}" hidden >
                        <input name="code" id="party_code{{$id}}" hidden>
                    </form>
                </div>
            </div><div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    @if($user->participates->first() ==null)
                        <p style="color: red">no party</p>
                    @else
                    <form  id='leave-form-{{$id}}' action="/admin/user/leaveparty" method="POST">
                        @csrf
                        <select class="form-control" name="party">
                            @foreach($user->participates as $party)
                                <option value="{{$party->id}}">{{$party->name}}</option>
                            @endforeach
                        </select>
                        <input name="id" value="{{$user->id}}" hidden >
                        <em onclick="leaveFunc{{$id}}()" class="fa fa-xl fa-times color-red" style="cursor: pointer" ></em>

                        <script>
                            function leaveFunc{{$id}}() {
                                var x = confirm('Do you really want to leave this party ?')
                                if (x == true) {
                                    document.getElementById('leave-form-{{$id}}').submit();
                                }
                            }
                        </script>
                        <input name="id" value="{{$user->id}}" hidden >
                        <input name="code" id="party_code{{$id}}" hidden>
                    </form>
                        @endif
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-1 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                      <a href="/admin/user/{{$id}}/edit"> <i class="fa fa-xl fa-edit"></i> </a>
                </div>
            </div>

            <div class="col-xs-6 col-md-3 col-lg-1 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    @if($id == 1)
                        <p style="color: red">
                        can't delete
                        </p>
                    @else
                    <form id="delete-form-{{$id}}"method="POST" action="/admin/user/delete">
                        @csrf
                        <em onclick="deleteFunc{{$id}}()" class="fa fa-xl fa-user-times color-red" style="cursor: pointer" ></em>
                        <script>
                            function deleteFunc{{$id}}() {
                                var x = confirm('Do you really want to delete {{$user->email}} user ?')
                                if(x == true){
                                    document.getElementById('delete-form-{{$id}}').submit(); }
                            }
                        </script>
                        <input name="id" value="{{$user->id}}" hidden >
                    @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

    </div>

    @endforeach

    @endsection

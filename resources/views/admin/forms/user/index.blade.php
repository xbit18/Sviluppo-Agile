@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">All users</h1>
        </div>
    </div>

    <div class="panel panel-container" style="background-color: #F1F4F7">
        <div class="row">
            <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Name
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
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
                    Edit
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
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
            <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                   {{$user->name}}
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
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
                <div class="panel panel-teal panel-widget border-right">
                      <a href="/admin/user/{{$id}}/edit"> <i class="fa fa-xl fa-edit"></i> </a>
                </div>
            </div>

            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    @if($id == 1)
                        <p style="color: red">
                        can't delete
                        </p>
                    @else
                    <form id="delete-form-{{$id}}"method="POST" action="/admin/user/delete">
                        @csrf
                    <a href="/admin/user/delete"
                       onclick="event.preventDefault(); document.getElementById('delete-form-{{$id}}').submit();">
                        <em class="fa fa-xl fa-user-times color-red" ></em> </a>
                        <input name="id" value="{{$user->id}}" hidden >
                    @endif
                    </form>
                </div>
            </div>

    </div>

    @endforeach

    @endsection

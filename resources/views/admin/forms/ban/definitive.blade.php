@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Totally banned users</h1>
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{!! \Session::get('success') !!}</li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="col-lg-12">
            <form  id='form' action="/admin/totalban/store" method="POST">
                @csrf
            <h3 class="page-header" onclick="insertFunc()"  style="color: red; cursor: pointer;" >Ban a user <em class="fa fa-user-times color-red"></em></h3>
                <script>
                    function insertFunc() {
                        var x = prompt('please enter email to ban');
                        document.getElementById("email").value = x;
                        document.getElementById('form').submit();
                    }
                </script>
                <input name="email" id="email" value="" hidden >
            </form>
        </div>
    </div>

    <div class="panel panel-container" style="background-color: #F1F4F7">
        <div class="row">
            <div class="col-xs- col-md-3 col-lg-10 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Banned User
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
        $id=$user->id;
        @endphp
        <div class="panel panel-container">

        <div class="row">
        <div class="col-xs-6 col-md-3 col-lg-10 no-padding">
            <div class="panel panel-teal panel-widget border-right" style="word-wrap: break-word; overflow-wrap: break-word;">{{$user->email}}</div>
        </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    <form id="delete-form-{{$id}}"method="POST" action="/admin/totalban/delete">
                        @csrf
                        <em class="fa fa-xl fa-times color-red" style="cursor: pointer;" onclick="deleteFunc{{$id}}()" ></em>
                        <script>
                            function deleteFunc{{$id}}() {
                                var x = confirm('Do you really want to delete this ban ?')
                                if(x == true){
                                    document.getElementById('delete-form-{{$id}}').submit(); }
                            }
                        </script>
                        <input name="id" value="{{$id}}" hidden >
                    </form>
                </div>
            </div>
        </div>
        </div>
    @endforeach
@endsection

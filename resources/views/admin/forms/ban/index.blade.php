@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Ban users</h1>
        </div>
    </div>
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    <div class="panel panel-container" style="background-color: #F1F4F7">
        <div class="row">
            <div class="col-xs-6 col-md-3 col-lg-4 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    User
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-4 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Banned User
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
    @foreach($bans as $ban)
    <div class="panel panel-container">

            @php
            $user=\App\User::find($ban->user_id);
            $banned=\App\User::find($ban->ban_user_id);
            $id=$ban->id;
            @endphp
        <div class="row">
            <div class="col-xs-6 col-md-3 col-lg-4 no-padding">
                <div class="panel panel-teal panel-widget border-right" style="word-wrap: break-word; overflow-wrap: break-word;">{{$user->email}}</div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-4 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    {{$banned->email}}
                </div>
            </div>

            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    <form  id='edit-form-{{$id}}' action="/admin/ban/update" method="POST">
                        @csrf
                        <em onclick="insertFunc{{$id}}()" class="fa fa-xl fa-edit color-blue" style="cursor: pointer" ></em>
                        <script>
                            function insertFunc{{$id}}() {
                                var x = prompt('please enter email to ban');
                                document.getElementById("banned{{$id}}").value = x;
                                document.getElementById('edit-form-{{$id}}').submit();
                            }
                        </script>

                        <input name="id" value="{{$user->id}}" hidden >
                        <input name="banned" id="banned{{$id}}" hidden>
                        <input name="old_ban" value="{{$banned->id}}" hidden>
                    </form>
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    <form id="delete-form-{{$id}}"method="POST" action="/admin/ban/delete">
                        @csrf
                        <em class="fa fa-xl fa-times color-red" style="cursor: pointer;" onclick="deleteFunc{{$id}}()" ></em>
                        <script>
                            function deleteFunc{{$id}}() {
                                var x = confirm('Do you really want to delete this kick ?')
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

@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">All Kicks</h1>
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
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Party Code
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Kicked User
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Time stamp kick
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    Kick duration
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
    @foreach($kicks as $kick)
        @php
        $id= $kick->id;
        $user=\App\User::where('id',$kick->user_id)->first();
        $party=\App\Party::where('id',$kick->party_id)->first();
    @endphp
    <div class="panel panel-container">
        <div class="row">
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    {{$party->code}}
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right" style="word-wrap: break-word; overflow-wrap: break-word;">{{$user->email}}</div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    {{$kick->timestamp_kick}}
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    {{$kick->kick_duration}}
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    <form  id='edit-form-{{$id}}' action="/admin/kick/update" method="POST">
                        @csrf
                        <em onclick="insertFunc{{$id}}()" class="fa fa-xl fa-edit color-blue" style="cursor: pointer" ></em>
                        <script>
                            function insertFunc{{$id}}() {
                                var x = prompt('please enter new date');
                                document.getElementById("date{{$id}}").value = x;
                                document.getElementById('edit-form-{{$id}}').submit();
                            }
                        </script>

                        <input name="id" value="{{$user->id}}" hidden >
                        <input name="party" value="{{$party->id}}" hidden >
                        <input name="time" id="date{{$id}}" hidden>
                    </form>
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    <form id="delete-form-{{$id}}"method="POST" action="/admin/kick/delete">
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

@extends('admin.app')

@section('section')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">All Votes</h1>
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
            <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    user email
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    party code
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    track id
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                    edit
                </div>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                <div class="panel panel-teal panel-widget border-right">
                   delete
                </div>
            </div>
        </div>
    </div>
    @foreach($votes as $vote)
        @php
            $id= $vote->id;
            $user=\App\User::where('id',$vote->user_id)->first();
            $party=\App\Party::where('id',$vote->party_id)->first();
        @endphp
        <div class="panel panel-container">
            <div class="row">
                <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
                    <div class="panel panel-teal panel-widget border-right" style="word-wrap: break-word; overflow-wrap: break-word;">{{$user->email}}</div>
                </div>
                <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
                    <div class="panel panel-teal panel-widget border-right">
                    {{$party->code}}
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                    <div class="panel panel-teal panel-widget border-right">
                    {{$vote->vote}}
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                    <div class="panel panel-teal panel-widget border-right">
                        <form  id='edit-form-{{$id}}' action="/admin/vote/update" method="POST">
                            @csrf
                            <em onclick="insertFunc{{$id}}()" class="fa fa-xl fa-edit color-blue" style="cursor: pointer" ></em>
                            <script>
                                function insertFunc{{$id}}() {
                                    var x = prompt('please enter track id');
                                    document.getElementById("track_id{{$id}}").value = x;
                                    document.getElementById('edit-form-{{$id}}').submit();
                                }
                            </script>
                            <input name="id" value="{{$id}}" hidden >
                            <input name="track_id" id="track_id{{$id}}" hidden>
                        </form>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 col-lg-2 no-padding">
                    <div class="panel panel-teal panel-widget border-right">
                        <form id="delete-form-{{$id}}"method="POST" action="/admin/vote/delete">
                            @csrf
                            <em class="fa fa-xl fa-times color-red" style="cursor: pointer;" onclick="deleteFunc{{$id}}()" ></em>
                            <script>
                                function deleteFunc{{$id}}() {
                                    var x = confirm('Do you really want to delete this vote ?')
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

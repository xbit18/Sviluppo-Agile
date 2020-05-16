@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

<<<<<<< HEAD
                    You are logged in!
=======
                    You are logged in!<br>
                        Name: {{ $user->name }}<br>
                        Email: {{ $user->email }}
                        <br><br>
                        <div id="form_container">

                            <form method="POST" action="{{ route('createparty') }}">
                                @csrf
                                <div class="form_description">
                                    <h2>Create your party</h2>
                                </div>
                                <ul >
                                    <li id="li_1" >
                                        <label class="description" for="element_1">Party Name </label>
                                        <div>
                                            <input id="element_1" name="name" class="element text medium" type="text" maxlength="255" value=""/>
                                        </div>
                                    </li>

                                    <li id="li_2" >
                                        <label class="description" for="element_2">Party Mood </label>
                                        <div>
                                            <input id="element_2" name="mood" class="element text medium" type="text" maxlength="255" value=""/>
                                        </div>
                                    </li>		<li id="li_4" >
                                        <label class="description" for="element_4">Party Type </label>
                                        <div>
                                            <select class="element select medium" id="element_4" name="type">
                                                <option>Battle</option>
                                                <option>Democracy</option>

                                            </select>
                                        </div>
                                    </li>

                                    <li id="li_3" >
                                        <label class="description" for="element_3">Party Genre </label>
                                        <div>
                                            <select class="element select medium" id="element_3" name="genre">
                                                <option>Rock</option>
                                                <option>Classic</option>
                                                <option>Metal</option>
                                                <option>EDM</option>

                                            </select>
                                        </div>
                                    </li>

                                    <li id="li_5" >
                                        <label class="description" for="element_5">Music Source </label>
                                        <div>
                                            <select class="element select medium" id="element_5" name="source">
                                                <option>Youtube</option>
                                                <option>Spotify</option>
                                                <option>SoundCloud</option>
                                            </select>
                                        </div>
                                    </li>
                                </ul>
                                <input style="margin-top:10px; margin-left: 40px; padding-left: 20px; padding-right: 20px;" id="saveForm" class="button_text" type="submit" value="Confirm"/>
                            </form>
>>>>>>> c7183762243e71ebc5194eb3d951a0c897beec83
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

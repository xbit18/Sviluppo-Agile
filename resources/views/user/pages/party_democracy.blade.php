@extends('user.layouts.layout')

@section('content')


<div class="animated-back-democracy">
    <ul class="squares">
        <li class="square_d"><i class="fa fa-thumbs-up" aria-hidden="true"></i></li>
        <li class="square_d"><i class="fa fa-thumbs-down" aria-hidden="true"></i></li>
        <li class="square_d"><i class="fa fa-users" aria-hidden="true"></i></li>
        <li class="square_d"><i class="fa fa-thumbs-down" aria-hidden="true"></i></li>
        <li class="square_d"><i class="fa fa-thumbs-up" aria-hidden="true"></i></li>
        <li class="square_d"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i></li>
        <li class="square_d"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i></li>
        <li class="square_d"><i class="fa fa-users" aria-hidden="true"></i></li>
        <li class="square_d"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i></li>
        <li class="square_d"><i class="fa fa-thumbs-up" aria-hidden="true"></i></li>
    </ul>
</div>



<!-- ***** Breadcrumb Area Start ***** -->
<div class="breadcumb-area bg-img bg-overlay"
    style="background-image: url({{ asset('img/bg-img/party-type/democracy.jpg') }});">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
            <h2 class="title mt-70">{{ $party->name }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="breadcumb--con">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- ***** Breadcrumb Area End ***** -->


@isset($party)

@if(Auth::user()->id == $party->user->id)
@include('user._shared.invite', ['code' => $party->code ])
@endif

<p id="party_name" class="d-none">{{ $party->name }}</p>
<span class="d-none" data-code="{{$party->code}}" id="party_code"></span>
<span class="d-none" data-code="{{Auth::user()->id}}" id="user_code"></span>

<div class="container party-container mt-2 p-3">

    <div class="row h-100">

        <div class="col-lg-8 col-md-12  h-100">
            <div class="row h-100">

               

                <div class="row h-25">
                     {{-- CARD --}}
                    @include('user._shared.card',['party' => $party])
                </div>


                <div class="col-12 h-75">
                    {{-- CERCA --}}
                    @include('.user._shared.cerca')
                    {{-- PLAYLIST --}}
                    @include('.user._shared.playlist', ['party' => $party, 'liked' => $liked])
                    
                </div>

            </div>
        </div>

        <div class="col-lg-4 col-md-12  h-100">
            {{-- LISTA PARTECIPANTI --}}
            @include('.user._shared.lista_partecipanti')
            @include('.user._shared.player', ['party' => $party])
            
        </div>


    </div>



</div>

<!-- Button trigger modal -->


<!-- Edit Party Modal -->
@include('user._shared.modals.edit_party')



<!-- Delete Modal HTML -->
@include('user._shared.modals.delete_song')


{{-- Add songs Modal --}}

@include('user._shared.modals.add_songs')

<!-- Kick Modal -->

@include('user._shared.modals.kick_user')

<!-- Ban Modal -->

@include('user._shared.modals.ban_user')

@include('user._shared.modals.unban',['party' => $party])

@endisset


@include('user._shared.events.partyEvents')
@endsection
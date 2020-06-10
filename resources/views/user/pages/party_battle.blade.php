@extends('user.layouts.layout',['battle' => true])

@section('content')

<!--
<div class="animated-back">
    <ul class="squares">
        <li class="square"></li>
        <li class="square"></li>
        <li class="square"></li>
        <li class="square"></li>
        <li class="square"></li>
        <li class="square"></li>
        <li class="square"></li>
        <li class="square"></li>
        <li class="square"></li>
        <li class="square"></li>
    </ul>
</div>
-->

<!-- ***** Breadcrumb Area Start ***** -->
<div class="breadcumb-area bg-img bg-overlay" style="background-image: url({{ asset('img/bg-img/party-type/battle1.jpg') }});
            background-position:center;
            ">
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

        <div class="col-lg-8 col-md-12 h-100">
            <div class="row h-100">



                <div class="row h-25">
                    {{-- CARD --}}
                    @include('user._shared.card',['party' => $party])
                </div>


                <div class="col-12 h-75">

                    @include('user._shared.ring')

                </div>

            </div>
        </div>

        <div class="col-lg-4 col-md-12 h-100">
            
            {{-- LISTA PARTECIPANTI --}}
            @include('.user._shared.lista_partecipanti',['battle' => true])

            @if(Auth::user()->id == $party->user->id)
            {{-- CERCA --}}
            @include('.user._shared.cerca')
            @endif

            {{-- PLAYLIST --}}
            @include('.user._shared.playlist', ['party' => $party, 'liked' => $liked,'battle' => true])


        </div>


    </div>

    @include('.user._shared.player', ['party' => $party])

</div>
<!-- Button trigger modal -->


<!-- Edit Party Modal -->
@include('user._shared.modals.edit_party')



<!-- Delete Modal HTML -->
@include('user._shared.modals.delete_song')


{{-- Add songs Modal --}}

@include('user._shared.modals.add_songs')


<!-- Battle Modal -->

@include('user._shared.modals.battle_modal')

<!-- Kick Modal -->

@include('user._shared.modals.kick_user')

<!-- Ban Modal -->

@include('user._shared.modals.ban_user')

@include('user._shared.modals.unban')


@endisset


@include('user._shared.events.partyEvents')
@endsection
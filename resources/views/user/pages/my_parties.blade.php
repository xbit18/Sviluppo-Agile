@extends('user.layouts.layout')

@section('content')


  <!-- ***** Breadcrumb Area Start ***** -->
  <div class="breadcumb-area bg-img bg-overlay" style="background-image: url({{ asset('img/bg-img/2.jpg') }});">
    <div class="container h-100">
      <div class="row h-100 align-items-center">
        <div class="col-12">
          <h2 class="title mt-70">My Parties</h2>
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
              <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">My Parties</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- ***** Breadcrumb Area End ***** -->

@isset($parties)
<div class="container">
      <div class="row poca-portfolio">

         @forelse($parties as $party)
         <!-- Single gallery Item -->
            <div class="col-12 col-md-6 single_gallery_item entre wow fadeInUp" data-wow-delay="0.2s">
            <!-- Welcome Music Area -->
            <div class="poca-music-area style-2 d-flex align-items-center flex-wrap">
                <div class="poca-music-thumbnail">
                <img src="{{ asset('img/bg-img/genres/' . $party->genre_id . '.jpg') }}" alt="">
                </div>
                <div class="poca-music-content text-center">
                <span class="music-published-date mb-2">{{ $party->created_at }}</span>
                <h2><a href="{{ route('party.show', [ 'code' => $party->code]) }}">{{ $party->name }}</a></h2>
                <div class="music-meta-data">
                    <p>By <a href="#" class="music-author">{{ $party->user->name }}</a></p>
                </div>
                <!-- Music Player -->
                <div class="poca-music-player">
                    <audio preload="auto" controls>
                        <source src="audio/dummy-audio.mp3">
                    </audio>
                </div>
                <!-- Likes, Share & Download -->
                <div class="likes-share-download d-flex align-items-center justify-content-between">
                    <a href="#"><i class="fa fa-user" aria-hidden="true"></i> Participants (0)</a>
                    <div>
                    <a href="#" class="mr-4"><i class="fa fa-share-alt" aria-hidden="true"></i> Share</a>
                    </div>
                </div>
                </div>
            </div>
            </div>
         @empty
         <p>No Parties</p>
         @endforelse
        

      </div>
    </div>
@endisset
    

@endsection

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
                <span class="music-published-date mb-2">{{ $party->created_at->format('d/m/Y H:m') }}</span>
                <h2><a class="party_name_list" href="{{ route('party.show', [ 'code' => $party->code]) }}">{{ $party->name }}</a></h2>
                <div class="music-meta-data">
                    <p>By <a href="#" class="music-author">{{ $party->user->name }}</a></p>
                </div>
                <!-- Likes, Share & Download -->
                <div class="likes-share-download d-flex align-items-center justify-content-between">
                    <a href="#"><i class="fa fa-user" aria-hidden="true"></i> Participants ({{ $party->partecipants }})</a>
                    <div>
                    <a href="#" class="mr-4">{{ $party->type }}</a>
                    </div>
                </div>
                </div>
            </div>
            </div>
         @empty
         <!-- Modal -->
          <div class="modal fade autofade" id="noPartiesModal" tabindex="-1" role="dialog" aria-labelledby="noPartiesModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Warning</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                You must have created at least one party to view your party list. <br/>
                <ol class="breadcrumb mt-2">
                  <li class="breadcrumb-item"><i class="fa fa-arrow-right mr-3"></i><i class="fa fa-home"></i> Home</li>
                  <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('party.create') }}">Create</a></li>
                </ol>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-success" data-dismiss="modal">Okay, I Understand</button>
                </div>
              </div>
            </div>
          </div>
         @endforelse
        

      </div>
    </div>
@endisset
    

@endsection

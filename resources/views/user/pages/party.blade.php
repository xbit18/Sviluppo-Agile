@extends('user.layouts.layout')

@section('content')


  <!-- ***** Breadcrumb Area Start ***** -->
  <div class="breadcumb-area bg-img bg-overlay" style="background-image: url({{ asset('img/bg-img/2.jpg') }});">
    <div class="container h-100">
      <div class="row h-100 align-items-center">
        <div class="col-12">
          <h2 class="title mt-70">Party Details</h2>
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
              <li class="breadcrumb-item active" aria-current="page">...</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- ***** Breadcrumb Area End ***** -->


@isset($party)
@include('user._shared.invite', ['code' => $party->code ])

<!-- ***** Blog Details Area Start ***** -->
<section class="blog-details-area">
    <div class="container">
      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="podcast-details-content d-flex mt-5 mb-80">

            <!-- Post Share -->
            <div class="post-share">
              <p>Share</p>
              <div class="social-info">
                <a href="#" class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                <a href="#" class="twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                <a href="#" class="google-plus"><i class="fa fa-google-plus" aria-hidden="true"></i></a>
                <a href="#" class="pinterest"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                <a href="#" class="thumb-tack"><i class="fa fa-thumb-tack" aria-hidden="true"></i></a>
              </div>
            </div>

            <!-- Post Details Text -->
            <div class="post-details-text">
              <img src="{{ asset('img/bg-img/genres/' . $party->genre_id . '.jpg') }}" class="mb-30" alt="">

              <div class="post-content">
                <a href="#" class="post-date">{{ $party->created_at }}</a>
                <h2 class="text-uppercase">{{ $party->name }}</h2>
                <div class="post-meta">
                  <a href="#" class="post-author">CREATED BY {{ $party->user->name }}</a>
                </div>
              </div>

              <p><i>Description: </i>{{ $party->description }}</p>
             
              <h5>Music Source: {{ $party->source }}</h5>

              <!-- Blockquote -->
              <blockquote class="poca-blockquote d-flex">
                <div class="icon">
                  <i class="fa fa-quote-left" aria-hidden="true"></i>
                </div>
                <div class="text">
                  <h5>Mood : <b>{{ $party->mood }}</b></h5>
                </div>
              </blockquote>

              <!-- Post Catagories -->
              <div class="post-catagories d-flex align-items-center">
                <h6>Genres:</h6>
                <ul class="d-flex flex-wrap align-items-center">
                    @foreach($party->genre as $genre)
                    <li><a href="#">{{ $genre->genre }}</a></li>
                    @endforeach
                </ul>
              </div>

            </div>
          </div>
        </div>





        <!-- COLONNA DI DESTRA -->

        <div class="col-12 col-lg-4">
          <div class="sidebar-area mt-5">

            <!-- Single Widget Area -->
            <div class="single-widget-area search-widget-area mb-80">
              <form action="#" method="post">
                <input type="search" name="search" class="form-control" placeholder="Search ...">
                <button type="submit"><i class="fa fa-search"></i></button>
              </form>
            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area catagories-widget mb-80">
              <h5 class="widget-title">Participants</h5>

              <!-- catagories list -->
              <ul class="catagories-list">
                <li><a href="#">User1</a></li>
                <li><a href="#">User2</a></li>
                <li><a href="#">User3</a></li>
                <li><a href="#">Other 12...</a></li>
              </ul>
            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area news-widget mb-80">
              <h5 class="widget-title">Other Similar Parties</h5>

              <!-- Single News Area -->
              <div class="single-news-area d-flex">
                <div class="blog-thumbnail">
                  <img src="{{ asset('img/bg-img/11.jpg')}}" alt="">
                </div>
                <div class="blog-content">
                  <a href="#" class="post-title">Party Rock: Season Finale</a>
                  <span class="post-date">December 9, 2018</span>
                </div>
              </div>

              <!-- Single News Area -->
              <div class="single-news-area d-flex">
                <div class="blog-thumbnail">
                  <img src="{{ asset('img/bg-img/12.jpg')}}" alt="">
                </div>
                <div class="blog-content">
                  <a href="#" class="post-title">Party Techno: SoundCloud Example</a>
                  <span class="post-date">December 9, 2018</span>
                </div>
              </div>

              <!-- Single News Area -->
              <div class="single-news-area d-flex">
                <div class="blog-thumbnail">
                  <img src="{{ asset('img/bg-img/13.jpg')}}" alt="">
                </div>
                <div class="blog-content">
                  <a href="#" class="post-title">Party Jazz: Best Mics for Podcasting</a>
                  <span class="post-date">December 9, 2018</span>
                </div>
              </div>

            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area adds-widget mb-80">
              <a href="#"><img class="w-100" src="./img/bg-img/banner.png" alt=""></a>
            </div>

            <!-- Single Widget Area -->
            <div class="single-widget-area tags-widget mb-80">
              <h5 class="widget-title">Popular Genres</h5>

              <ul class="tags-list">
                    @foreach($genres as $genre)
                    <li><a href="#">{{ $genre->genre }}</a></li>
                    @endforeach
              </ul>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ***** Blog Details Area End ***** -->

  @endisset

@include('user._shared.events.partyEvents')

@endsection

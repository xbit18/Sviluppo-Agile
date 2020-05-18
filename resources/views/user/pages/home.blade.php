@extends('user.layouts.layout')

@section('content')


<!-- ***** Welcome Area Start ***** -->
<section class="welcome-area">
    <!-- Welcome Slides -->
    <div class="welcome-slides owl-carousel">

      <!-- Single Welcome Slide -->
      <div class="welcome-welcome-slide bg-img bg-overlay" style="background-image: url(img/bg-img/1.jpg);">
        <div class="container h-100">
          <div class="row h-100 align-items-center">
            <div class="col-12">
              <!-- Welcome Text -->
              <div class="welcome-text">
                <h2 data-animation="fadeInUp" data-delay="100ms">Bentornato {{ $user->name }}</h2>
                <h5 data-animation="fadeInUp" data-delay="150ms">You are logged in!</h5>
                <div class="welcome-btn-group">
                  <a href="{{ route('party.create') }}" class="btn poca-btn m-2 ml-0 active" data-animation="fadeInUp" data-delay="200ms">Create Party</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
<!-- ***** Welcome Area End ***** -->
  

@endsection

<footer>

  <!-- ***** Newsletter Area Start ***** -->
  <section class="poca-newsletter-area bg-img bg-overlay pt-50 jarallax" style="background-image: url({{ asset('img/bg-img/15.jpg')}});">
    <div class="container">
      <div class="row align-items-center">
      @guest
        <!-- Newsletter Content -->
        <div class="col-12 col-lg-12">
          <div class="newsletter-content mb-50">
            <h2>Sign Up To Website</h2>
            <h6>Start to create and share parties with your friends!</h6>
            <a href="{{ route('register') }}" class="btn poca-btn mt-30">Register </a>
          </div>

        </div>
      @endguest

      @auth
      <!-- Newsletter Content -->
      <div class="col-12 col-lg-12">
          <div class="newsletter-content mb-50">
              @if(\Illuminate\Support\Facades\Auth::user()->ban == 0)
            <h2>Let's go!</h2>
            <h6>Start to create and share parties with your friends!</h6>
                  @else
                  <h2>YOU ARE BANNED!</h2>
                  <h6>Please contact the admin</h6>
              @endif
          </div>
        </div>
      @endauth
      </div>
    </div>
  </section>
  <!-- ***** Newsletter Area End ***** -->







  <!-- ***** Footer Area Start ***** -->
  <div class="footer-area section-padding-80-0">


    <div class="container">
      <div class="row">

        <!-- Single Footer Widget -->
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="single-footer-widget  mb-15 mb-sm-50 mb-md-80 mb-lg-100">
            <!-- Widget Title -->
            <h4 class="widget-title">About Us</h4>

            <p>It is a long established fact that a reader will be distracted by the readable content.</p>
            <div class="copywrite-content">
              <p>&copy;

<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
            </div>
          </div>
        </div>
          <div class="col-12 col-sm-6 col-lg-3">
              <div class="single-footer-widget mb-30 mb-sm-50 mb-md-80 mb-lg-100">
                  <!-- Widget Title -->
                  <h4 class="widget-title">Latest Parties</h4>
                  @php
                      $party_controller = new \App\Http\Controllers\PartyController();
                      $latest_parties = $party_controller->getLatestParties();
                  @endphp
                  <!-- Single Latest Episodes -->
                  @foreach($latest_parties->take(3) as $party)
                      <div class="single-latest-episodes">
                          <p class="episodes-date">{{ $party->created_at->format('d/m/y H:i') }}</p>
                          <a href="/party/show/{{ $party->code }}" class="episodes-title">{{ $party->name }}</a>
                      </div>
                  @endforeach
              </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-3">
              <div class="single-footer-widget mb-30 mb-sm-50 mb-md-80 mb-lg-100">
                  <h4 class="widget-title">             </h4>
                  @foreach($latest_parties->skip(3)->take(3) as $party)
                      <div class="single-latest-episodes">
                          <p class="episodes-date">{{ $party->created_at->format('d/m/y H:i') }}</p>
                          <a href="/party/show/{{ $party->code }}" class="episodes-title">{{ $party->name }}</a>
                      </div>
                  @endforeach
              </div>
          </div>
        <!-- Single Footer Widget -->
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="single-footer-widget mb-80">
            <!-- Widget Title -->
            <h4 class="widget-title">Follow Us</h4>
            <!-- Social Info -->
            <div class="footer-social-info">
              <a href="#" class="facebook" data-toggle="tooltip" data-placement="top" title="Facebook"><i class="fa fa-facebook"></i></a>
              <a href="#" class="twitter" data-toggle="tooltip" data-placement="top" title="Twitter"><i class="fa fa-twitter"></i></a>
              <a href="#" class="pinterest" data-toggle="tooltip" data-placement="top" title="Pinterest"><i class="fa fa-pinterest"></i></a>
              <a href="#" class="instagram" data-toggle="tooltip" data-placement="top" title="Instagram"><i class="fa fa-instagram"></i></a>
              <a href="#" class="youtube" data-toggle="tooltip" data-placement="top" title="YouTube"><i class="fa fa-youtube-play"></i></a>
            </div>
          </div>
        </div>

      </div>
    </div>
</div>
  <!-- ***** Footer Area End ***** -->
</footer>

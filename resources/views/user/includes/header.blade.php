<!-- ***** Header Area Start ***** -->
<header class="header-area">
    <!-- Main Header Start -->
    <div class="main-header-area">
      <div class="classy-nav-container breakpoint-off">
        <!-- Classy Menu -->
        <nav class="classy-navbar justify-content-between" id="pocaNav">

          <!-- Logo -->
          <a class="nav-brand" href="index.html"><img src="{{ asset('img/core-img/logo2.png')}}" alt=""></a>

          <!-- Navbar Toggler -->
          <div class="classy-navbar-toggler">
            <span class="navbarToggler"><span></span><span></span><span></span></span>
          </div>

          <!-- Menu -->
          <div class="classy-menu">

            <!-- Menu Close Button -->
            <div class="classycloseIcon">
              <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
            </div>

            <!-- Nav Start -->
            <div class="classynav">
              <ul id="nav">

                <!-- Generic Links -->
                <li class="{{ Route::currentRouteName() == 'home' ? 'current-item' : '' }}" ><a href="{{ route('home') }}">Home</a></li>


                @auth
                <!-- Auth Links -->
                @if(Auth::user()->email=='static@e.it')
                        <li class="{{ Route::currentRouteName() == 'me.parties.show' ? 'current-item' : '' }}" ><a href="/admin">Admin Panel</a></li>
                    @endif
                    <li class="{{ Route::currentRouteName() == 'me.parties.show' ? 'current-item' : '' }}" ><a href="{{ route('me.parties.show') }}">My Parties</a></li>
                    <li class="{{ Route::currentRouteName() == 'party.create' ? 'current-item' : '' }}" ><a href="{{ route('party.create') }}">Create</a></li>
                    <li class="{{ Route::currentRouteName() == 'parties.index' ? 'current-item' : '' }}" ><a href="{{ route('parties.index') }}">Participate</a></li>
                    <li><a href="#">{{ Auth::user()->name }}</a>
                      <ul class="dropdown">
                        <li>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                              document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>


                          </li>
                      </ul>
                    </li>
                @endauth

                @guest
                <!-- Guest Links -->
                <li class="{{ Route::currentRouteName() == 'login' ? 'current-item' : '' }}" ><a href="{{ route('login') }}">Login</a></li>
                <li class="{{ Route::currentRouteName() == 'register' ? 'current-item' : '' }}" ><a href="{{ route('register') }}">Register</a></li>
                @endguest
              </ul>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
              </form>

              <!--
              Top Search Area - Per il momento non ci serve  -->
              <div class="top-search-area">
                <form action="index.html" method="post">
                  <input type="search" name="top-search-bar" class="form-control" placeholder="Search and hit enter...">
                  <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                </form>
              </div>


              <!--
              Top Social Area - Per il momento non ci serve -->
              <div class="top-social-area">
                <a href="#" class="fa fa-facebook" aria-hidden="true"></a>
                <a href="#" class="fa fa-twitter" aria-hidden="true"></a>
                <a href="#" class="fa fa-pinterest" aria-hidden="true"></a>
                <a href="#" class="fa fa-instagram" aria-hidden="true"></a>
                <a href="#" class="fa fa-youtube-play" aria-hidden="true"></a>
              </div>

            </div>
            <!-- Nav End -->
          </div>
        </nav>
      </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->

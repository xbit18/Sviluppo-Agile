<!DOCTYPE html>
<html lang="en">

<head>
  
  @include('user.includes.head')

</head>

<body>
  
  @include('user.includes.preloader')

  @include('user.includes.header')

  @yield('content')

  @include('user.includes.footer')

  @if(session('spotifyLogIn'))
  <span class="d-none" id="spotifyLogIn">
  
  </span>
  @endif

  @include('user.includes.scripts')

  
  
</body>

</html>
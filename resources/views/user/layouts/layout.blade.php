<!DOCTYPE html>
<html lang="en">

<head>

  @if(isset($battle))
  @include('user.includes.head',['battle' => $battle])
  @else
  @include('user.includes.head')
  @endif
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

  @if(session('spotifyLogOut'))
  <span class="d-none" id="spotifyLogOut">

  </span>
  @endif

  

  @include('user.includes.scripts')



</body>

</html>

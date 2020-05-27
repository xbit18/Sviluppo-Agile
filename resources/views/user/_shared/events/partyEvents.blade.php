
<!-- <script type="text/javascript" src="{{ asset('js/events/party.js')}}" defer></script> -->

@if(Auth::user()->id == $party->user->id)  
  @include('user._shared.spotify_scripts')
@else
  @include('user._shared.spotify_partecipate')
@endif

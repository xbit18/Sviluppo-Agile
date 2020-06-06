
<script src="https://sdk.scdn.co/spotify-player.js" defer></script>
<!-- <script src="{{ asset('js/spotifycontrol.js') }}" defer></script> -->
@if($party->type == 'Battle')
    <script src="{{ asset('js/player/player_host_battle.js') }}" defer></script>
@else
    <script src="{{ asset('js/player/player_host.js') }}" defer></script>
@endif
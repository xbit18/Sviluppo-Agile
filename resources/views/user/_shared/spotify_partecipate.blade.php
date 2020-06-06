<script src="https://sdk.scdn.co/spotify-player.js" defer></script>
<!-- <script src="{{ asset('js/spotifypartecipate.js') }}" defer></script> -->assert

@if($party->type == 'Battle')
    <script src="{{ asset('js/player/player_partecipate_battle.js') }}" defer></script>
@else
    <script src="{{ asset('js/player/player_partecipate.js') }}" defer></script>
@endif
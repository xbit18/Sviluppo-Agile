<div class="modal fade" id="suggestedSongsModal" tabindex="-1" role="dialog" aria-labelledby="suggestedSongs"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title" id="editModalTitle">Suggested songs list</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <div class="container">
                    <div class="row">

                        <div class="card-body">
                            <div id="suggested-songs" class="suggested-songs-list row justify-content-center">

                                @if($party->user->id != Auth::id())
                                <div id="#suggested-song"
                                    class="d-none suggested-song justify-content-around col-12 align-items-center p-2"
                                    data-track-uri="{{Auth::user()->participates()->where('party_id',$party->id)->first()->pivot->suggest_track_uri}}">
                                    <div
                                        class="list-group-item list-group-item-action align-items-start p-0">
                                        <div class="row align-items-center justify-content-around">
                                            <div class="col-2 h-100 p-0 ml-3">
                                                <img src="" alt="">
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6></h6>
                                                    <small class="mr-1"></small>
                                                </div>
                                                <div class="d-flex w-100 justify-content-between">
                                                    <small></small>
                                                    <small class="mr-1"></small>
                                                </div>
                                            </div>
                                            <div class="suggested-delete col-1">
                                                <span>

                                                    <i class="fa fa-times fa-2x" aria-hidden="true"></i>

                                                </span>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else

                                @forelse ($party->users()->where('suggest_track_uri','!=',null)->get() as $user)
                                <div class="suggested-song justify-content-around  col-12 align-items-center p-2"
                                    data-user-id="{{$user->id}}" data-track-uri="{{$user->pivot->suggest_track_uri}}">
                                    <div
                                        class="list-group-item list-group-item-action align-items-start p-0">
                                        <div class="row align-items-center justify-content-around">
                                            <div class="col-2 h-100 p-0 ml-3">
                                                <img src="" alt="">
                                            </div>
                                            <div class="col-8">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6></h6>
                                                    <small class="mr-1"></small>
                                                </div>
                                                <div class="d-flex w-100 justify-content-between">
                                                    <small></small>
                                                    <small class="mr-1"></small>
                                                </div>
                                            </div>
                                            <div class="suggested-delete col-1">
                                                <span>

                                                    <i class="fa fa-times fa-2x" aria-hidden="true"></i>

                                                </span>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty

                                @endforelse

                                @endif
                            </div>
                        </div>
                    </div>


                    <div id="suggested-prototype"
                        class=" d-none justify-content-around col-12 align-items-center p-2">
                        <div class="list-group-item list-group-item-action align-items-start p-0">
                            <div class="row align-items-center justify-content-around">
                                <div class="col-2 h-100 p-0 ml-3">
                                    <img src="" alt="">
                                </div>
                                <div class="col-8">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6></h6>
                                        <small class="mr-1"></small>
                                    </div>
                                    <div class="d-flex w-100 justify-content-between">
                                        <small></small>
                                        <small class="mr-1"></small>
                                    </div>
                                </div>
                                <div class="suggested-delete col-1">
                                    <span class="">
                                        <i class="fa fa-times fa-2x" aria-hidden="true"></i>
                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>
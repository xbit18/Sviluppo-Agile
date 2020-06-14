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
                                    class="d-none suggested-song justify-content-around  col-12 col-lg-8 col-xl-8 align-items-center p-0 p-sm-1 p-md-1 p-lg-1 p-xl-1 mb-1"
                                    data-track-uri="{{Auth::user()->participates()->where('party_id',$party->id)->first()->pivot->suggest_track_uri}}">
                                    <div
                                        class="list-group-item list-group-item-action align-items-start">
                                        <div class="row align-items-center justify-content-around">
                                            <div class="col-2 h-100 p-0">
                                                <img src="" alt="">
                                            </div>
                                            <div class="col-8 pl-0 pl-sm-1 pl-md-1 pl-lg-1 pl-xl-1">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6></h6>
                                                    <small ></small>
                                                </div>
                                                <div class="d-flex w-100 justify-content-between">
                                                    <small></small>
                                                    <small ></small>
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
                                <div class="suggested-song justify-content-around  col-12 col-lg-8 col-xl-8 align-items-center p-0 p-sm-1 p-md-1 p-lg-1 p-xl-1 mb-1"
                                    data-user-id="{{$user->id}}" data-track-uri="{{$user->pivot->suggest_track_uri}}">
                                    <div
                                        class="list-group-item list-group-item-action align-items-start">
                                        <div class="row align-items-center justify-content-around">
                                            <div class="col-2 h-100 p-0">
                                                <img src="" alt="">
                                            </div>
                                            <div class="col-8 pl-0 pl-sm-1 pl-md-1 pl-lg-1 pl-xl-1">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6></h6>
                                                    <small class="ml-2"></small>
                                                </div>
                                                <div class="d-flex w-100 justify-content-between">
                                                    <small></small>
                                                    <small class="ml-2"></small>
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
                        class=" d-none justify-content-around  col-12 col-lg-8 col-xl-8 align-items-center p-0 p-sm-1 p-md-1 p-lg-1 p-xl-1 mb-1">
                        <div class="list-group-item list-group-item-action align-items-start">
                            <div class="row align-items-center justify-content-around">
                                <div class="col-2 h-100 p-0">
                                    <img src="" alt="">
                                </div>
                                <div class="col-8 pl-0 pl-sm-1 pl-md-1 pl-lg-1 pl-xl-1">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6></h6>
                                        <small class="ml-2" ></small>
                                    </div>
                                    <div class="d-flex w-100 justify-content-between">
                                        <small></small>
                                        <small  class="ml-2"></small>
                                    </div>
                                </div>
                                <div class="suggested-delete col-1">
                                    <span >
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
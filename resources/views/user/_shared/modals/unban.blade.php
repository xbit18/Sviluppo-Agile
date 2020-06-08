<div class="modal fade" id="unbanModal" tabindex="-1" role="dialog" aria-labelledby="unbanModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title" id="editModalTitle">Unban an user</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <div class="container">
                    <div class="row">

                        <div class="card-body">
                            <div id="ban-list" class="users">

                                @forelse ($party->user->bans as $user)
                            <div class="user justify-content-center row align-items-center p-2" data-id="{{$user->id}}">
                                    <div class="col-1">
                                        <i class="fa fa-user fa-2x" aria-hidden="true"></i>
                                    </div>
                                    <div class="col-7 ban-name">
                                        <span class="text-uppercase"><strong>{{$user->name}}</strong></span>
                                    </div>
                                    <div class="col-1 mr-2">
                                        <i class="fa fa-check unban" aria-hidden="true"></i>
                                    </div>
                                </div>
                                @empty
                                <div class="row justify-content-center align-items-center" >
                                    <h2>You have not banned any user</h2>
                                </div>

                                @endforelse
                            </div>




                        </div>
                    </div>
                </div>

                <div id="user-prototype" class="user d-none justify-content-center row align-items-center p-2">
                    <div class="col-1">
                        <i class="fa fa-user fa-2x" aria-hidden="true"></i>
                    </div>
                    <div class="col-7 ban-name">
                        <span class="text-uppercase"><strong></strong></span>
                    </div>
                    <div class="col-1 mr-2">
                        <i class="fa fa-check unban" aria-hidden="true"></i>
                    </div>
                </div>
                </div>
            </div>

        </div>
    </div>
</div>
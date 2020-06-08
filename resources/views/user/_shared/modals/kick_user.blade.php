<div class="modal fade" id="kickModal" tabindex="-1" role="dialog" aria-labelledby="kickModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title" id="editModalTitle">Kick an user</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <div class="container">
                    <div class="row justify-content-center">

                        <div class="card-body">
                            <div class="contact-form">

                                <form method="" action="#" id="kick_form"
                                    class="form-row justify-content-around">
                                    @csrf


                                    <div class="form-group col-6 ">
                                        <label class="description-inline" for="kick_duration">Kick duration</label>
                                        <input class="form-control" type="date" name="date" min="{{\Carbon\Carbon::now()->format('Y-m-d')}}" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}" required>

                                    </div>
                                    <div class="form-group col-6 ">
                                        <label class="description-inline" for="hour">Hour</label>
                                        <input class="form-control" type="time" name="hour" required>

                                    </div>
                                    <input type="hidden" value="{{ $party->code }}" name="party_code">

                                    <div id="forErrors"></div>

                                    <button type="submit" class="btn poca-btn ml-auto mr-auto">Kick</button>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addSongsModal" tabindex="-1" role="dialog" aria-labelledby="AddSongsModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Select the genere</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <div class="container">
                    <div class="row justify-content-center">

                        <div class="card-body">
                            <div class="contact-form">

                                <form id="playlistPopolate"
                                    class="form-row">
                                    @csrf


                                    <div class="form-group col-12">
                                        <label class="description-inline" for="genre">Add 10 songs based on the genre
                                            selected</label>
                                        <select class="form-control form-control-sm" id="genre" name="genre_id">
                                            @foreach($genre_list as $genre)
                                            <option value="{{ $genre->id }}">{{ $genre->genre }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <input type="hidden" value="{{ $party->code }}" name="party_code">

                                    <div id="forErrors"></div>

                                    <button type="submit" class="btn poca-btn ml-auto mr-auto">Add songs</button>
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
<div class="modal fade" id="editPartyModal" tabindex="-1" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalTitle">Edit your party's settings!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">


    <div class="container">
        <div class="row justify-content-center">

                <div class="card-body">
                    <div class="contact-form">
                        <div class="contact-heading">
                            <h2>Edit your party</h2>
                            <!-- <h5></h5> -->
                        </div>
                        <form method="POST" action="{{ route('party.update', [ 'code' => $party->code]) }}" id="editPartyForm">
                            @csrf
                            <div class="form-group">
                                <label for="partymood">Party Mood</label>
                                <input type="text" class="form-control" id="partymood" aria-describedby="partymood_help" placeholder="es. 90's, Cartoon Songs" name="mood" required value="{{$party->mood}}"/>
                                <small id="partymood_help" class="form-text text-muted">The Party Mood suggests the party theme</small>
                            </div>
                            <div class="form-group">
                                <label class="description" for="partytype">Party Type</label>
                                <select class="form-control form-control-sm" id="partytype" name="type">
                                    @if($party->type === 'Battle')
                                    <option value="Battle" selected>BATTLE (pick two songs and let users vote for one of them)</option>
                                    <option value="Democracy">DEMOCRACY (play the playlist’s most voted song)</option>
                                    @else
                                    <option value="Battle">BATTLE (pick two songs and let users vote for one of them)</option>
                                    <option value="Democracy" selected>DEMOCRACY (play the playlist’s most voted song)</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="description" for="partygenre">Party Genre (You can choise multiple genres)</label>
                                <select class="form-control form-control-sm" id="partygenre" name="genre[]" multiple="multiple">
                                    @foreach($genre_list as $genre)
                                        <option value="{{ $genre->id }}"
                                                    @if ($party->genre->contains($genre))
                                                    selected="selected"
                                                    @endif
                                        >{{ $genre->genre }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="form-group">
                                <label class="description" for="source">Music Source </label>
                                <select class="form-control form-control-sm" id="source" name="source">
                                    <option value="Spotify">Spotify</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="desc">Party Description</label>
                                <textarea class="form-control" rows="5" id="desc" name="desc">{{ $party->description }}</textarea>
                            </div>

                            <div id="forErrors"></div>

                            <button type="submit" class="btn poca-btn">Save Changes</button>
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
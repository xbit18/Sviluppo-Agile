
    <div class="col-12 h-100">
        <div  id="party-card" class="card h-100">
            <div class="row">
                <div class="col-4 align-self-center">

                    <img id="party_img_genre "
                        src="{{ asset('img/bg-img/genres/' . $party->genre_id . '.jpg') }}"
                        class="card-img-left ">
                </div>

                <div class="col-8">


                    <div class="card-header row text-center align-items-center bg-white pb-0">
                        <div class="col-10">
                            <h4>{{ $party->name }}</h4>
                        </div>

                        <div class="col-2">
                            @if(Auth::user()->id == $party->user->id) <button type="button"
                                class="btn poca-btn setting-button mb-2" data-toggle="modal"
                                data-target="#editPartyModal"><i class="fa fa-cogs"
                                    aria-hidden="true"></i></button> @endif

                        </div>


                    </div>



                    <div class="card-body pt-2">

                        <div class="col-12">
                            <span>
                                <strong>Mood:</strong>
                            </span>
                            <span>{{ $party->mood }}</span>
                        </div>
                        <div class="col-12">
                            <span>
                                <strong>Type:</strong>
                            </span>
                            <span>{{ $party->type }}</span>
                        </div>
                        <div class="col-12">
                            <span>
                                <strong>Description:</strong>
                            </span>
                            <span>{{ $party->description }}</span>
                        </div>
                        <div class="post-meta col-12">
                                <span>
                                    <strong>Created By: </strong>
                                </span>
                                <span>{{ $party->user->name }}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="tags-list">
                            @foreach($party->genre as $genre)
                            <li class="genre"><a href="#">{{ $genre->genre }}</a></li>
                            @endforeach
                      </ul>
                    </div>
                </div>
            </div>


        </div>
    </div>

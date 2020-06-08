@if(!isset($battle))

{{-- ------------------ DEMOCRACY --------------------------- --}}

<!-- Single Widget Area -->
<div class="single-widget-area catagories-widget participants_list">
    <h5 class="widget-title text-center">Participants</h5>

    <ul class="d-none">
        <li id="partecipant-prototype" class="partecipant">
            <a href="#" class="h-100 w-100">
                <div class="row justify-content-center align-items-center h-100">
                    <div class="col-1">
                        <i class="fa fa-user" aria-hidden="true"></i>
                    </div>
                    <div class="col-8 name">
                        
                    </div>

                    @if(Auth::id() == $party->user->id)
                    <div class="col-1">
                        <i class="fa fa-times kick" aria-hidden="true"></i>
                    </div>
                    
                    <div class="col-1 mr-2">
                        <i class="fa fa-ban ban" aria-hidden="true"></i>
                    </div>
                    @endif
                </div>
            </a>
        </li>
    </ul>

    <!-- catagories list -->
    <div class="row">
        <ul id="joining-list" class="catagories-list col-12 ">
        </ul>
    </div>
    

    <span id="my_id" class="d-none" data-id="{{Auth::user()->id}}"></span>
</div>

@else

{{-- -------------------------------- BATTLE ------------------------------- --}}

<!-- Single Widget Area -->
<div class="single-widget-area catagories-widget participants_list">
    <h5 class="widget-title text-center">Participants</h5>

    <ul class="d-none">
        <li id="partecipant-prototype" class="partecipant">
            <a href="#" class="h-100 w-100">
                <div class="row justify-content-center align-items-center h-100">
                    <div class="col-1">
                        <i class="fa fa-user" aria-hidden="true"></i>
                    </div>
                    <div class="col-8 name">
                        
                    </div>
                    @if(Auth::id() == $party->user->id)
                    <div class="col-1">
                        <i class="fa fa-times kick" aria-hidden="true"></i>
                    </div>
                    
                    <div class="col-1 mr-2">
                        <i class="fa fa-ban ban" aria-hidden="true"></i>
                    </div>
                    @endif
                </div>
            </a>
        </li>
    </ul>

    <!-- catagories list -->
    <div class="row">
        <ul id="joining-list" class="catagories-list col-12 ">
        </ul>
    </div>
    

    <span id="my_id" class="d-none" data-id="{{Auth::user()->id}}"></span>
</div>

@endif
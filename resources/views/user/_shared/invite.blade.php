<!-- ***** Invite Area Start ***** -->
<section class="poca-newsletter-area bg-img bg-overlay pt-50 jarallax mt-2" style="background-image: url({{ asset('img/bg-img/invite_bg.jpg')}});">
    <div class="container">
      <div class="row align-items-center">
        <!-- Invite Content -->
        <div class="col-md-12 col-lg-6">
          <div class="newsletter-content mb-50">
            <h2>Invite People</h2>
            <h6>Search by email and invite</h6>
            <button id="reset_invite" type="button" class="btn btn-default mt-2">Reset</button>
          </div>
        </div>
        <!-- Invite Form -->
        <div class="col-md-12 col-lg-6">
            <div class="newsletter-form mb-50">
                <form id="add_people_to_list" action="#" method="post">
                    <input id="invite_field" type="email" name="nl-email" class="form-control email" placeholder="Email">
                    <button type="submit" class="btn"><i class="fa fa-search"></i> Search</button>
                </form>
            </div>
            <div class="newsletter-form mb-50">
                <form action="{{ route('party.invite', ['code' => $code]) }}" method="post">
                    @csrf
                    <input readonly="readonly" id="invite_list" type="email" name="invite_list" class="form-control email">
                    <button id="invite_btn" type="submit" class="btn" disabled="disabled">Invite People</button>
                </form>
            </div>
            
        </div>
      </div>
    </div>
</section>
  <!-- ***** Invite Area End ***** -->
$( document ).ready( function() {

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    if($('#spotifyLogIn').length){
        Toast.fire({
            type: 'success',
            title: 'The Spotify Token has been refreshed'
            });
    }

    $('#add_people_to_list').on('submit', function(event) {
        event.preventDefault();

        var email = $( "#invite_field" ).val();

        /**
         * Controlli sulla validità dell'input
         */
        if (!email) {
            return;
        }
        if ($( "#invite_list" ).val() && $( "#invite_list" ).val().indexOf(email) >= 0) {
            Toast.fire({
                type: 'warning',
                title: 'The user is already present in the list'
                });
            return;
        }

        /**
         * Faccio la richiesta per sapere il nome dell'utente
         */
        $.ajax({
            url: "/users/" + email + "/nome",
            dataType: 'json',
            success: function(data){
                // DEBUGGING
                //console.log(data);
                
                // Setto i campi interessati con il nome dell'utente
                if($('#invite_list').val()) $('#invite_list').val( $('#invite_list').val() + ",(" + data.nome + " - " + email + ")" );
                else $('#invite_list').val( "(" + data.nome + " - " + email + ")" );
                
                /**
                 * Abilito il pulsante invita e setto a vuoto
                 * il campo email.
                 */
                $('#invite_btn').attr('disabled', false);
                $( "#invite_field" ).val("");
            },
            error:function (xhr, ajaxOptions, thrownError){ 
                /**
                 * Error Handling
                 */
                if(xhr.status == 404) {
                    console.log("404 NOT FOUND");
                    Toast.fire({
                        type: 'error',
                        title: 'User Not Found'
                        });
                }else if(xhr.status == 500) {
                    console.log("500 INTERNAL SERVER ERROR");
                }
            }
        });

        return;
    });

    $('#reset_invite').on('click', function(){
        $( "#invite_field" ).val("");
        $( "#invite_list" ).val("");
        $('#invite_btn').attr('disabled', true);
    });


    if( $('.autofade').length ) {
        $('.autofade').modal('show');
    }

});


$( document ).ready( function() {

   

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 6000
    });

    if($('#spotifyLogIn').length){
        Toast.fire({
            type: 'success',
            title: 'The Spotify Token has been refreshed'
            });
    }

    if($('#spotifyLogOut').length){
        Toast.fire({
            type: 'success',
            title: 'The Spotify Token has been deleted'
        });
    }

    if($('#kicked').length){
        Toast.fire({
            type: 'warning',
            title: 'You have been kicked from this party'
        });
    }

    if($('#banned').length){
        Toast.fire({
            type: 'warning',
            title: 'This user has banned you permanently'
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

    $(document).on('click','.delete_party',function(event){
        
        let id = $(this).data('id');
        let parent = $(this).parents('.single_gallery_item');
        $('#party_deleteModal').modal();

        $('#party_delete_form').on('submit',function(event){
            event.preventDefault()
            
            $.ajax({
                type: "DELETE",
                url: `/party/${id}/delete`,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function (response) {

                    $('#party_deleteModal').modal('hide');
                    if (response.error) {
                        Toast.fire({
                            type: 'warning',
                            title: response.error
                        })

                    } else {

                        parent.fadeOut(300,function(){
                            parent.remove()
                        })
                        Toast.fire({
                            type: 'success',
                            title: 'Party deleted'
                        })

                    }
                },
                error: function (error) {

                    console.log(error);
                }
            });
        })
    })

});


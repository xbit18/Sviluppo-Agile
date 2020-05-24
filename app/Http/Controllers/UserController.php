<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UserController extends Controller
{
    /**
     * Restituisce il nome di un utente data la sua email. 
     * Se l'utente non è presente sul db viene mostrata la schemata 404.
     */
    public function get_name_by_email($email) {

        /**
         * Se la richiesta non viene fatta di tipo json restituisco un errore
         */
        if(request()->ajax())
        {
            $user = User::where('email', '=', $email)->first();
            if($user) {
                return response()->json([
                    'nome' => $user->name,
                ]);
            }
            
            abort(404);
        }

        abort(500);

    }
}

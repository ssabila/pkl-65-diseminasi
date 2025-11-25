<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Redirect setelah login sukses.
     */
    public function toResponse($request)
    {
        // Kalau ada intended url (misal dari middleware auth) pakai itu,
        // kalau tidak ada, fallback ke /dashboard
        return redirect()->intended('/dashboard');
    }
}

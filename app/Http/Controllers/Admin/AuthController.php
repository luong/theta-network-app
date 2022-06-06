<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function login()
    {
        if (request()->isMethod('post')) {
            if (!(auth('admin')->attempt(request()->only('username', 'password')))) {
                throw ValidationException::withMessages([
                    'email' => 'Username or password is invalid',
                ]);
            }
            session()->regenerate();
            return redirect()->intended(route('admin.home'));
        }
        return view('admin.auth.login');
    }

    public function logout()
    {
        auth()->guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('admin.login');
    }

}

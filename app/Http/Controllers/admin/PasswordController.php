<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PasswordController extends Controller
{
    public function check(Request $request)
    {

        // Check if the input password matches the set password
        if ($request->input('password') === config('app.password_admin')) { // Replace 'yourPassword' with the actual password
            // Set a session variable to indicate the user is authenticated for the password protected route

            Session::put('isPasswordProtectedRouteAuthenticated', true);
            // Redirect to the originally requested page

            return redirect()->intended(Session::get('adminUri'));
        }

        // If password is incorrect, redirect back with an error message
        return back()->withErrors(['password' => 'The password is incorrect']);
    }
}

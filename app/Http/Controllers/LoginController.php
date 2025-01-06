<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // @desc  Show login form
    // @route GET /login
    public function login(): View
    {
        return view('auth.login');
    }
    //@desc Authenticate user
    //@route POST/login
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate(
            [
                // 'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:100',
                'password' => 'required|string',
            ]
        );
        // dd($credentials); cu dd se afiseaza fara a se salva ceea ce este in paranteza

        //Atempt(incercare de autorizare) to authenticate user  
        if (Auth::attempt($credentials)) {
            //Regenerate the session to prevent fixation attacks
            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'Yo are now logged in!');
        }
        // if auth fails, redirect back with error
        return back()->withErrors(['email' => 'the provided credentials do not match our records'])->onlyInput('email');
    }
    //@desc logout 
    //route POST/logout
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout(); // Log out the user

        $request->session()->invalidate(); // Invalidate the session
        $request->session()->regenerateToken(); // Regenerate the CSRF token

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    // @desc  Show register form
    // @route GET /register
    // se citeste cam asa: 
    // "Metoda register este folosită pentru a returna vederea auth/register din directorul resources/views, care reprezintă pagina de înregistrare."
    public function register(): View
    {
        return view('auth.register');
    }
    //@desc store user and database
    //@route POST/register
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]
        );
        //hash password adica sa criptezi parola
        $validatedData['password'] = Hash::make($validatedData['password']);
        //create user
        $user = User::create($validatedData);
        return redirect()->route('login')->with('success', 'You are registered and can login');
    }
}

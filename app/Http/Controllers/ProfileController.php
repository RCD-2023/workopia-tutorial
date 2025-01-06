<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    //@desc update profile info
    //route PUT/profile
    public function update(Request $request): RedirectResponse
    {
        //get logged usser
        $user = Auth::user();
        //Validate data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => "nullable|image|mimes:jpeg,jpg,png,gif|max:2048"
        ]);
        //Get user name and email
        $user->name = $request->input("name");
        $user->email = $request->input("email");
        /**
         * Explcatii de scriere a sintaxei de mai sus cu folosirea operatorului '='
         * Acesta ia valoarea obtinuta in partea dreapta si o atribuie 
         * in partea stanga
         */
        //Handle avatar upload
        if ($request->hasFile("avatar")) {
            //delete old avatar if exists
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }
            //Store new avatar
            $avatarPath = $request->file(key: 'avatar')->store(path: 'avatars', options: 'public');
            $user->avatar = $avatarPath;
        }
        //update user's info
        $user->save();
        // dd($user);
        return redirect()->route('dashboard')->with('success', 'Profile info updated');
    }
}

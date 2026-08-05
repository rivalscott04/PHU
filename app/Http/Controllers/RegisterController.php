<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $attributes = ValidationHelper::validate($request, [
            'username' => 'required|max:255|min:2',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:5|max:255',
            'terms' => 'required',
        ]);

        $user = User::create($attributes);
        auth()->login($user);

        return redirect('/dashboard');
    }
}

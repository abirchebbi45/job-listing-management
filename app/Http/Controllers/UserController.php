<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Show Register/Create Form
    public function create() {
        return view('users.register');
    }

    // Create New User Account
    public function store(Request $request) {
        $formField = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:6'
        ]);

        // Hash password
        $formField['password'] = bcrypt($formField['password']);

        $user = User::create($formField);

        // Login
        auth()->guard()->login($user);;

        return redirect('/')->with('message','User created and logged successfully!');
    }

    // logout user
    public function logout(Request $request) {
        auth()->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message','You have been logged out');
    }

    // Show login Form
    public function login() {
        return view('users.login');
    }

    // Authenticate User
    public function authenticate(Request $request) {
        $formField = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        if(auth()->guard()->attempt($formField)) {
            $request->session()->regenerate();
            return redirect('/')->with('message','You are logged in successfully!');
        }
        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
{
    return view('admin.auth.login'); 
}

    public function authenticate(Request $request)
{
    // Validasi input dari form (input name di view tetap 'email' agar tidak bingung)
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // Mapping input form ke nama kolom database asli
    $credentials = [
        'user_email' => $request->email, // Mencocokkan ke kolom user_email
        'password'   => $request->password, // Laravel akan otomatis mengecek ke user_pass via getAuthPassword()
    ];

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended(route('posts.index'));
    }

    return back()->withErrors([
        'email' => 'Kredensial tidak cocok dengan data kami.',
    ])->onlyInput('email');
}
}

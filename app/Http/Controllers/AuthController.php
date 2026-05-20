<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login'); 
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('user_email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
            'email' => 'Email tidak ditemukan.',
            ])->onlyInput('email');
        }

         if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->user_pass)) {
        // Fallback: cek MD5 (WordPress lama)
        if (md5($request->password) !== $user->user_pass) {
            return back()->withErrors([
                'email' => 'Kredensial tidak cocok dengan data kami.',
            ])->onlyInput('email');
        }
        }

        \Illuminate\Support\Facades\Auth::login($user);
        $request->session()->regenerate();

         return redirect()->intended(route('home.home'));

    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}


//         $credentials = [
//             'user_email' => $request->email, 
//             'password'   => $request->password, 
//         ];

//         if (Auth::attempt($credentials)) {
//             $request->session()->regenerate();
//             return redirect()->intended(route('home.home'));
//         }

//         return back()->withErrors([
//             'email' => 'Kredensial tidak cocok dengan data kami.',
//         ])->onlyInput('email');
//     }

    // public function logout(Request $request)
    // {
    //     Auth::logout();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return redirect()->route('login');
    // }
// }
<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('dosen')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }


    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'pin' => 'required',
        ]);

        $nik = $request->nik;
        $pin = md5($request->pin);

        $dosen = Dosen::where('nik', $nik)->where('pin', $pin)->first();

        if ($dosen) {
            session(['dosen' => $dosen]);
            return redirect()->route('dashboard');
        } else {
            return back()->withErrors(['login' => 'NIK atau PIN salah!']);
        }
    }

    public function logout()
    {
        session()->forget('dosen');
        return redirect()->route('login');
    }
}

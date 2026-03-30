<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::with(['roleUsers' => function ($q) {
            $q->where('status', 1);
        }, 'roleUsers.role'])
        ->where('email', $request->email)
        ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah'])->withInput();
        }

        $activeRole = $user->roleUsers->first();
        $idrole = $activeRole->idrole ?? null;
        $nama_role = $activeRole->role->nama_role ?? 'User';

        // Login user ke sistem
        Auth::login($user);

        // Simpan data ke session (kalau nanti butuh)
        $request->session()->put([
            'iduser' => $user->iduser,
            'nama' => $user->nama,
            'email' => $user->email,
            'idrole' => $idrole,
            'role' => $nama_role,
        ]);

        // Redirect sesuai role
        switch ($idrole) {
            case 1:
                return redirect()->route('Admin.dashboard-admin')->with('success', 'Login berhasil sebagai Admin');
            case 2:
                return redirect()->route('Dokter.dashboard-dokter')->with('success', 'Login berhasil sebagai Dokter');
            case 3:
                return redirect()->route('Perawat.dashboard-perawat')->with('success', 'Login berhasil sebagai Perawat');
            case 4:
                return redirect()->route('Resepsionis.dashboard-resepsionis')->with('success', 'Login berhasil sebagai Resepsionis');
            case 5:
                return redirect()->route('Pemilik.dashboard-pemilik')->with('success', 'Login berhasil sebagai Pemilik');
            default:
                Auth::logout();
                return redirect()->route('login')->withErrors(['role' => 'Role tidak dikenali'])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Logout berhasil');
    }
}


//cihuy
<?php

namespace App\Http\Controllers\Perawat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProfilPerawat_Controller extends Controller
{
    public function profil_saya()
    {
        $user = DB::table('user')
            ->where('iduser', auth()->id())
            ->first();

        $perawat = DB::table('perawat')
            ->where('iduser', auth()->id())
            ->whereNull('deleted_at')
            ->first();

        if (!$user || !$perawat) {
            abort(404);
        }

        return view('Perawat.Profil.profil-saya', [
            'user' => $user,
            'perawat' => $perawat,
        ]);
    }
}
//cihuy
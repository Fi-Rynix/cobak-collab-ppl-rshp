<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainSite_Controller extends Controller
{
    public function rshp() {
        return view('MainSite.rshp');
    }

    public function struktur_organisasi() {
        return view('MainSite.struktur-organisasi');
    }

    public function layanan_umum() {
        return view('MainSite.layanan-umum');
    }

    // public function login() {
    //     return view('MainSite.login');
    // }

    public function cek_koneksi() {
        try {
            \DB::connection()->getPdo();
            return 'udah konek';
        } catch (\Exception $e) {
            return 'belum konek' . $e->getMessage();
        }
    }
}


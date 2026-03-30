<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardPemilik_Controller extends Controller
{
    public function dashboard_pemilik()
    {
        return view('Pemilik.dashboard-pemilik');
    }
}
//cihuy
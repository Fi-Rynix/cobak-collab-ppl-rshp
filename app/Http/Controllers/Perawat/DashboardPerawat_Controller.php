<?php

namespace App\Http\Controllers\Perawat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardPerawat_Controller extends Controller
{
    public function dashboard_perawat()
    {
        return view('Perawat.dashboard-perawat');
    }
}
//cihuy
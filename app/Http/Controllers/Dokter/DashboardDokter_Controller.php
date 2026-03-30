<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardDokter_Controller extends Controller
{
    public function dashboard_dokter()
    {
        return view('Dokter.dashboard-dokter');
    }
}
//cihuy
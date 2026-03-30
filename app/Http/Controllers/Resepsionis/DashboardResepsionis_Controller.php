<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardResepsionis_Controller extends Controller
{
    public function dashboard_resepsionis()
    {
        return view('Resepsionis.dashboard-resepsionis');
    }
}

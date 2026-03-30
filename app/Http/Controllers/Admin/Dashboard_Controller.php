<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Dashboard_Controller extends Controller
{
    public function dashboard_admin() {
        return view('Admin.dashboard-admin');
    }
}
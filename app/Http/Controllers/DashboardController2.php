<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController2 extends Controller
{
    public function showEntry2()
    {
        // Just return the Blade view
        return view('reactdashboard.sales.entry2');
    }
}
<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik dashboard ditangani oleh komponen Livewire (DashboardStats) agar real-time.
        return view('admin.dashboard');
    }
}

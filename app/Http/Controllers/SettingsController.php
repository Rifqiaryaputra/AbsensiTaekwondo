<?php

namespace App\Http\Controllers;

class SettingsController extends Controller
{
    public function index()
    {
        // Form pengaturan profil ditangani oleh komponen Livewire (SettingsProfil).
        return view('admin.settings');
    }
}

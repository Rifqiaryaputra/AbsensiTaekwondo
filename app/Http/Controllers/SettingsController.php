<?php

namespace App\Http\Controllers;

class SettingsController extends Controller
{
    public function index()
    {
        // TODO Fase 4: data pengaturan profil diambil dari tabel singleton pengaturan_profil.
        $profil = [
            'nama_unit' => 'UKM Taekwondo',
            'nama_universitas' => 'Universitas Ahmad Dahlan (UAD)',
            'alamat' => 'Gedung Student Center Lt. 1, Kampus Pusat, Jl. Ringroad Selatan, Yogyakarta',
            'logo_unit' => null,
            'logo_universitas' => null,
        ];

        return view('admin.settings', compact('profil'));
    }
}

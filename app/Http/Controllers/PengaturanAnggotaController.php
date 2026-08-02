<?php

namespace App\Http\Controllers;

class PengaturanAnggotaController extends Controller
{
    public function index()
    {
        // TODO Fase 4: proses ubah password dihubungkan ke autentikasi & validasi password.
        return view('anggota.pengaturan');
    }
}

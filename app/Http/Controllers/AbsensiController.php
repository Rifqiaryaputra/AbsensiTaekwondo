<?php

namespace App\Http\Controllers;

class AbsensiController extends Controller
{
    public function index()
    {
        // TODO Fase 4: logika jadwal aktif & daftar absensi berasal dari database.
        return view('petugas.absensi');
    }
}

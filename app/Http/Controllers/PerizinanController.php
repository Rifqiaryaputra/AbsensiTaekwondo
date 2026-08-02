<?php

namespace App\Http\Controllers;

class PerizinanController extends Controller
{
    public function index()
    {
        // TODO Fase 4: data pengajuan diambil dari database (Livewire KelolaPerizinan).
        return view('admin.perizinan.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\DatabaseAnggotaExport;
use Maatwebsite\Excel\Facades\Excel;

class AnggotaController extends Controller
{
    public function index()
    {
        // TODO Fase 4: data anggota diambil dari database (Livewire DaftarAnggota).
        return view('admin.anggota.index');
    }

    public function export()
    {
        return Excel::download(
            new DatabaseAnggotaExport(
                (string) request()->query('search', ''),
                (string) request()->query('fakultas', ''),
                (string) request()->query('prodi', '')
            ),
            'Database_Anggota_UKM.xlsx'
        );
    }
}

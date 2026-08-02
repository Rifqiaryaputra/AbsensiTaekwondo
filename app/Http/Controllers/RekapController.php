<?php

namespace App\Http\Controllers;

use App\Exports\RekapKehadiranExport;
use Maatwebsite\Excel\Facades\Excel;

class RekapController extends Controller
{
    public function index()
    {
        return view('admin.rekap');
    }

    public function export()
    {
        $start = request()->query('start');
        $end = request()->query('end');

        if (! $start || ! $end || $start > $end) {
            abort(422, 'Rentang tanggal tidak valid.');
        }

        return Excel::download(
            new RekapKehadiranExport($start, $end),
            'Rekap_Absensi_'.$start.'_sd_'.$end.'.xlsx'
        );
    }
}

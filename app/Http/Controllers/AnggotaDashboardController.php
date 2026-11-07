<?php

namespace App\Http\Controllers;

use App\Services\AnggotaService;
use Illuminate\Support\Facades\File;

class AnggotaDashboardController extends Controller
{
    public function index()
    {
        return view('anggota.dashboard');
    }

    /**
     * Unduh QR Code keanggotaan member yang login (hanya QR, tanpa kartu).
     */
    public function downloadQR()
    {
        $anggota = auth()->user()?->anggota;

        abort_unless($anggota, 404);

        if (! $anggota->qr_code || ! File::exists(public_path($anggota->qr_code))) {
            $anggota->qr_code = app(AnggotaService::class)->generateQrCode($anggota->id_anggota);
            $anggota->save();
        }

        return response()->download(public_path($anggota->qr_code), 'QR_'.$anggota->id_anggota.'.svg');
    }
}

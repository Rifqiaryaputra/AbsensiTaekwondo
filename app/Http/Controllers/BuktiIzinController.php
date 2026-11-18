<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BuktiIzinController extends Controller
{
    /**
     * Sajikan file bukti izin dari storage Laravel atau folder publik lama.
     *
     * Dipakai sebagai fallback ketika symlink public/storage tidak tersedia
     * atau tidak sinkron di shared hosting.
     */
    public function show(string $filename): BinaryFileResponse
    {
        $filename = basename(str_replace('\\', '/', $filename));

        abort_if($filename === '' || $filename === '.' || $filename === '..', 404);
        abort_if(str_contains($filename, "\0"), 404);

        $candidates = [
            Storage::disk('public')->path('bukti-izin/'.$filename),
            public_path('bukti-izin/'.$filename),
            public_path('storage/bukti-izin/'.$filename),
        ];

        foreach ($candidates as $path) {
            if (! File::isFile($path)) {
                continue;
            }

            return response()->file($path, [
                'Content-Type' => $this->mimeType($path, $filename),
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        abort(404, 'File bukti izin tidak ditemukan.');
    }

    private function mimeType(string $path, string $filename): string
    {
        $mime = File::mimeType($path);

        if (is_string($mime) && $mime !== '') {
            return $mime;
        }

        return match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'application/octet-stream',
        };
    }
}

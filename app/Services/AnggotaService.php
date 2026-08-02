<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AnggotaService
{
    public const QR_DIR = 'qr-codes';

    public const FOTO_DIR = 'dobok-photos';

    /**
     * Generate ID Anggota dengan format TKD{2 digit awal NIM}-{nomor urut global 3 digit}.
     */
    public function generateIdAnggota(string $nim): string
    {
        if (strlen($nim) < 2) {
            throw new \InvalidArgumentException('NIM tidak valid untuk generate ID Anggota.');
        }

        $angkatan = substr($nim, 0, 2);

        // Nomor urut global = seq terbesar yang pernah ada + 1 (tidak reuse setelah hapus).
        $seq = Anggota::query()->pluck('id_anggota')
            ->map(fn (string $id) => (int) substr($id, -3))
            ->max() ?? 0;

        return sprintf('TKD%s-%03d', $angkatan, $seq + 1);
    }

    /**
     * Generate QR Code (SVG) berbasis ID Anggota dan simpan di public/qr-codes.
     * Format SVG dipakai agar tidak bergantung pada ekstensi Imagick/GD.
     *
     * @return string path relatif (mis. qr-codes/TKD22-001.svg)
     */
    public function generateQrCode(string $idAnggota): string
    {
        $dir = public_path(self::QR_DIR);
        File::ensureDirectoryExists($dir);

        $filename = $idAnggota.'.svg';
        QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($idAnggota, $dir.DIRECTORY_SEPARATOR.$filename);

        return self::QR_DIR.'/'.$filename;
    }

    /**
     * Auto-provisioning akun login anggota: email {nim}@webmail.uad.ac.id,
     * password default = NIM (ter-hash), role anggota.
     */
    public function createUser(Anggota $anggota): User
    {
        return User::create([
            'name' => $anggota->nama_lengkap,
            'email' => $anggota->nim.'@webmail.uad.ac.id',
            'password' => Hash::make($anggota->nim),
            'role' => User::ROLE_ANGGOTA,
            'anggota_id' => $anggota->id,
            'force_password_change' => true,
        ]);
    }

    /**
     * Simpan foto dobok ke public/dobok-photos.
     *
     * @return string|null path relatif atau null jika tidak ada file
     */
    public function storeFotoDobok(\Illuminate\Http\UploadedFile $file, string $idAnggota): string
    {
        $dir = public_path(self::FOTO_DIR);
        File::ensureDirectoryExists($dir);

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $idAnggota.'.'.$extension;
        File::copy($file->getRealPath(), $dir.DIRECTORY_SEPARATOR.$filename);

        return self::FOTO_DIR.'/'.$filename;
    }

    /**
     * Hapus file (QR/foto) dari public jika ada.
     */
    public function deleteFile(string $relativePath): void
    {
        $full = public_path($relativePath);
        if (File::exists($full)) {
            File::delete($full);
        }
    }
}

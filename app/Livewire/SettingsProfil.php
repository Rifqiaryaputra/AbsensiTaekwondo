<?php

namespace App\Livewire;

use App\Models\PengaturanProfil;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;

class SettingsProfil extends Component
{
    use WithFileUploads;

    public $logoKiri;

    public $logoKanan;

    public ?string $existingLogoKiri = null;

    public ?string $existingLogoKanan = null;

    public string $namaUnit = '';

    public string $namaUniversitas = '';

    public string $alamat = '';

    public function mount(): void
    {
        $settings = PengaturanProfil::instance();

        $this->namaUnit = $settings->nama_unit_kegiatan ?? 'UKM Taekwondo';
        $this->namaUniversitas = $settings->nama_universitas ?? 'Universitas Ahmad Dahlan (UAD)';
        $this->alamat = $settings->alamat_sekretariat ?? '';
        $this->existingLogoKiri = $settings->logo_unit_kegiatan;
        $this->existingLogoKanan = $settings->logo_universitas;
    }

    public function save(): void
    {
        $this->validate([
            'namaUnit' => ['required', 'string', 'max:255'],
            'namaUniversitas' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:1000'],
            'logoKiri' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'logoKanan' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $profil = PengaturanProfil::instance();

        if ($this->logoKiri) {
            if ($profil->logo_unit_kegiatan) {
                File::delete(public_path($profil->logo_unit_kegiatan));
            }
            $profil->logo_unit_kegiatan = $this->storeLogo($this->logoKiri, 'unit');
        }

        if ($this->logoKanan) {
            if ($profil->logo_universitas) {
                File::delete(public_path($profil->logo_universitas));
            }
            $profil->logo_universitas = $this->storeLogo($this->logoKanan, 'universitas');
        }

        $profil->nama_unit_kegiatan = $this->namaUnit;
        $profil->nama_universitas = $this->namaUniversitas;
        $profil->alamat_sekretariat = $this->alamat;
        $profil->save();

        $this->logoKiri = null;
        $this->logoKanan = null;
        $this->existingLogoKiri = $profil->logo_unit_kegiatan;
        $this->existingLogoKanan = $profil->logo_universitas;

        $this->dispatch('toast', title: 'Berhasil', message: 'Pengaturan profil berhasil disimpan.', type: 'success');
    }

    private function storeLogo($file, string $side): string
    {
        $dir = public_path('logos');
        File::ensureDirectoryExists($dir);

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $name = $side.'-'.now()->timestamp.'.'.$extension;
        File::copy($file->getRealPath(), $dir.DIRECTORY_SEPARATOR.$name);

        return 'logos/'.$name;
    }

    public function render()
    {
        return view('livewire.settings-profil');
    }
}

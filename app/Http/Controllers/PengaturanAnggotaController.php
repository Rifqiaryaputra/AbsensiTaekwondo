<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengaturanAnggotaController extends Controller
{
    public function index()
    {
        return view('anggota.pengaturan');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        $request->session()->flash('success', 'Kata sandi berhasil diubah!');

        return back();
    }
}
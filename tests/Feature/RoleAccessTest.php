<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_admin_can_access_all_module_routes(): void
    {
        $this->actingAs($this->user(User::ROLE_ADMIN));

        foreach ([
            'dashboard',
            'absensi',
            'anggota.index',
            'perizinan.index',
            'petugas.index',
            'jadwal.index',
            'hari-libur.index',
            'rekap.index',
            'settings.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_petugas_can_access_permitted_routes(): void
    {
        $this->actingAs($this->user(User::ROLE_PETUGAS));

        foreach ([
            'dashboard',
            'absensi',
            'anggota.index',
            'perizinan.index',
            'hari-libur.index',
            'rekap.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_petugas_cannot_access_admin_only_routes(): void
    {
        $this->actingAs($this->user(User::ROLE_PETUGAS));

        foreach (['petugas.index', 'jadwal.index', 'settings.index'] as $route) {
            $this->get(route($route))->assertForbidden();
        }
    }

    public function test_anggota_can_access_own_pages(): void
    {
        $this->actingAs($this->user(User::ROLE_ANGGOTA));

        $this->get(route('anggota.dashboard'))->assertOk();
        $this->get(route('anggota.pengaturan'))->assertOk();
    }

    public function test_anggota_redirected_away_from_admin_routes(): void
    {
        $this->actingAs($this->user(User::ROLE_ANGGOTA));

        foreach (['dashboard', 'absensi', 'anggota.index', 'perizinan.index', 'petugas.index', 'jadwal.index', 'settings.index', 'rekap.index'] as $route) {
            $this->get(route($route))->assertRedirect(route('anggota.dashboard'));
        }
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}

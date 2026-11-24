<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_protected_routes(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('layanan.index'))->assertRedirect(route('login'));
        $this->get(route('transaksi.index'))->assertRedirect(route('login'));
        $this->get(route('laporan.index'))->assertRedirect(route('login'));
    }

    public function test_admin_and_kasir_can_login_and_logout(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('secret123'),
        ]);

        $kasir = User::factory()->kasir()->create([
            'email' => 'kasir@example.test',
            'password' => Hash::make('secret123'),
        ]);

        $this->post(route('login.attempt'), [
            'email' => $admin->email,
            'password' => 'secret123',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->post(route('login.attempt'), [
            'email' => $kasir->email,
            'password' => 'secret123',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($kasir);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->kasir()->create([
            'email' => 'nonaktif@example.test',
            'password' => Hash::make('secret123'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login.attempt'), [
            'email' => 'nonaktif@example.test',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_kasir_can_view_layanan_but_cannot_mutate_layanan_or_manage_users(): void
    {
        $kasir = User::factory()->kasir()->create();
        $layanan = Layanan::query()->create([
            'kode_layanan' => 'REG',
            'nama_layanan' => 'Reguler',
            'harga_per_kg' => 7000,
            'estimasi_durasi' => '3-4 hari',
            'estimasi_jam' => 84,
            'is_active' => true,
        ]);

        $this->actingAs($kasir);

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('layanan.index'))->assertOk()->assertSee('Reguler');
        $this->get(route('transaksi.index'))->assertOk();
        $this->get(route('laporan.index'))->assertOk();

        // Kasir diblokir saat mencoba CRUD Layanan atau kelola pengguna
        $this->get(route('layanan.create'))->assertForbidden();
        $this->post(route('layanan.store'), [])->assertForbidden();
        $this->get(route('layanan.edit', $layanan))->assertForbidden();
        $this->put(route('layanan.update', $layanan), [])->assertForbidden();
        $this->delete(route('layanan.destroy', $layanan))->assertForbidden();
        $this->get(route('users.index'))->assertForbidden();
    }
}

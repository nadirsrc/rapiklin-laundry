<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_dashboard_pelanggan_and_admin_user_management_work(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@rapiklin.test')->firstOrFail();
        $kasir = User::query()->where('email', 'kasir@rapiklin.test')->firstOrFail();

        $this->actingAs($admin);

        // Dashboard renders seeded KPIs and services
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Reguler')
            ->assertSee('Ekspress')
            ->assertSee('Superkilat');

        // Pelanggan index & show render cleanly
        $pelanggan = Pelanggan::query()->where('nama', 'Budi Santoso')->firstOrFail();
        $this->get(route('pelanggan.index'))
            ->assertOk()
            ->assertSee('Budi Santoso');
        $this->get(route('pelanggan.show', $pelanggan))
            ->assertOk()
            ->assertSee('Budi Santoso');

        // Admin creates a new kasir
        $this->get(route('users.create'))->assertOk();
        $this->post(route('users.store'), [
            'name' => 'Kasir Baru',
            'email' => 'kasir2@rapiklin.test',
            'role' => User::ROLE_KASIR,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => '1',
        ])->assertRedirect(route('users.index'));

        $kasirBaru = User::query()->where('email', 'kasir2@rapiklin.test')->firstOrFail();
        $this->assertSame(User::ROLE_KASIR, $kasirBaru->role);

        // Admin edits user without changing password
        $this->get(route('users.edit', $kasirBaru))->assertOk();
        $this->put(route('users.update', $kasirBaru), [
            'name' => 'Kasir Baru Updated',
            'email' => 'kasir2@rapiklin.test',
            'role' => User::ROLE_ADMIN,
            'is_active' => '1',
        ])->assertRedirect(route('users.index'));
        $kasirBaru->refresh();
        $this->assertSame('Kasir Baru Updated', $kasirBaru->name);
        $this->assertSame(User::ROLE_ADMIN, $kasirBaru->role);

        // Admin cannot toggle self inactive
        $this->patch(route('users.toggle', $admin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $admin->refresh();
        $this->assertTrue($admin->is_active);

        // Admin can toggle kasir inactive and back
        $this->patch(route('users.toggle', $kasir))
            ->assertRedirect()
            ->assertSessionHas('success');
        $kasir->refresh();
        $this->assertFalse($kasir->is_active);
    }
}

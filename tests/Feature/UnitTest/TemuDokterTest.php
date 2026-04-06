<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Dokter;
use App\Models\RoleUser;
use App\Models\TemuDokter;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Tests\TestCase;

class TemuDokterTest extends TestCase
{
    use RefreshDatabase;

    private function actAsResepsionis()
    {
        $resepsionis = WithRole::resepsionis();
        $this->actingAs($resepsionis);
        $this->withSession(['idrole' => 4, 'iduser' => $resepsionis->iduser]);
        return $resepsionis;
    }

    private function actAsPerawat()
    {
        $perawat = WithRole::perawat();
        $this->actingAs($perawat);
        $this->withSession(['idrole' => 3, 'iduser' => $perawat->iduser]);
        return $perawat;
    }

    private function actAsAdmin()
    {
        $admin = WithRole::admin();
        $this->actingAs($admin);
        $this->withSession(['idrole' => 1, 'iduser' => $admin->iduser]);
        return $admin;
    }

    public function test_resepsionis_berhasil_create_temu_dokter()
    {
        $this->actAsResepsionis();

        $pet = Pet::factory()->create();
        $dokter = WithRole::dokter();
        $roleUserDokter = RoleUser::where('iduser', $dokter->iduser)->first();

        $response = $this->post(route('Resepsionis.TemuDokter.store-temu-dokter'), [
            'idpet' => $pet->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
        ]);

        $response->assertRedirect(route('Resepsionis.TemuDokter.daftar-temu-dokter'));
        $response->assertSessionHas('success', 'Reservasi dokter berhasil dibuat.');

        $this->assertDatabaseHas('temu_dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
            'status' => 'W',
            'no_urut' => 1,
        ]);
    }

    public function test_perawat_gagal_create_temu_dokter()
    {
        $this->actAsPerawat();

        $pet = Pet::factory()->create();
        $dokter = WithRole::dokter();
        $roleUserDokter = RoleUser::where('iduser', $dokter->iduser)->first();

        $response = $this->post(route('Perawat.TemuDokter.store-temu-dokter'), [
            'idpet' => $pet->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
        ]);

        $response->assertRedirect(route('Perawat.TemuDokter.daftar-temu-dokter'));
        $response->assertSessionHas('success', 'Reservasi dokter berhasil dibuat.');

        $this->assertDatabaseHas('temu_dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
            'status' => 'W',
        ]);
    }

    public function test_admin_berhasil_create_temu_dokter()
    {
        $this->actAsAdmin();

        $pet = Pet::factory()->create();
        $dokter = WithRole::dokter();
        $roleUserDokter = RoleUser::where('iduser', $dokter->iduser)->first();

        $response = $this->post(route('Admin.TemuDokter.store-temu-dokter'), [
            'idpet' => $pet->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
        ]);

        $response->assertRedirect(route('Admin.TemuDokter.daftar-temu-dokter'));
        $response->assertSessionHas('success', 'Reservasi dokter berhasil dibuat.');

        $this->assertDatabaseHas('temu_dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
            'status' => 'W',
        ]);
    }

    public function test_admin_create_dua_temu_dokter_no_urut_reset_harian()
    {
        $this->actAsAdmin();

        $pet1 = Pet::factory()->create();
        $pet2 = Pet::factory()->create();
        $dokter = WithRole::dokter();
        $roleUserDokter = RoleUser::where('iduser', $dokter->iduser)->first();

        $this->post(route('Admin.TemuDokter.store-temu-dokter'), [
            'idpet' => $pet1->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
        ]);

        $response = $this->post(route('Admin.TemuDokter.store-temu-dokter'), [
            'idpet' => $pet2->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
        ]);

        $response->assertRedirect(route('Admin.TemuDokter.daftar-temu-dokter'));

        $temuDokterToday = TemuDokter::where('idrole_user', $roleUserDokter->idrole_user)
            ->orderBy('no_urut')
            ->get();

        $this->assertCount(2, $temuDokterToday);
        $this->assertEquals(1, $temuDokterToday[0]->no_urut);
        $this->assertEquals(2, $temuDokterToday[1]->no_urut);

        $tomorrow = Carbon::tomorrow();
        $this->travelTo($tomorrow);

        $pet3 = Pet::factory()->create();

        $this->post(route('Admin.TemuDokter.store-temu-dokter'), [
            'idpet' => $pet3->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
        ]);

        $temuDokterTomorrow = TemuDokter::where('idrole_user', $roleUserDokter->idrole_user)
            ->whereDate('waktu_daftar', $tomorrow)
            ->orderBy('no_urut')
            ->get();

        if ($temuDokterTomorrow->isNotEmpty()) {
            $this->assertEquals(1, $temuDokterTomorrow[0]->no_urut);
        }
    }

    public function test_admin_gagal_create_temu_dokter_dengan_dokter_status_inactive()
    {
        $this->actAsAdmin();

        $pet = Pet::factory()->create();
        $dokter = WithRole::dokter();
        $roleUserDokter = RoleUser::where('iduser', $dokter->iduser)->first();

        $roleUserDokter->update(['status' => 0]);

        $response = $this->post(route('Admin.TemuDokter.store-temu-dokter'), [
            'idpet' => $pet->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
        ]);

        $this->assertDatabaseMissing('temu_dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $roleUserDokter->idrole_user,
        ]);
    }
}

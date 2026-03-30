<?php

namespace Tests\Feature;

use App\Models\Dokter;
use App\Models\Perawat;
use Database\Factories\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Tests\TestCase;

class EditProfilTest extends TestCase
{
    use RefreshDatabase;

    private function actAsDokter()
    {
        $dokter = WithRole::dokter();
        $this->actingAs($dokter);
        $this->withSession(['idrole' => 2, 'iduser' => $dokter->iduser]);
        return $dokter;
    }

    private function actAsDokterLain()
    {
        $dokter = WithRole::dokter();
        $this->actingAs($dokter);
        $this->withSession(['idrole' => 2, 'iduser' => $dokter->iduser]);
        return $dokter;
    }

    private function actAsPerawat()
    {
        $perawat = WithRole::perawat();
        $this->actingAs($perawat);
        $this->withSession(['idrole' => 3, 'iduser' => $perawat->iduser]);
        return $perawat;
    }

    /**
     * TEST 1: Dokter berhasil melihat profil miliknya
     */
    public function test_dokter_berhasil_melihat_profil_miliknya()
    {
        $dokter = $this->actAsDokter();

        $response = $this->withoutVite()->get(route('Dokter.Profil.profil-saya'));

        $response->assertStatus(200);
        $response->assertViewIs('Dokter.Profil.profil-saya');
        $response->assertViewHas('user');
        $response->assertViewHas('dokter');
        
        $this->assertEquals($dokter->iduser, auth()->id());
    }

    /**
     * TEST 2: Dokter berhasil mengedit profil miliknya
     */
    public function test_dokter_berhasil_mengedit_profil_miliknya()
    {
        $dokter = $this->actAsDokter();

        $updateData = [
            'alamat' => 'Jalan Merdeka No. 123, Jakarta Pusat',
            'no_hp' => '081234567890',
            'bidang_dokter' => 'Bedah',
        ];

        $response = $this->put(route('Dokter.Profil.update-profil'), $updateData);

        $response->assertRedirect(route('Dokter.Profil.profil-saya'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('dokter', [
            'iduser' => $dokter->iduser,
            'alamat' => 'Jalan Merdeka No. 123, Jakarta Pusat',
            'no_hp' => '081234567890',
            'bidang_dokter' => 'Bedah',
        ]);
    }

    /**
     * TEST 3: Perawat gagal melihat profil dokter lain (403 Forbidden)
     */
    public function test_perawat_gagal_melihat_profil_dokter_lain()
    {
        $dokter = WithRole::dokter();
        $this->actAsPerawat();

        $response = $this->get(route('Dokter.Profil.profil-saya'));

        $this->assertContains($response->status(), [302, 403, 404]);
    }

    /**
     * TEST 4: Dokter gagal melihat profil dokter lain
     */
    public function test_dokter_gagal_melihat_profil_dokter_lain()
    {
        $dokterLain = WithRole::dokter();
        
        $dokterUtama = $this->actAsDokter();

        $response = $this->withoutVite()->get(route('Dokter.Profil.profil-saya'));

        $response->assertStatus(200);
        
        $this->assertEquals($dokterUtama->iduser, auth()->id());
        $this->assertNotEquals($dokterLain->iduser, auth()->id());
    }

    /**
     * TEST 5: Dokter gagal mengedit profil dokter lain
     */
    public function test_dokter_gagal_mengedit_profil_dokter_lain()
    {
        $dokterLain = WithRole::dokter();
        $originalAlamat = DB::table('dokter')
            ->where('iduser', $dokterLain->iduser)
            ->value('alamat');

        $this->actAsDokter();

        $updateData = [
            'alamat' => 'Alamat Palsu Yang Diinginkan Hacker',
            'no_hp' => '089999999999',
            'bidang_dokter' => 'Radiologi',
        ];

        $response = $this->put(route('Dokter.Profil.update-profil'), $updateData);

        $response->assertStatus(403);

        $currentAlamat = DB::table('dokter')
            ->where('iduser', $dokterLain->iduser)
            ->value('alamat');

        $this->assertEquals($originalAlamat, $currentAlamat);
    }
}

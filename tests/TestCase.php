<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use App\Models\Dokter;
use App\Models\Pemilik;
use App\Models\Perawat;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function payloadValid($extra = [])
    {
        return array_merge([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'jenis_kelamin' => 'L',
            'no_hp' => '08123456789'
        ], $extra);
    }

    protected function buatDokterDanLoginAdmin()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($admin);

        $dokter = Dokter::factory()->create();

        return [
            'dokter' => $dokter
        ];
    }

    protected function buatPemilikDanLoginAdmin()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($admin);

        $pemilik = Pemilik::factory()->create();

        return [
            'pemilik' => $pemilik
        ];
    }

    protected function buatPerawatDanLoginAdmin()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($admin);

        $perawat = Perawat::factory()->create();

        return [
            'perawat' => $perawat
        ];
    }
}
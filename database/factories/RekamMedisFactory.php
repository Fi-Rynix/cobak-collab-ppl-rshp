<?php

namespace Database\Factories;

use App\Models\RoleUser;
use App\Models\temuDokter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RekamMedis>
 */
class RekamMedisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anamnesa' => fake()->sentence(),
            'temuan_klinis' => fake()->sentence(),
            'diagnosa' => fake()->sentence(),
            'dokter_pemeriksa' => RoleUser::factory(),
            'idreservasi_dokter' => temuDokter::factory(),
        ];
    }

    /**
     * Create a RekamMedis for a specific dokter (RoleUser).
     */
    public function forDokter(RoleUser $dokter): static
    {
        return $this->state(fn (array $attributes) => [
            'dokter_pemeriksa' => $dokter->idrole_user,
        ]);
    }

    /**
     * Create a RekamMedis for a specific TemuDokter.
     */
    public function forTemuDokter(temuDokter $temuDokter): static
    {
        return $this->state(fn (array $attributes) => [
            'idreservasi_dokter' => $temuDokter->idreservasi_dokter,
        ]);
    }
}

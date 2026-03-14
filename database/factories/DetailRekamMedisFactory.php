<?php

namespace Database\Factories;

use App\Models\KodeTindakanTerapi;
use App\Models\RekamMedis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailRekamMedis>
 */
class DetailRekamMedisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'idrekam_medis' => RekamMedis::factory(),
            'idkode_tindakan_terapi' => KodeTindakanTerapi::factory(),
            'detail' => fake()->sentence(),
        ];
    }

    /**
     * Create a DetailRekamMedis for a specific RekamMedis.
     */
    public function forRekamMedis(RekamMedis $rekamMedis): static
    {
        return $this->state(fn (array $attributes) => [
            'idrekam_medis' => $rekamMedis->idrekam_medis,
        ]);
    }

    /**
     * Create a DetailRekamMedis for a specific KodeTindakanTerapi.
     */
    public function forKodeTindakanTerapi(KodeTindakanTerapi $kodeTindakanTerapi): static
    {
        return $this->state(fn (array $attributes) => [
            'idkode_tindakan_terapi' => $kodeTindakanTerapi->idkode_tindakan_terapi,
        ]);
    }
}

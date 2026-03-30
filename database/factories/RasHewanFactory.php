<?php

namespace Database\Factories;

use App\Models\JenisHewan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RasHewan>
 */
class RasHewanFactory extends Factory
{
    /**
     * Define the model's default state.
     * Auto-creates JenisHewan if not exists
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenisHewan = JenisHewan::factory()->create();

        return [
            'idjenis_hewan' => $jenisHewan->idjenis_hewan,
            'nama_ras' => fake()->word(),
            'deleted_at' => null,
            'deleted_by' => null,
        ];
    }

    /**
     * Create a RasHewan for a specific JenisHewan.
     */
    public function forJenisHewan(JenisHewan $jenisHewan): static
    {
        return $this->state(fn (array $attributes) => [
            'idjenis_hewan' => $jenisHewan->idjenis_hewan,
        ]);
    }
}

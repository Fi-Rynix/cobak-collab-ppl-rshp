<?php

namespace Database\Factories;

use App\Models\Pemilik;
use App\Models\RasHewan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->firstName(),
            'tanggal_lahir' => fake()->dateTimeBetween('-5 years', 'now'),
            'warna_tanda' => fake()->word(),
            'jenis_kelamin' => fake()->randomElement(['J', 'B']),
            'idpemilik' => Pemilik::factory(),
            'idras_hewan' => RasHewan::factory(),
        ];
    }

    /**
     * Create a Pet for a specific Pemilik.
     */
    public function forPemilik(Pemilik $pemilik): static
    {
        return $this->state(fn (array $attributes) => [
            'idpemilik' => $pemilik->idpemilik,
        ]);
    }

    /**
     * Create a Pet with a specific RasHewan.
     */
    public function forRasHewan(RasHewan $rasHewan): static
    {
        return $this->state(fn (array $attributes) => [
            'idras_hewan' => $rasHewan->idras_hewan,
        ]);
    }
}

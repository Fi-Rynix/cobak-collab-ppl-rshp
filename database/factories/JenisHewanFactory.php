<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JenisHewan>
 */
class JenisHewanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenisHewan = ['Anjing', 'Kucing', 'Burung', 'Kelinci', 'Hamster'];

        return [
            'nama_jenis_hewan' => fake()->randomElement($jenisHewan),
        ];
    }

    /**
     * Create a JenisHewan with name "Anjing"
     */
    public function anjing(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_jenis_hewan' => 'Anjing',
        ]);
    }

    /**
     * Create a JenisHewan with name "Kucing"
     */
    public function kucing(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_jenis_hewan' => 'Kucing',
        ]);
    }

    /**
     * Create a JenisHewan with name "Burung"
     */
    public function burung(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_jenis_hewan' => 'Burung',
        ]);
    }
}

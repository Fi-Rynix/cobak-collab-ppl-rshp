<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kategori>
 */
class KategoriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kategoriList = [
            'Pemeriksaan Rutin',
            'Vaksinasi',
            'Grooming',
            'Operasi',
            'Perawatan Gigi',
            'Konsultasi Nutrisi',
        ];

        return [
            'nama_kategori' => fake()->randomElement($kategoriList),
        ];
    }

    /**
     * Create a Kategori with specific name
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_kategori' => $name,
        ]);
    }
}

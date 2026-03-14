<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KategoriKlinis>
 */
class KategoriKlinisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kategoriKlinisList = [
            'Infeksi',
            'Dermatologi',
            'Orthopedi',
            'Oftalmologi',
            'Gastroenterologi',
            'Neurologi',
            'Kardiologi',
        ];

        return [
            'nama_kategori_klinis' => fake()->randomElement($kategoriKlinisList),
        ];
    }

    /**
     * Create a KategoriKlinis with specific name
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_kategori_klinis' => $name,
        ]);
    }
}

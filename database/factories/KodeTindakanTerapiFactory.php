<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\KategoriKlinis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KodeTindakanTerapi>
 */
class KodeTindakanTerapiFactory extends Factory
{
    /**
     * Define the model's default state.
     * Auto-creates Kategori and KategoriKlinis if not exists
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kategori = Kategori::factory()->create();
        $kategoriKlinis = KategoriKlinis::factory()->create();

        return [
            'kode' => fake()->ean8(),
            'deskripsi_tindakan_terapi' => fake()->sentence(),
            'idkategori' => $kategori->idkategori,
            'idkategori_klinis' => $kategoriKlinis->idkategori_klinis,
        ];
    }

    /**
     * Create a KodeTindakanTerapi for a specific Kategori.
     */
    public function forKategori(Kategori $kategori): static
    {
        return $this->state(fn (array $attributes) => [
            'idkategori' => $kategori->idkategori,
        ]);
    }

    /**
     * Create a KodeTindakanTerapi for a specific KategoriKlinis.
     */
    public function forKategoriKlinis(KategoriKlinis $kategoriKlinis): static
    {
        return $this->state(fn (array $attributes) => [
            'idkategori_klinis' => $kategoriKlinis->idkategori_klinis,
        ]);
    }
}

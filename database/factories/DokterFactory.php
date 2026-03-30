<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dokter>
 */
class DokterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'iduser' => User::factory(),
            'alamat' => fake()->address(),
            'no_hp' => fake()->phoneNumber(),
            'bidang_dokter' => fake()->randomElement(['Umum', 'Bedah', 'Radiologi', 'Anestesi', 'Patologi']),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'deleted_at' => null,
            'deleted_by' => null,
        ];
    }

    /**
     * Create a Dokter for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'iduser' => $user->iduser,
        ]);
    }

    /**
     * Create a Dokter with a specific bidang (specialization).
     */
    public function withBidang(string $bidang): static
    {
        return $this->state(fn (array $attributes) => [
            'bidang_dokter' => $bidang,
        ]);
    }
}

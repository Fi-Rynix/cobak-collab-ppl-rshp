<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perawat>
 */
class PerawatFactory extends Factory
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
            'pendidikan' => fake()->randomElement(['D3 Keperawatan', 'S1 Keperawatan', 'Ners']),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'deleted_at' => null,
            'deleted_by' => null,
        ];
    }

    /**
     * Create a Perawat for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'iduser' => $user->iduser,
        ]);
    }

    /**
     * Create a Perawat with a specific pendidikan (education).
     */
    public function withPendidikan(string $pendidikan): static
    {
        return $this->state(fn (array $attributes) => [
            'pendidikan' => $pendidikan,
        ]);
    }
}

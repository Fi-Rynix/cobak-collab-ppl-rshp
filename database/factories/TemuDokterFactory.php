<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\RoleUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\temuDokter>
 */
class TemuDokterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_urut' => fake()->unique()->numberBetween(1, 100),
            'status' => fake()->randomElement(['menunggu', 'sedang_diperiksa', 'selesai']),
            'idpet' => Pet::factory(),
            'idrole_user' => RoleUser::factory(),
        ];
    }

    /**
     * Create a TemuDokter for a specific Pet.
     */
    public function forPet(Pet $pet): static
    {
        return $this->state(fn (array $attributes) => [
            'idpet' => $pet->idpet,
        ]);
    }

    /**
     * Create a TemuDokter for a specific RoleUser.
     */
    public function forRoleUser(RoleUser $roleUser): static
    {
        return $this->state(fn (array $attributes) => [
            'idrole_user' => $roleUser->idrole_user,
        ]);
    }
}

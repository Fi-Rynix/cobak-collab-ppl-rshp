<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoleUser>
 */
class RoleUserFactory extends Factory
{
    /**
     * Define the model's default state.
     * Default role is Pemilik (idrole 5)
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'iduser' => User::factory(),
            'idrole' => 5, // Default: Pemilik
            'status' => 'aktif',
        ];
    }

    /**
     * Create a RoleUser with a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'iduser' => $user->iduser,
        ]);
    }

    /**
     * Create a RoleUser with Admin role.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'idrole' => 1,
        ]);
    }

    /**
     * Create a RoleUser with Dokter role.
     */
    public function dokter(): static
    {
        return $this->state(fn (array $attributes) => [
            'idrole' => 2,
        ]);
    }

    /**
     * Create a RoleUser with Perawat role.
     */
    public function perawat(): static
    {
        return $this->state(fn (array $attributes) => [
            'idrole' => 3,
        ]);
    }

    /**
     * Create a RoleUser with Resepsionis role.
     */
    public function resepsionis(): static
    {
        return $this->state(fn (array $attributes) => [
            'idrole' => 4,
        ]);
    }

    /**
     * Create a RoleUser with Pemilik role.
     */
    public function pemilik(): static
    {
        return $this->state(fn (array $attributes) => [
            'idrole' => 5,
        ]);
    }
}

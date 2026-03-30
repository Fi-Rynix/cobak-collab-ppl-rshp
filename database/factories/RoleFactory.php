<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_role' => fake()->unique()->word(),
        ];
    }

    /**
     * Create an Admin role.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => 'Admin',
        ]);
    }

    /**
     * Create a Dokter role.
     */
    public function dokter(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => 'Dokter',
        ]);
    }

    /**
     * Create a Perawat role.
     */
    public function perawat(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => 'Perawat',
        ]);
    }

    /**
     * Create a Resepsionis role.
     */
    public function resepsionis(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => 'Resepsionis',
        ]);
    }

    /**
     * Create a Pemilik role.
     */
    public function pemilik(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => 'Pemilik',
        ]);
    }

    /**
     * Create a role with specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => $name,
        ]);
    }
}
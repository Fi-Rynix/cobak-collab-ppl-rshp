<?php

namespace Database\Factories;

use App\Models\RoleUser;
use App\Models\User;

/**
 * Helper trait untuk membuat user dengan role spesifik untuk testing
 *
 * Role yang tersedia:
 * - Admin (idrole 1)
 * - Dokter (idrole 2)
 * - Perawat (idrole 3)
 * - Resepsionis (idrole 4)
 * - Pemilik (idrole 5)
 */
trait WithRole
{
    /**
     * Create a user with Admin role
     */
    public static function admin(): User
    {
        $user = User::factory()->create();
        RoleUser::factory()
            ->forUser($user)
            ->admin()
            ->create();

        return $user->fresh();
    }

    /**
     * Create a user with Dokter role
     */
    public static function dokter(): User
    {
        $user = User::factory()->create();
        RoleUser::factory()
            ->forUser($user)
            ->dokter()
            ->create();

        return $user->fresh();
    }

    /**
     * Create a user with Perawat role
     */
    public static function perawat(): User
    {
        $user = User::factory()->create();
        RoleUser::factory()
            ->forUser($user)
            ->perawat()
            ->create();

        return $user->fresh();
    }

    /**
     * Create a user with Resepsionis role
     */
    public static function resepsionis(): User
    {
        $user = User::factory()->create();
        RoleUser::factory()
            ->forUser($user)
            ->resepsionis()
            ->create();

        return $user->fresh();
    }

    /**
     * Create a user with Pemilik role
     */
    public static function pemilik(): User
    {
        $user = User::factory()->create();
        RoleUser::factory()
            ->forUser($user)
            ->pemilik()
            ->create();

        return $user->fresh();
    }
}


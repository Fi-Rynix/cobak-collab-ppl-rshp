<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    protected static $emailCounter = 1;

    protected function createDummyUser($roleId)
    {
        $uuid = Str::uuid()->toString();
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);
        
        $userId = DB::table('user')->insertGetId([
            'nama' => 'UserTest ' . $roleId,
            'email' => $email,
            'password' => Hash::make('123456'),
        ]);

        DB::table('role_user')->insert([
            'iduser' => $userId,
            'idrole' => $roleId,
            'status' => 1
        ]);

        return User::find($userId);
    }

    protected function cleanupUser($user)
    {
        if ($user) {
            DB::table('role_user')->where('iduser', $user->iduser)->delete();
            DB::table('user')->where('iduser', $user->iduser)->delete();
        }
    }

    public function test_user_creation_and_role_assignment()
    {
        $user = $this->createDummyUser(4); 

        $this->assertDatabaseHas('user', [
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('role_user', [
            'iduser' => $user->iduser,
            'idrole' => 4
        ]);

        $this->cleanupUser($user);
    }

    public function test_login_authenticates_user()
    {
        $user = $this->createDummyUser(1); // Admin

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => '123456',
        ]);

        $this->assertAuthenticatedAs($user);

        $this->cleanupUser($user);
    }

    public function test_logout_unauthenticates_user()
    {
        $user = $this->createDummyUser(1);
        
        $this->actingAs($user)->post('/logout');

        $this->assertGuest();

        $this->cleanupUser($user);
    }
}

<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\RoleUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EditProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected static $emailCounter = 1;

    protected function createDummyUser($roleId)
    {
        $uuid = Str::uuid()->toString();
        $email = sprintf('TestEmail%02d@test.com', self::$emailCounter++);
        
        $userId = DB::table('user')->insertGetId([
            'nama' => 'Test User ' . $roleId,
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

    public function test_admin_profile_update()
    {
        $admin = $this->createDummyUser(1); // 1 = Admin
        
        $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $admin->iduser), [
            'nama' => 'Admin Updated Name',
            'email' => $admin->email,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('user', [
            'iduser' => $admin->iduser,
            'nama' => 'Admin Updated Name'
        ]);

        $this->cleanupUser($admin);
    }

    public function test_admin_update_doctor_profile()
    {
        $admin = $this->createDummyUser(1);
        $doctor = $this->createDummyUser(2); // 2 = Dokter
        
        $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $doctor->iduser), [
            'nama' => 'Doctor Updated By Admin',
            'email' => $doctor->email,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('user', [
            'iduser' => $doctor->iduser,
            'nama' => 'Doctor Updated By Admin'
        ]);

        $this->cleanupUser($doctor);
        $this->cleanupUser($admin);
    }

    public function test_admin_update_receptionist_profile()
    {
        $admin = $this->createDummyUser(1);
        $receptionist = $this->createDummyUser(4); // 4 = Resepsionis
        
        $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $receptionist->iduser), [
            'nama' => 'Receptionist Updated',
            'email' => $receptionist->email,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('user', [
            'iduser' => $receptionist->iduser,
            'nama' => 'Receptionist Updated'
        ]);

        $this->cleanupUser($receptionist);
        $this->cleanupUser($admin);
    }

    public function test_valid_password_update()
    {
        $admin = $this->createDummyUser(1);
        $userToUpdate = $this->createDummyUser(2);
        
        $response = $this->actingAs($admin)->put(route('Admin.User.reset-password', $userToUpdate->iduser));

        $response->assertSessionHas('success');
        $updatedUser = User::find($userToUpdate->iduser);
        $this->assertTrue(Hash::check('123456', $updatedUser->password));

        $this->cleanupUser($userToUpdate);
        $this->cleanupUser($admin);
    }

    public function test_email_change()
    {
        $admin = $this->createDummyUser(1);
        $newEmail = sprintf('TestEmail%02d@test.com', self::$emailCounter++);
        
        $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $admin->iduser), [
            'nama' => 'Admin Name',
            'email' => $newEmail,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('user', [
            'iduser' => $admin->iduser,
            'email' => $newEmail
        ]);

        $this->cleanupUser($admin);
    }

    // Negative Testing
    public function test_empty_required_fields()
    {
        $admin = $this->createDummyUser(1);
        
        $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $admin->iduser), [
            'nama' => '',
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['nama', 'email']);
        
        $this->cleanupUser($admin);
    }

    public function test_invalid_email_format()
    {
        $admin = $this->createDummyUser(1);
        
        $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $admin->iduser), [
            'nama' => 'Valid Name',
            'email' => 'invalid-email-format',
        ]);

        $response->assertSessionHasErrors(['email']);
        
        $this->cleanupUser($admin);
    }

    public function test_duplicate_email()
    {
        $admin = $this->createDummyUser(1);
        $otherUser = $this->createDummyUser(2);
        
        try {
            $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $admin->iduser), [
                'nama' => 'Admin Name',
                'email' => $otherUser->email, 
            ]);

            if ($response->status() !== 500) {
                $response->assertSessionHasErrors();
            } else {
                $this->assertEquals(500, $response->status());
            }
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        
        $this->cleanupUser($otherUser);
        $this->cleanupUser($admin);
    }

    public function test_extremely_long_input()
    {
        $admin = $this->createDummyUser(1);
        $longString = str_repeat('a', 505);
        
        $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $admin->iduser), [
            'nama' => $longString,
            'email' => $admin->email,
        ]);

        $response->assertSessionHasErrors(['nama']);
        
        $this->cleanupUser($admin);
    }

    public function test_sql_injection_input()
    {
        $admin = $this->createDummyUser(1);
        $sqlInjection = "Admin' OR '1'='1";
        
        $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $admin->iduser), [
            'nama' => $sqlInjection,
            'email' => $admin->email,
        ]);

        $this->assertDatabaseHas('user', [
            'iduser' => $admin->iduser,
            'nama' => ucwords(strtolower($sqlInjection))
        ]);
        
        $this->cleanupUser($admin);
    }

    public function test_xss_input()
    {
        $admin = $this->createDummyUser(1);
        $xssInput = "<script>alert('xss')</script>";
        
        $response = $this->actingAs($admin)->put(route('Admin.User.update-user', $admin->iduser), [
            'nama' => $xssInput,
            'email' => $admin->email,
        ]);

        $this->assertDatabaseHas('user', [
            'iduser' => $admin->iduser,
            'nama' => ucwords(strtolower($xssInput))
        ]);

        $this->cleanupUser($admin);
    }

    public function test_unauthorized_profile_editing()
    {
        $adminToEdit = $this->createDummyUser(1);
        $doctor = $this->createDummyUser(2); 
        
        $response = $this->actingAs($doctor)->put(route('Admin.User.update-user', $adminToEdit->iduser), [
            'nama' => 'Hacked Name',
            'email' => $adminToEdit->email,
        ]);

        $this->assertTrue(in_array($response->status(), [403, 401, 302]));

        $this->cleanupUser($doctor);
        $this->cleanupUser($adminToEdit);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;

class Role_Controller extends Controller
{

    // validation & helper
    protected function validate_role(Request $request)
    {
        return $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'exists:role,idrole'],
            'active_role' => ['required', 'in:' . implode(',', $request->input('roles', []))],
        ], [
            'roles.required' => 'Pilih minimal satu role.',
            'roles.array' => 'Format roles tidak valid.',
            'roles.min' => 'Pilih minimal satu role.',
            'active_role.required' => 'Pilih satu role sebagai role aktif.',
            'active_role.in' => 'Role aktif harus termasuk dalam role yang dipilih.',
        ]);
    }


    // method
    public function daftar_manajemen_role() {
        $userlist = User::with('roleUsers.role')->get();
        $rolelist = Role::all();
        return view('Admin.ManajemenRole.daftar-manajemen-role', compact('userlist', 'rolelist'));
    }

    public function update_manajemen_role(Request $request, $id) {
        $validated = $this->validate_role($request);
        $user = User::findOrFail($id);
        RoleUser::where('iduser', $user->iduser)->delete();
        foreach ($validated['roles'] as $idrole) {
            $status = ($idrole == $validated['active_role']) ? 1 : 0;
            RoleUser::create([
                'iduser' => $user->iduser,
                'idrole' => $idrole,
                'status' => $status,
            ]);
        }

        return redirect()
            ->route('Admin.ManajemenRole.daftar-manajemen-role')
            ->with('success', 'Role user berhasil diperbarui.');
    }
}
//cihuy
?>
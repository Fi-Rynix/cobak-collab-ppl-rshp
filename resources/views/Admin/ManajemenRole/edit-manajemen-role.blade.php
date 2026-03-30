@foreach ($userlist as $user)
<el-dialog>
    <dialog id="modalEdit-{{ $user->iduser }}" class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

        <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4">
            <el-dialog-panel class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">

                <h2 class="text-xl font-semibold mb-4">Ubah Role - {{ $user->nama }}</h2>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800 text-sm font-semibold mb-2">Terjadi kesalahan validasi:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-700 text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('Admin.ManajemenRole.update-manajemen-role', $user->iduser) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block mb-3 text-sm text-slate-700 font-semibold">Pilih Role (Checkbox)</label>
                        
                        @php
                            $userRoleIds = $user->roleUsers->pluck('idrole')->toArray();
                        @endphp

                        <div class="space-y-2">
                            @foreach($rolelist as $role)
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="role_{{ $role->idrole }}_{{ $user->iduser }}"
                                           name="roles[]" 
                                           value="{{ $role->idrole }}"
                                           {{ in_array($role->idrole, $userRoleIds) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
                                    <label for="role_{{ $role->idrole }}_{{ $user->iduser }}" class="ml-2 text-sm text-slate-700 cursor-pointer">
                                        {{ $role->nama_role }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block mb-3 text-sm text-slate-700 font-semibold">Pilih Role Aktif (Radio)</label>
                        
                        <div class="space-y-2">
                            @foreach($rolelist as $role)
                                <div class="flex items-center">
                                    <input type="radio" 
                                           id="active_{{ $role->idrole }}_{{ $user->iduser }}"
                                           name="active_role" 
                                           value="{{ $role->idrole }}"
                                           {{ in_array($role->idrole, $userRoleIds) && $user->roleUsers->firstWhere('idrole', $role->idrole)->status == 1 ? 'checked' : '' }}
                                           class="w-4 h-4 border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
                                    <label for="active_{{ $role->idrole }}_{{ $user->iduser }}" class="ml-2 text-sm text-slate-700 cursor-pointer">
                                        {{ $role->nama_role }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('active_role')
                            <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button"
                                command="close"
                                commandfor="modalEdit-{{ $user->iduser }}"
                                class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            Batal
                        </button>

                        <button type="submit"
                                class="px-5 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                            Simpan
                        </button>
                    </div>
                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>
@endforeach

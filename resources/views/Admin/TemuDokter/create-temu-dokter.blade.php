<el-dialog>
    <dialog id="modalCreate" class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

        <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4">
            <el-dialog-panel class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">

                <h2 class="text-xl font-semibold mb-4">Buat Reservasi Dokter</h2>

                {{-- Error Messages --}}
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

                <form action="{{ route('Admin.TemuDokter.store-temu-dokter') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Pet/Pasien</label>
                        <select name="idpet"
                            class="w-full rounded-md border p-2 transition @error('idpet') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                            <option value="">-- Pilih Pet/Pasien --</option>
                            @foreach(\App\Models\Pet::with('pemilik.user', 'rasHewan')->get() as $pet)
                                <option value="{{ $pet->idpet }}" {{ old('idpet') == $pet->idpet ? 'selected' : '' }}>
                                    {{ sprintf('%s - %s (%s)', $pet->nama, $pet->rasHewan->nama_ras, $pet->pemilik->user->nama ?? '-') }}
                                </option>
                            @endforeach
                        </select>
                        @error('idpet')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Dokter</label>
                        <select name="idrole_user"
                            class="w-full rounded-md border p-2 transition @error('idrole_user') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                            <option value="">-- Pilih Dokter --</option>
                            @foreach(\App\Models\RoleUser::where('idrole', 2)->where('status', 1)->with('user')->get() as $dokter)
                                <option value="{{ $dokter->idrole_user }}" {{ old('idrole_user') == $dokter->idrole_user ? 'selected' : '' }}>
                                    {{ $dokter->user->nama ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('idrole_user')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button"
                                command="close"
                                commandfor="modalCreate"
                                class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            Batal
                        </button>

                        <button type="submit"
                                class="px-5 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                            Buat Reservasi
                        </button>
                    </div>
                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

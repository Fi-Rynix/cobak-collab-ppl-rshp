<el-dialog>
    <dialog id="modalCreate" class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

        <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4">
            <el-dialog-panel class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">

                <h2 class="text-xl font-semibold mb-4">Tambah Pet</h2>

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

                <form action="{{ route('Admin.Pet.store-pet') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Nama Pet</label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                            class="w-full rounded-md border p-2 transition @error('nama') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                        @error('nama')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                            class="w-full rounded-md border p-2 transition @error('tanggal_lahir') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                        @error('tanggal_lahir')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Warna/Tanda</label>
                        <input type="text" name="warna_tanda" value="{{ old('warna_tanda') }}"
                            class="w-full rounded-md border p-2 transition @error('warna_tanda') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                        @error('warna_tanda')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                            class="w-full rounded-md border p-2 transition @error('jenis_kelamin') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="J" {{ old('jenis_kelamin') == 'J' ? 'selected' : '' }}>Jantan</option>
                            <option value="B" {{ old('jenis_kelamin') == 'B' ? 'selected' : '' }}>Betina</option>
                        </select>
                        @error('jenis_kelamin')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Pemilik</label>
                        <select name="idpemilik"
                            class="w-full rounded-md border p-2 transition @error('idpemilik') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                            <option value="">-- Pilih Pemilik --</option>
                            @foreach($pemiliklist as $pemilik)
                                <option value="{{ $pemilik->idpemilik }}" {{ old('idpemilik') == $pemilik->idpemilik ? 'selected' : '' }}>
                                    {{ $pemilik->user->nama ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('idpemilik')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Ras Hewan</label>
                        <select name="idras_hewan"
                            class="w-full rounded-md border p-2 transition @error('idras_hewan') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                            <option value="">-- Pilih Ras Hewan --</option>
                            @foreach($rashevanlist as $rashewan)
                                <option value="{{ $rashewan->idras_hewan }}" {{ old('idras_hewan') == $rashewan->idras_hewan ? 'selected' : '' }}>
                                    {{ $rashewan->nama_ras ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('idras_hewan')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button"
                                command="close"
                                commandfor="modalCreate"
                                class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            Batal
                        </button>

                        <button type="submit"
                                class="px-5 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                            Tambah
                        </button>
                    </div>
                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

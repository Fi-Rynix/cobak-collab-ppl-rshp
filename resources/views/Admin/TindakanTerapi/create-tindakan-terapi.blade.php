<el-dialog>
    <dialog id="modalCreate" class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">

        {{-- Backdrop --}}
        <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

        {{-- Wrapper tengah --}}
        <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4">

            {{-- Panel --}}
            <el-dialog-panel class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 transform transition-all">

                <h2 class="text-xl font-semibold mb-4">Tambah Tindakan Terapi</h2>

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

                <form action="{{ route('Admin.TindakanTerapi.store-tindakan-terapi') }}" method="POST">
                    @csrf

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Deskripsi Tindakan Terapi</label>
                        <input type="text" name="deskripsi_tindakan_terapi" value="{{ old('deskripsi_tindakan_terapi') }}"
                              class="w-full rounded-md border p-2 transition @error('deskripsi_tindakan_terapi') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                        @error('deskripsi_tindakan_terapi')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Kategori</label>
                        <select name="idkategori"
                                class="w-full rounded-md border p-2 transition @error('idkategori') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategorilist as $kategori)
                                <option value="{{ $kategori->idkategori }}" {{ old('idkategori') == $kategori->idkategori ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('idkategori')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori Klinis --}}
                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Kategori Klinis</label>
                        <select name="idkategori_klinis"
                                class="w-full rounded-md border p-2 transition @error('idkategori_klinis') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror focus:border-transparent focus:ring-2">
                            <option value="">-- Pilih Kategori Klinis --</option>
                            @foreach($kategori_klinislist as $klinis)
                                <option value="{{ $klinis->idkategori_klinis }}" {{ old('idkategori_klinis') == $klinis->idkategori_klinis ? 'selected' : '' }}>{{ $klinis->nama_kategori_klinis }}</option>
                            @endforeach
                        </select>
                        @error('idkategori_klinis')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button"
                                command="close"
                                commandfor="modalCreate"
                                class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            Batal
                        </button>

                        <button type="submit"
                                class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>

                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

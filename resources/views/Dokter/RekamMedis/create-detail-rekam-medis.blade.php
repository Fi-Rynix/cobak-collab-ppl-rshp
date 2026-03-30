<el-dialog>
    <dialog id="modalCreate" class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">

        {{-- BACKDROP --}}
        <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

        {{-- WRAPPER TENGAH --}}
        <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4">

            {{-- PANEL MODAL --}}
            <el-dialog-panel
                class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6
                       transform transition-all">

                <h2 class="text-lg font-semibold mb-4 text-gray-800">
                    Tambah Tindakan
                </h2>

                {{-- Alert Error --}}
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

                <form action="{{ route('Dokter.RekamMedis.store-detail', $rekam_medis->idrekam_medis) }}" method="POST" class="space-y-3">
                    @csrf

                    {{-- Row 1: Kode Tindakan --}}
                    <div class="flex items-start gap-3">
                        <label class="text-sm font-medium text-gray-700 pt-2.5 whitespace-nowrap">Kode Tindakan:</label>
                        <div class="flex-1">
                            <select name="idkode_tindakan_terapi" class="w-full rounded-md border @error('idkode_tindakan_terapi') border-red-500 @else border-slate-300 @enderror p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">-- Pilih Kode Tindakan --</option>
                                @foreach ($kode_tindakan as $kode)
                                    <option value="{{ $kode->idkode_tindakan_terapi }}" {{ old('idkode_tindakan_terapi') == $kode->idkode_tindakan_terapi ? 'selected' : '' }}>
                                        {{ $kode->kode }} - {{ $kode->deskripsi_tindakan_terapi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idkode_tindakan_terapi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 2: Detail Tindakan --}}
                    <div class="flex items-start gap-3">
                        <label class="text-sm font-medium text-gray-700 pt-2 whitespace-nowrap">Detail:</label>
                        <div class="flex-1">
                            <textarea name="detail" rows="4" class="w-full rounded-md border @error('detail') border-red-500 @else border-slate-300 @enderror p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Jelaskan detail tindakan yang dilakukan..." required>{{ old('detail') }}</textarea>
                            @error('detail')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            type="button"
                            command="close"
                            commandfor="modalCreate"
                            class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300">
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

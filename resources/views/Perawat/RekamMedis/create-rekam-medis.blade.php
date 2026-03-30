<el-dialog>
    <dialog id="modalCreate" class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">

        {{-- BACKDROP --}}
        <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

        {{-- WRAPPER TENGAH --}}
        <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4 overflow-y-auto">

            {{-- PANEL MODAL --}}
            <el-dialog-panel
                class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6
                       transform transition-all my-8">

                <h2 class="text-lg font-semibold mb-4 text-gray-800">
                    Buat Rekam Medis Baru
                </h2>

                <form action="{{ route('Perawat.RekamMedis.store-rekam-medis') }}" method="POST" class="space-y-3">
                    @csrf

                    {{-- Row 1: Reservasi --}}
                    <div class="flex items-start gap-3">
                        <label class="text-sm font-medium text-gray-700 pt-2.5 whitespace-nowrap">Reservasi:</label>
                        <div class="flex-1">
                            <select name="idreservasi_dokter" class="w-full rounded-md border @error('idreservasi_dokter') border-red-500 @else border-slate-300 @enderror p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih Reservasi --</option>
                                @foreach($reservasilist as $reservasi)
                                    @php
                                        $tanggalLabel = \Carbon\Carbon::parse($reservasi->waktu_daftar)->isToday() ? 'Today' : \Carbon\Carbon::parse($reservasi->waktu_daftar)->format('d-m-Y');
                                    @endphp
                                    <option value="{{ $reservasi->idreservasi_dokter }}" {{ old('idreservasi_dokter') == $reservasi->idreservasi_dokter ? 'selected' : '' }}>
                                        #{{ $reservasi->no_urut }} - {{ $reservasi->pet_nama }} - {{ $reservasi->nama_ras ?? '-' }} ({{ $reservasi->pemilik_nama ?? '-' }}) - {{ $reservasi->dokter_nama ?? '-' }} - {{ $tanggalLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idreservasi_dokter')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 2: Anamnesa --}}
                    <div class="flex items-start gap-3">
                        <label class="text-sm font-medium text-gray-700 pt-2 whitespace-nowrap">Anamnesa:</label>
                        <div class="flex-1">
                            <textarea name="anamnesa" rows="2" class="w-full rounded-md border @error('anamnesa') border-red-500 @else border-slate-300 @enderror p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Max 1000 karakter">{{ old('anamnesa') }}</textarea>
                            @error('anamnesa')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 3: Temuan Klinis --}}
                    <div class="flex items-start gap-3">
                        <label class="text-sm font-medium text-gray-700 pt-2 whitespace-nowrap">Temuan:</label>
                        <div class="flex-1">
                            <textarea name="temuan_klinis" rows="2" class="w-full rounded-md border @error('temuan_klinis') border-red-500 @else border-slate-300 @enderror p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Max 1000 karakter">{{ old('temuan_klinis') }}</textarea>
                            @error('temuan_klinis')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 4: Diagnosa --}}
                    <div class="flex items-start gap-3">
                        <label class="text-sm font-medium text-gray-700 pt-2 whitespace-nowrap">Diagnosa:</label>
                        <div class="flex-1">
                            <textarea name="diagnosa" rows="2" class="w-full rounded-md border @error('diagnosa') border-red-500 @else border-slate-300 @enderror p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Max 1000 karakter">{{ old('diagnosa') }}</textarea>
                            @error('diagnosa')
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

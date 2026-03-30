@foreach($temuDokterlist as $row)
    <el-dialog>
        <dialog id="modalCancel{{ $row->idreservasi_dokter }}"
            class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">

            {{-- BACKDROP --}}
            <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

            {{-- WRAPPER TENGAH --}}
            <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4">

                {{-- PANEL MODAL --}}
                <el-dialog-panel
                    class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6
                           transform transition-all">

                    <h2 class="text-lg font-semibold mb-3 text-gray-800">
                        Konfirmasi Batalkan Reservasi
                    </h2>

                    <p class="text-gray-600 text-sm mb-6">
                        Apakah kamu yakin ingin membatalkan reservasi dokter untuk
                        <span class="font-semibold text-gray-800">
                            "{{ $row->pet_nama }}"
                        </span>
                        dengan dokter
                        <span class="font-semibold text-gray-800">
                            "{{ $row->dokter_nama }}"
                        </span>?
                        <br>Status reservasi akan berubah menjadi Cancelled.
                    </p>

                    <div class="flex justify-end gap-2">

                        {{-- BATAL --}}
                        <button
                            type="button"
                            command="close"
                            commandfor="modalCancel{{ $row->idreservasi_dokter }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300">
                            Batal
                        </button>

                        {{-- BATALKAN RESERVASI --}}
                        <form method="POST" action="{{ route('Resepsionis.TemuDokter.cancel-temu-dokter', $row->idreservasi_dokter) }}">
                            @csrf
                            @method('PUT')

                            <button
                                type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                                Batalkan Reservasi
                            </button>
                        </form>

                    </div>

                </el-dialog-panel>

            </div>

        </dialog>
    </el-dialog>
@endforeach

<el-dialog>
    <dialog id="modalDelete-{{ $pemilik->idpemilik }}" class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

        <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4">
            <el-dialog-panel class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">

                <h2 class="text-lg font-semibold mb-3 text-gray-800">Konfirmasi Hapus</h2>

                <p class="text-gray-600 text-sm mb-6">
                    Apakah kamu yakin ingin menghapus pemilik
                    <span class="font-semibold text-gray-800">
                        {{ $pemilik->nama ?? 'Tanpa Nama' }}
                    </span>?
                    <br>Data yang dihapus tidak dapat dikembalikan.
                </p>

                <div class="flex justify-end gap-2">

                    {{-- Batal --}}
                    <button type="button"
                            command="close"
                            commandfor="modalDelete-{{ $pemilik->idpemilik }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300">
                        Batal
                    </button>

                    {{-- Hapus --}}
                    <form action="{{ route('Resepsionis.Pemilik.delete-pemilik', $pemilik->idpemilik) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                            Hapus
                        </button>
                    </form>

                </div>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

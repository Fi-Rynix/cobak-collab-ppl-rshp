<el-dialog>
    <dialog id="modalResetPassword-{{ $row->iduser }}"
            class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">

        {{-- BACKDROP --}}
        <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

        {{-- PANEL --}}
        <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4">
            <el-dialog-panel
                class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all">

                <h2 class="text-lg font-semibold mb-3 text-gray-800">Reset Password</h2>

                <p class="text-gray-600 text-sm mb-6">
                    Password user
                    <span class="font-semibold text-gray-800">{{ $row->nama }}</span>
                    akan di-reset menjadi <span class="font-semibold text-gray-800">123456</span>.
                </p>

                <div class="flex justify-end gap-2">
                    <button type="button"
                            command="close"
                            commandfor="modalResetPassword-{{ $row->iduser }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300">
                        Batal
                    </button>

                    <form action="{{ route('Admin.User.reset-password', $row->iduser) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                                class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Reset
                        </button>
                    </form>
                </div>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

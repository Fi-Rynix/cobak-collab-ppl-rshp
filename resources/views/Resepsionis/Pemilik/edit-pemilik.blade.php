<el-dialog>
    <dialog id="modalEdit-{{ $pemilik->iduser }}" class="fixed inset-0 z-[9000] bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-black/50 z-[9000]"></el-dialog-backdrop>

        <div class="fixed inset-0 z-[9500] flex items-center justify-center p-4">
            <el-dialog-panel class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">

                <h2 class="text-xl font-semibold mb-4">{{ $pemilik->idpemilik ? 'Edit' : 'Isi' }} Data Pemilik</h2>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-700 text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('Resepsionis.Pemilik.save-pemilik', $pemilik->iduser) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Nama</label>
                        <input type="text" name="nama" value="{{ old('nama', $pemilik->nama) }}"
                        class="w-full rounded-md border p-2 @error('nama') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror">
                        @error('nama')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $pemilik->email) }}"
                        class="w-full rounded-md border p-2 @error('email') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror">
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">No WA</label>
                        <input type="text" name="no_wa" value="{{ old('no_wa', $pemilik->no_wa) }}"
                        class="w-full rounded-md border p-2 @error('no_wa') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror">
                        @error('no_wa')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-sm text-slate-700 font-medium">Alamat</label>
                        <textarea name="alamat"
                        class="w-full rounded-md border p-2 h-24 @error('alamat') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror">{{ old('alamat', $pemilik->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button"
                                command="close"
                                commandfor="modalEdit-{{ $pemilik->iduser }}"
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

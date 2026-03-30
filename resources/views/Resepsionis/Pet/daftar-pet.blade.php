@extends('layouts.app')

@section('title', 'Data Pet')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Kelola Data Pet</h1>
</div>

<div class="bg-white rounded-lg shadow-md border border-slate-200">
    
    <div class="p-6 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Pet</h2>

        {{-- Tombol Tambah --}}
        <button
            command="show-modal"
            commandfor="modalCreate"
            class="inline-flex items-center gap-2 px-5 py-2.5
                bg-gradient-to-r from-blue-500 to-blue-600
                text-white rounded-lg hover:from-blue-600 hover:to-blue-700
                transition-all duration-200 shadow-md hover:shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span class="font-medium">Tambah Pet</span>
        </button>
    </div>

    {{-- Tabel --}}

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Tanggal Lahir</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Warna/Tanda</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Jenis Kelamin</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Pemilik</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Ras Hewan</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @foreach($petlist as $row)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $row->nama }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($row->tanggal_lahir)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4">{{ $row->warna_tanda }}</td>
                        <td class="px-6 py-4">{{ $row->jenis_kelamin == 'J' ? 'Jantan' : 'Betina' }}</td>
                        <td class="px-6 py-4">{{ $row->pemilik_nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $row->nama_ras ?? '-' }}</td>

                        <td class="px-6 py-4 text-center">
                            <button
                                command="show-modal"
                                commandfor="modalEdit-{{ $row->idpet }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5
                                    bg-teal-500 text-white text-sm font-medium
                                    rounded-md hover:bg-teal-600 transition shadow-sm hover:shadow">
                                Edit
                            </button>

                            <button
                                command="show-modal"
                                commandfor="modalDelete-{{ $row->idpet }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5
                                    bg-red-500 text-white text-sm font-medium
                                    rounded-md hover:bg-red-600 transition shadow-sm hover:shadow">
                                Hapus
                            </button>
                        </td>
                    </tr>

                    @include('Resepsionis.Pet.edit-pet', ['pet' => $row])
                    @include('Resepsionis.Pet.delete-pet', ['pet' => $row])

                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('Resepsionis.Pet.create-pet')

@endsection

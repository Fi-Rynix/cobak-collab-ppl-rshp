@extends('layouts.app')

@section('title', 'Data Dokter')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Kelola Data Dokter</h1>
</div>

<div class="bg-white rounded-lg shadow-md border border-slate-200">
    
    <div class="p-6 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Dokter</h2>

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
            <span class="font-medium">Tambah Dokter</span>
        </button>
    </div>

    {{-- Tabel --}}

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Bidang</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">No HP</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Jenis Kelamin</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @foreach($dokterlist as $row)
                    @php
                        $isIncomplete = !$row->no_hp || !$row->jenis_kelamin || !$row->bidang_dokter || !$row->alamat || !$row->iddokter;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors duration-150 {{ $isIncomplete ? 'bg-yellow-50' : '' }}">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium">{{ $row->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $row->email ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($row->bidang_dokter)
                                {{ $row->bidang_dokter }}
                            @else
                                <span class="text-gray-400 italic">Belum diisi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($row->no_hp)
                                {{ $row->no_hp }}
                            @else
                                <span class="text-gray-400 italic">Belum diisi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($row->jenis_kelamin)
                                {{ $row->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            @else
                                <span class="text-gray-400 italic">Belum diisi</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($isIncomplete)
                                <button
                                    command="show-modal"
                                    commandfor="modalEdit-{{ $row->iduser }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5
                                        bg-amber-500 text-white text-sm font-medium
                                        rounded-md hover:bg-amber-600 transition shadow-sm hover:shadow">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Isi Data
                                </button>
                            @else
                                <button
                                    command="show-modal"
                                    commandfor="modalEdit-{{ $row->iduser }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5
                                        bg-teal-500 text-white text-sm font-medium
                                        rounded-md hover:bg-teal-600 transition shadow-sm hover:shadow">
                                    Edit
                                </button>

                                <button
                                    command="show-modal"
                                    commandfor="modalDelete-{{ $row->iddokter }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5
                                        bg-red-500 text-white text-sm font-medium
                                        rounded-md hover:bg-red-600 transition shadow-sm hover:shadow">
                                    Hapus
                                </button>
                            @endif
                        </td>
                    </tr>

                    @if($row->iddokter)
                        @include('Admin.Dokter.edit-dokter', ['dokter' => $row])
                        @include('Admin.Dokter.delete-dokter', ['dokter' => $row])
                    @else
                        @include('Admin.Dokter.edit-dokter', ['dokter' => (object)["iduser" => $row->iduser, "nama" => $row->nama, "email" => $row->email, "iddokter" => null, "no_hp" => null, "jenis_kelamin" => null, "bidang_dokter" => null, "alamat" => null]])
                    @endif

                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('Admin.Dokter.create-dokter')

@endsection

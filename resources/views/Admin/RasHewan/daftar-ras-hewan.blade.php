@extends('layouts.app')

@section('title', 'Ras Hewan')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Kelola Data Ras Hewan</h1>
</div>

{{-- Container --}}
<div class="bg-white rounded-lg shadow-md border border-slate-200">

    <div class="p-6 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Ras Hewan</h2>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Jenis Hewan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Ras Hewan</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($hewanRasList as $jenis)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $jenis->nama_jenis_hewan }}</td>
                        <td class="px-6 py-4">
                            @if($jenis->rasHewan && $jenis->rasHewan->count() > 0)
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($jenis->rasHewan as $ras)
                                        <li class="flex items-center justify-between">
                                            <span>{{ $ras->nama_ras }}</span>
                                            <div class="flex gap-2">
                                                <button
                                                    command="show-modal"
                                                    commandfor="modalEdit-{{ $ras->idras_hewan }}"
                                                    class="px-3 py-1 bg-teal-500 text-white rounded-md text-sm hover:bg-teal-600">
                                                    Edit
                                                </button>
                                                <button
                                                    command="show-modal"
                                                    commandfor="modalDelete-{{ $ras->idras_hewan }}"
                                                    class="px-3 py-1 bg-red-500 text-white rounded-md text-sm hover:bg-red-600">
                                                    Hapus
                                                </button>
                                            </div>
                                        </li>

                                        @include('Admin.RasHewan.edit-ras-hewan', ['ras' => $ras])
                                        @include('Admin.RasHewan.delete-ras-hewan', ['ras' => $ras])
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-500">Tidak ada ras terdaftar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button
                                command="show-modal"
                                commandfor="modalCreate-{{ $jenis->idjenis_hewan }}"
                                class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                                Tambah Ras
                            </button>
                            @include('Admin.RasHewan.create-ras-hewan', ['jenis' => $jenis])
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

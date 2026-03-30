@extends('layouts.app')

@section('title', 'Jenis Hewan')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Kelola Data Jenis Hewan</h1>
</div>

{{-- Container --}}
<div class="bg-white rounded-lg shadow-md border border-slate-200">
    
    <div class="p-6 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Jenis Hewan</h2>

        {{-- Tambah --}}
        <button
            command="show-modal"
            commandfor="modalCreate"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span class="font-medium">Tambah Hewan</span>
        </button>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        No
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Jenis Hewan
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($hewanlist as $row)
                <tr class="hover:bg-slate-50 transition-colors duration-150">
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">{{ $row->nama_jenis_hewan }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">

                            {{-- Edit --}}
                            <button
                                command="show-modal"
                                commandfor="modalEdit-{{ $row->idjenis_hewan }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-500 text-white text-sm font-medium rounded-md hover:bg-teal-600 transition shadow-sm hover:shadow">
                                Edit
                            </button>

                            {{-- Hapus --}}
                            <button
                                command="show-modal"
                                commandfor="modalDelete-{{ $row->idjenis_hewan }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600 transition shadow-sm hover:shadow">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>

                @include('Admin.JenisHewan.edit-jenis-hewan', ['row' => $row])
                @include('Admin.JenisHewan.delete-jenis-hewan', ['row' => $row])

                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('Admin.JenisHewan.create-jenis-hewan')

@endsection

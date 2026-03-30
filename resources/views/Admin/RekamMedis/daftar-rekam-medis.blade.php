@extends('layouts.app')

@section('title', 'Rekam Medis')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Manajemen Rekam Medis</h1>
</div>

<div class="bg-white rounded-lg shadow-md border border-slate-200">
    
    <div class="p-6 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Rekam Medis</h2>

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
            <span class="font-medium">Buat Rekam Medis</span>
        </button>
    </div>

    {{-- Alert Error --}}
    @if ($errors->any())
        <div class="m-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700 font-semibold mb-2">Terjadi kesalahan:</p>
            <ul class="text-red-600 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Pet/Pasien</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Pemilik</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Dokter</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Anamnesa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Temuan Klinis</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Diagnosa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Detail</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Tindakan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse($rekamMedislist as $row)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-4 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $row->temuDokter->pet->nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $row->temuDokter->pet->pemilik->user->nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $row->temuDokter->roleUser->user->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ Str::limit($row->anamnesa, 40) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ Str::limit($row->temuan_klinis, 40) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ Str::limit($row->diagnosa, 40) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ Str::limit($row->detailRekamMedis->detail ?? '-', 40) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            @if($row->detailRekamMedis)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">{{ $row->detailRekamMedis->kodeTindakanTerapi->kode ?? '-' }}</span>
                                <div class="text-gray-600 mt-1">{{ Str::limit($row->detailRekamMedis->kodeTindakanTerapi->deskripsi_tindakan_terapi ?? '-', 50) }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button
                                    command="show-modal"
                                    commandfor="modalEdit{{ $row->idrekam_medis }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs font-semibold rounded-md transition-colors duration-150">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button
                                    command="show-modal"
                                    commandfor="modalDelete{{ $row->idrekam_medis }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 text-xs font-semibold rounded-md transition-colors duration-150">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-6 py-8 text-center text-slate-500">
                            Tidak ada data rekam medis
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('Admin.RekamMedis.create-rekam-medis')
@include('Admin.RekamMedis.edit-rekam-medis')
@include('Admin.RekamMedis.delete-rekam-medis')

@endsection

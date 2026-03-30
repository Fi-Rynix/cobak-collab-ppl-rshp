@extends('layouts.app')

@section('title', 'Rekam Medis')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Rekam Medis Pet</h1>
</div>

<div class="bg-white rounded-lg shadow-md border border-slate-200">
    
    <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Rekam Medis</h2>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Pet</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Pemilik</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Tanggal Periksa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Dokter Pemeriksa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Anamnesa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Temuan Klinis</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Diagnosa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse($rekam_medis_list as $index => $rekam)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-4 font-medium">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $rekam->pet_nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $rekam->pemilik_nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            {{ \Carbon\Carbon::parse($rekam->created_at)->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-6 py-4">{{ $rekam->dokter_nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ Str::limit($rekam->anamnesa, 30) ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ Str::limit($rekam->temuan_klinis, 30) ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ Str::limit($rekam->diagnosa, 30) ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('Dokter.RekamMedis.detail-rekam-medis', $rekam->idrekam_medis) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs font-semibold rounded-md transition-colors duration-150">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Lihat
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-slate-500">
                            Belum ada rekam medis
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

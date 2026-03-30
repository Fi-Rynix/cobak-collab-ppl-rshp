@extends('layouts.app')

@section('title', 'Temu Dokter')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Manajemen Reservasi Dokter</h1>
</div>

<div class="bg-white rounded-lg shadow-md border border-slate-200">
    
    <div class="p-6 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Reservasi Dokter</h2>

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
            <span class="font-medium">Buat Reservasi</span>
        </button>
    </div>

    {{-- Filter Section --}}
    <div class="p-6 border-b border-slate-200 bg-slate-50">
        <div class="flex gap-4 flex-wrap">
            {{-- Filter Tanggal --}}
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-slate-700 mb-2">Filter Tanggal</label>
                <form method="GET" action="{{ route('Resepsionis.TemuDokter.daftar-temu-dokter') }}" class="flex gap-2">
                    <select name="filter_tanggal" onchange="this.form.submit()" class="flex-1 rounded-md border border-slate-300 p-2 focus:ring-blue-500 focus:ring-2 focus:border-transparent">
                        <option value="hari_ini" {{ $filterTanggal === 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="semua" {{ $filterTanggal === 'semua' ? 'selected' : '' }}>Semua Tanggal</option>
                    </select>
                    <input type="hidden" name="filter_dokter" value="{{ $filterDokter }}">
                </form>
            </div>

            {{-- Filter Dokter --}}
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-slate-700 mb-2">Filter Dokter</label>
                <form method="GET" action="{{ route('Resepsionis.TemuDokter.daftar-temu-dokter') }}" class="flex gap-2">
                    <select name="filter_dokter" onchange="this.form.submit()" class="flex-1 rounded-md border border-slate-300 p-2 focus:ring-blue-500 focus:ring-2 focus:border-transparent">
                        <option value="">-- Semua Dokter --</option>
                        @foreach($dokterlist as $dokter)
                            <option value="{{ $dokter->idrole_user }}" {{ $filterDokter == $dokter->idrole_user ? 'selected' : '' }}>
                                {{ $dokter->nama ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="filter_tanggal" value="{{ $filterTanggal }}">
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">No Urut</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Pet/Pasien</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Pemilik</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Dokter</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse($temuDokterlist as $row)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-teal-100 text-teal-800 text-sm font-semibold rounded-full">
                                {{ $row->no_urut }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($row->waktu_daftar)->format('d-m-Y H:i') }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $row->pet_nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $row->pemilik_nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $row->dokter_nama ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($row->status === 'W')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                    Waiting
                                </span>
                            @elseif($row->status === 'D')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                    Done
                                </span>
                            @elseif($row->status === 'C')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                    Cancel
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($row->status === 'W')
                                <button
                                    command="show-modal"
                                    commandfor="modalCancel{{ $row->idreservasi_dokter }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600 transition shadow-sm hover:shadow">
                                    Batalkan
                                </button>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                            Tidak ada data reservasi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('Resepsionis.TemuDokter.create-temu-dokter')
@include('Resepsionis.TemuDokter.delete-temu-dokter')

@endsection

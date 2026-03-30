@extends('layouts.app')

@section('title', 'Detail Rekam Medis')

@section('content')

<div class="mb-4">
    <a href="{{ route('Dokter.RekamMedis.daftar-rekam-medis') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-800 hover:bg-gray-400 rounded-lg transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 gap-6">
    {{-- Header Card --}}
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 shadow-md text-white">
        <h1 class="text-3xl font-bold mb-2">Detail Rekam Medis</h1>
        <p class="text-blue-100">{{ \Carbon\Carbon::parse($rekam_medis->created_at)->format('d F Y - H:i') }}</p>
    </div>

    {{-- Pet & Dokter Info Card --}}
    <div class="bg-white rounded-lg p-6 shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Pemeriksaan</h2>
        
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600 mb-1">Pet/Pasien</p>
                <p class="text-2xl font-bold text-gray-800">{{ $rekam_medis->nama_pet }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600 mb-1">Pemilik</p>
                <p class="text-2xl font-bold text-gray-800">{{ $rekam_medis->pemilik_nama }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mt-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600 mb-1">Jenis Hewan</p>
                <p class="text-lg font-semibold text-gray-800">{{ $rekam_medis->nama_jenis_hewan ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600 mb-1">Ras Hewan</p>
                <p class="text-lg font-semibold text-gray-800">{{ $rekam_medis->nama_ras ?? '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mt-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600 mb-1">Tanggal Periksa</p>
                <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($rekam_medis->created_at)->format('d-m-Y') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600 mb-1">Jam Periksa</p>
                <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($rekam_medis->created_at)->format('H:i') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mt-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600 mb-1">Dokter Pemeriksa</p>
                <p class="text-lg font-semibold text-gray-800">{{ $rekam_medis->dokter_nama ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg p-6 shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Hasil Pemeriksaan & Tindakan</h2>
        
        <div class="space-y-4">
            {{-- Anamnesa --}}
            <div>
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Anamnesa (Riwayat Penyakit)</p>
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                    <p class="text-gray-800 leading-relaxed">{{ $rekam_medis->anamnesa ?? 'Tidak ada data' }}</p>
                </div>
            </div>

            {{-- Temuan Klinis --}}
            <div>
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Temuan Klinis</p>
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-green-500">
                    <p class="text-gray-800 leading-relaxed">{{ $rekam_medis->temuan_klinis ?? 'Tidak ada data' }}</p>
                </div>
            </div>

            {{-- Diagnosa --}}
            <div>
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Diagnosa</p>
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-500">
                    <p class="text-gray-800 leading-relaxed">{{ $rekam_medis->diagnosa ?? 'Tidak ada data' }}</p>
                </div>
            </div>

            {{-- Detail Separator --}}
            <div class="my-6 border-t-2 border-gray-300"></div>

            {{-- Detail Rekam Medis --}}
            @if($rekam_medis->detail_list && count($rekam_medis->detail_list) > 0)
                @php $detail = $rekam_medis->detail_list[0]; @endphp
                
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Detail</p>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-orange-500">
                        <p class="text-gray-800 leading-relaxed">{{ $detail->detail ?? 'Tidak ada data' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Tindakan</p>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-indigo-500">
                        <div class="flex items-start gap-3">
                            <span class="inline-block bg-indigo-600 text-white px-3 py-1 rounded text-sm font-bold">
                                {{ $detail->kode }}
                            </span>
                            <div>
                                <p class="text-gray-800 font-semibold">{{ $detail->deskripsi_tindakan_terapi }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons (hanya jika dokter ini adalah dokter_pemeriksa) --}}
                @if($current_idrole_user && $current_idrole_user == $rekam_medis->dokter_pemeriksa)
                    <div class="flex gap-2 mt-4">
                        <button 
                            command="show-modal"
                            commandfor="modalEdit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs font-semibold rounded-md transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                        <button
                            command="show-modal"
                            commandfor="modalDelete"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 text-xs font-semibold rounded-md transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                        </button>
                    </div>
                @endif
            @else
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Tindakan & Detail</p>
                    <div class="text-center py-6 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                        <svg class="w-10 h-10 mx-auto text-yellow-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-yellow-700 text-sm font-medium">Belum Diisi</p>
                    </div>
                </div>

                {{-- Button Tambah (hanya jika dokter ini adalah dokter_pemeriksa) --}}
                @if($current_idrole_user && $current_idrole_user == $rekam_medis->dokter_pemeriksa)
                    <div class="mt-4">
                        <button 
                            command="show-modal"
                            commandfor="modalCreate"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-lg transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Tindakan
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-4">
        <a href="{{ route('Dokter.RekamMedis.daftar-rekam-medis') }}" class="flex-1 inline-block text-center px-6 py-3 bg-gray-300 text-gray-800 hover:bg-gray-400 rounded-lg transition-colors font-semibold">
            Kembali ke Daftar
        </a>
    </div>
</div>

{{-- Create Modal --}}
@include('Dokter.RekamMedis.create-detail-rekam-medis')

{{-- Edit Modal --}}
@include('Dokter.RekamMedis.edit-detail-rekam-medis')

{{-- Delete Modal --}}
@include('Dokter.RekamMedis.delete-detail-rekam-medis')

@endsection

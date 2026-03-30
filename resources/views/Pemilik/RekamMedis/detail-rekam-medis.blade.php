@extends('layouts.app')

@section('title', 'Detail Rekam Medis')

@section('content')

<div class="mb-4">
    <a href="{{ route('Pemilik.RekamMedis.daftar-rekam-medis') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-800 hover:bg-gray-400 rounded-lg transition-colors font-medium">
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
                <p class="text-2xl font-bold text-gray-800">{{ $rekam_medis->pet_nama }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600 mb-1">Dokter Pemeriksa</p>
                <p class="text-2xl font-bold text-gray-800">{{ $rekam_medis->dokter_nama }}</p>
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
            @else
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Tindakan & Detail</p>
                    <div class="text-center py-6 bg-gray-50 rounded-lg">
                        <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Tidak ada tindakan yang tercatat</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-4">
        <a href="{{ route('Pemilik.RekamMedis.daftar-rekam-medis') }}" class="flex-1 inline-block text-center px-6 py-3 bg-gray-300 text-gray-800 hover:bg-gray-400 rounded-lg transition-colors font-semibold">
            Kembali ke Daftar
        </a>
    </div>
</div>

@endsection

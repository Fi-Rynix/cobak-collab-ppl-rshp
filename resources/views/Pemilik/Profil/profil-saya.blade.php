@extends('layouts.app')

@section('title', 'Profil Saya - Pemilik')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Profil Saya</h1>
</div>

{{-- Informasi Profil --}}
<div class="bg-white rounded-lg shadow-md border border-slate-200 mb-6">
    
    <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Informasi Pribadi</h2>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-slate-600 font-medium">Nama</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">{{ $user->nama }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 font-medium">Email</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 font-medium">No. WhatsApp</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">{{ $pemilik->no_wa ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 font-medium">Alamat</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">{{ $pemilik->alamat ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Daftar Pet --}}
<div class="bg-white rounded-lg shadow-md border border-slate-200">
    
    <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Pet Saya</h2>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama Pet</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Jenis Hewan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Ras Hewan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Tanggal Lahir</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Jenis Kelamin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($pet_list as $index => $pet)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-4 font-medium">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $pet->nama }}</td>
                        <td class="px-6 py-4">{{ $pet->jenis_hewan_nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $pet->ras_hewan_nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $pet->tanggal_lahir ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($pet->jenis_kelamin == 'J')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                    Jantan
                                </span>
                            @elseif($pet->jenis_kelamin == 'B')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-pink-100 text-pink-800 text-xs font-semibold rounded-full">
                                    Betina
                                </span>
                            @else
                                <span class="text-slate-500 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            Belum ada pet terdaftar
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

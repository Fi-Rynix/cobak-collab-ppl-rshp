@extends('layouts.app')

@section('title', 'Profil Saya - Perawat')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Profil Saya</h1>
</div>

{{-- Informasi Profil --}}
<div class="bg-white rounded-lg shadow-md border border-slate-200">
    
    <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Informasi Pribadi</h2>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-slate-600 font-medium">Nama</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">{{ $user->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 font-medium">Jenis Kelamin</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">
                    @if($perawat->jenis_kelamin == 'L')
                        Laki-laki
                    @elseif($perawat->jenis_kelamin == 'P')
                        Perempuan
                    @else
                        -
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-slate-600 font-medium">Email</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">{{ $user->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 font-medium">No. HP</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">{{ $perawat->no_hp ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 font-medium">Alamat</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">{{ $perawat->alamat ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600 font-medium">Pendidikan</p>
                <p class="text-lg text-slate-800 mt-1 font-semibold">{{ $perawat->pendidikan ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

@endsection

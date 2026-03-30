@extends('layouts.app')

@section('title', 'Dashboard Dokter')

@section('content')

<div class="grid grid-cols-1 gap-6">
    <div class="bg-gradient-to-r from-green-500 to-green-400 rounded-lg p-8 text-white shadow-md">
        <h1 class="text-3xl font-bold mb-2">Selamat datang, {{ Auth::user()->nama }}!</h1>
        <p class="text-green-100">Anda login sebagai <span class="font-semibold">{{ session('role') }}</span></p>
    </div>
</div>

@endsection
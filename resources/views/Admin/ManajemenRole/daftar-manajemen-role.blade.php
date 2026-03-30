@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Manajemen Role User</h1>
</div>

<div class="bg-white rounded-lg shadow-md border border-slate-200">
    
    <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Daftar User & Role</h2>
    </div>

    {{-- Tabel --}}

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Nama User</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Role Aktif</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @foreach($userlist as $row)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $row->nama }}</td>
                        <td class="px-6 py-4">{{ $row->email }}</td>
                        <td class="px-6 py-4">
                            @if($row->roleUsers->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach($row->roleUsers as $roleUser)
                                        @if($roleUser->role)
                                            @if($roleUser->status == 1)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                                    ✓ {{ $roleUser->role->nama_role }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-200 text-gray-700 text-xs font-medium rounded-full">
                                                    {{ $roleUser->role->nama_role }}
                                                </span>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-500 text-sm">Tidak ada role</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <button
                                command="show-modal"
                                commandfor="modalEdit-{{ $row->iduser }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5
                                    bg-teal-500 text-white text-sm font-medium
                                    rounded-md hover:bg-teal-600 transition shadow-sm hover:shadow">
                                Ubah Role
                            </button>
                        </td>
                    </tr>

                    @include('Admin.ManajemenRole.edit-manajemen-role', ['user' => $row])

                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection


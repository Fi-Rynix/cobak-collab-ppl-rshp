<div
    class="bg-gradient-to-b from-slate-50 to-slate-100 border-r border-slate-200 h-full flex flex-col transition-all duration-300 shadow-lg"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
    x-data="{
        openDropdowns: {},
        activeItem: null,
        sidebarOpen: true,
        setActive(item) {
            this.activeItem = item;
        },
        toggleDropdown(key) {
            this.openDropdowns[key] = !this.openDropdowns[key];
            if (this.openDropdowns[key]) {
                Object.keys(this.openDropdowns).forEach(k => {
                    if (k !== key) {
                        this.openDropdowns[k] = false;
                    }
                });
            }
        },
        initActiveItem() {
            @php
                $idrole = session('idrole');
            @endphp

            @if (Route::is('Admin.dashboard-admin'))
                this.activeItem = 'dashboard';
            @elseif ($idrole == 4 && Route::is('Resepsionis.dashboard-resepsionis'))
                this.activeItem = 'dashboard';
            @elseif ($idrole == 3 && Route::is('Perawat.dashboard-perawat'))
                this.activeItem = 'dashboard';
            @elseif ($idrole == 2 && Route::is('Dokter.dashboard-dokter'))
                this.activeItem = 'dashboard';
            @elseif ($idrole == 5 && Route::is('Pemilik.dashboard-pemilik'))
                this.activeItem = 'dashboard';
            {{-- Admin routes --}}
            @elseif ($idrole == 1 && Route::is('Admin.TindakanTerapi.daftar-tindakan-terapi', 'Admin.Kategori.daftar-kategori', 'Admin.KategoriKlinis.daftar-kategori-klinis', 'Admin.JenisHewan.daftar-jenis-hewan', 'Admin.RasHewan.daftar-ras-hewan'))
                this.openDropdowns['dataMaster'] = true;
                @if (Route::is('Admin.TindakanTerapi.daftar-tindakan-terapi'))
                    this.activeItem = 'kodeTindakanTerapi';
                @elseif (Route::is('Admin.Kategori.daftar-kategori'))
                    this.activeItem = 'kategori';
                @elseif (Route::is('Admin.KategoriKlinis.daftar-kategori-klinis'))
                    this.activeItem = 'kategoriKlinis';
                @elseif (Route::is('Admin.JenisHewan.daftar-jenis-hewan'))
                    this.activeItem = 'jenisHewan';
                @elseif (Route::is('Admin.RasHewan.daftar-ras-hewan'))
                    this.activeItem = 'rasHewan';
                @endif
            @elseif ($idrole == 1 && Route::is('Admin.User.daftar-user', 'Admin.Pemilik.daftar-pemilik', 'Admin.Pet.daftar-pet', 'Admin.Perawat.daftar-perawat', 'Admin.Dokter.daftar-dokter', 'Admin.ManajemenRole.daftar-manajemen-role'))
                this.openDropdowns['dataUser'] = true;
                @if (Route::is('Admin.User.daftar-user'))
                    this.activeItem = 'user';
                @elseif (Route::is('Admin.Pemilik.daftar-pemilik'))
                    this.activeItem = 'pemilik';
                @elseif (Route::is('Admin.Pet.daftar-pet'))
                    this.activeItem = 'petPasien';
                @elseif (Route::is('Admin.Perawat.daftar-perawat'))
                    this.activeItem = 'perawat';
                @elseif (Route::is('Admin.Dokter.daftar-dokter'))
                    this.activeItem = 'dokter';
                @elseif (Route::is('Admin.ManajemenRole.daftar-manajemen-role'))
                    this.activeItem = 'manajemenRole';
                @endif
            @elseif ($idrole == 1 && Route::is('Admin.TemuDokter.daftar-temu-dokter', 'Admin.RekamMedis.daftar-rekam-medis'))
                this.openDropdowns['transactional'] = true;
                @if (Route::is('Admin.TemuDokter.daftar-temu-dokter'))
                    this.activeItem = 'temuDokter';
                @elseif (Route::is('Admin.RekamMedis.daftar-rekam-medis'))
                    this.activeItem = 'rekamMedis';
                @endif
            {{-- Resepsionis routes --}}
            @elseif ($idrole == 4 && Route::is('Resepsionis.Pemilik.daftar-pemilik', 'Resepsionis.Pet.daftar-pet'))
                this.openDropdowns['pasien'] = true;
                @if (Route::is('Resepsionis.Pemilik.daftar-pemilik'))
                    this.activeItem = 'resPemilik';
                @elseif (Route::is('Resepsionis.Pet.daftar-pet'))
                    this.activeItem = 'resPet';
                @endif
            @elseif ($idrole == 4 && Route::is('Resepsionis.TemuDokter.daftar-temu-dokter'))
                this.activeItem = 'resReservasi';
            {{-- Perawat routes --}}
            @elseif ($idrole == 3 && Route::is('Perawat.RekamMedis.daftar-rekam-medis'))
                this.activeItem = 'perawatRekamMedis';
            @elseif ($idrole == 3 && Route::is('Perawat.Profil.profil-saya'))
                this.activeItem = 'perawatProfil';
            {{-- Dokter routes --}}
            @elseif ($idrole == 2 && Route::is('Dokter.RekamMedis.daftar-rekam-medis'))
                this.activeItem = 'dokterRekamMedis';
            @elseif ($idrole == 2 && Route::is('Dokter.Profil.profil-saya'))
                this.activeItem = 'dokterProfil';
            {{-- Pemilik routes --}}
            @elseif ($idrole == 5 && Route::is('Pemilik.TemuDokter.daftar-reservasi-saya'))
                this.activeItem = 'pemilikReservasi';
            @elseif ($idrole == 5 && Route::is('Pemilik.RekamMedis.daftar-rekam-medis'))
                this.activeItem = 'pemilikRekamMedis';
            @elseif ($idrole == 5 && Route::is('Pemilik.Profil.profil-saya'))
                this.activeItem = 'pemilikProfil';
            @endif
        }
    }"
    @click="if (!sidebarOpen) { Object.keys(openDropdowns).forEach(k => openDropdowns[k] = false); }"
    @mouseenter="initActiveItem()"
>

    {{-- TOP BAR --}}
    <div class="h-20 flex items-center justify-center px-4 gap-3 border-b border-slate-200 bg-white relative">

        {{-- Toggle Button (selalu di tengah) --}}
        <button 
            @click="sidebarOpen = !sidebarOpen; if (!sidebarOpen) { Object.keys(openDropdowns).forEach(k => openDropdowns[k] = false); }"
            class="text-slate-600 hover:text-blue-600 transition-colors duration-200 focus:outline-none"
        >
            <svg class="w-12 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        {{-- Logo muncul hanya ketika open --}}
        <div x-show="sidebarOpen" class="flex items-center flex-1" x-transition>
            <img src="{{ asset('assets/logounerpanjang.png') }}"
                 alt="RSHP"
                 class="h-8 ml-2">
        </div>

    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 mt-2 px-3 space-y-1 overflow-y-auto">

        @php
            $idrole = session('idrole');
        @endphp

        {{-- Dashboard untuk semua role --}}
        <a href="@if ($idrole == 1) {{ route('Admin.dashboard-admin') }} @elseif ($idrole == 4) {{ route('Resepsionis.dashboard-resepsionis') }} @elseif ($idrole == 3) {{ route('Perawat.dashboard-perawat') }} @elseif ($idrole == 2) {{ route('Dokter.dashboard-dokter') }} @elseif ($idrole == 5) {{ route('Pemilik.dashboard-pemilik') }} @endif"
            @click="setActive('dashboard')"
            class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
            :class="activeItem === 'dashboard' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
            </svg>
            <span x-show="sidebarOpen" x-transition class="font-medium text-sm">Dashboard</span>
        </a>

        {{-- ADMIN MENU --}}
        @if ($idrole == 1)

            {{-- Data Master Dropdown --}}
            <div>
                <button
                    @click="toggleDropdown('dataMaster'); setActive('dataMaster'); if (!sidebarOpen) { sidebarOpen = true; }"
                    class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                    :class="activeItem === 'dataMaster' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="font-medium text-sm flex-1 text-left">Data Master</span>
                    <div x-show="sidebarOpen" x-transition class="w-2 h-1 flex-shrink-0" style="clip-path: polygon(0% 100%, 50% 0%, 100% 100%); background-color: currentColor; transform: rotate(180deg);"></div>
                </button>
                
                {{-- Dropdown Items --}}
                <div x-show="openDropdowns['dataMaster']" x-transition class="mt-1 ml-4 space-y-1">
                    <a href="{{ route('Admin.TindakanTerapi.daftar-tindakan-terapi') }}" @click="setActive('kodeTindakanTerapi')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'kodeTindakanTerapi' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'kodeTindakanTerapi' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Kode Tindakan Terapi</span>
                    </a>
                    <a href="{{ route('Admin.Kategori.daftar-kategori') }}" @click="setActive('kategori')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'kategori' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'kategori' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Kategori</span>
                    </a>
                    <a href="{{ route('Admin.KategoriKlinis.daftar-kategori-klinis') }}" @click="setActive('kategoriKlinis')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'kategoriKlinis' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'kategoriKlinis' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Kategori Klinis</span>
                    </a>
                    <a href="{{ route('Admin.JenisHewan.daftar-jenis-hewan') }}" @click="setActive('jenisHewan')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'jenisHewan' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'jenisHewan' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Jenis Hewan</span>
                    </a>
                    <a href="{{ route('Admin.RasHewan.daftar-ras-hewan') }}" @click="setActive('rasHewan')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'rasHewan' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'rasHewan' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Ras Hewan</span>
                    </a>
                </div>
            </div>

            {{-- Data User Dropdown --}}
            <div>
                <button
                    @click="toggleDropdown('dataUser'); setActive('dataUser'); if (!sidebarOpen) { sidebarOpen = true; }"
                    class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                    :class="activeItem === 'dataUser' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="font-medium text-sm flex-1 text-left">Data User</span>
                    <div x-show="sidebarOpen" x-transition class="w-2 h-1 flex-shrink-0" style="clip-path: polygon(0% 100%, 50% 0%, 100% 100%); background-color: currentColor; transform: rotate(180deg);"></div>
                </button>
                
                {{-- Dropdown Items --}}
                <div x-show="openDropdowns['dataUser']" x-transition class="mt-1 ml-4 space-y-1">
                    <a href="{{ route('Admin.User.daftar-user') }}" @click="setActive('user')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'user' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'user' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">User</span>
                    </a>
                    <a href="{{ route('Admin.Pemilik.daftar-pemilik') }}" @click="setActive('pemilik')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'pemilik' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'pemilik' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Pemilik</span>
                    </a>
                    <a href="{{ route('Admin.Pet.daftar-pet') }}" @click="setActive('petPasien')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'petPasien' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'petPasien' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Pet (Pasien)</span>
                    </a>
                    <a href="{{ route('Admin.Perawat.daftar-perawat') }}" @click="setActive('perawat')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'perawat' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'perawat' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Perawat</span>
                    </a>
                    <a href="{{ route('Admin.Dokter.daftar-dokter') }}" @click="setActive('dokter')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'dokter' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'dokter' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Dokter</span>
                    </a>
                    <a href="{{ route('Admin.ManajemenRole.daftar-manajemen-role') }}" @click="setActive('manajemenRole')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'manajemenRole' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'manajemenRole' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Manajemen Role</span>
                    </a>
                </div>
            </div>

            {{-- Transactional Dropdown --}}
            <div>
                <button
                    @click="toggleDropdown('transactional'); setActive('transactional'); if (!sidebarOpen) { sidebarOpen = true; }"
                    class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                    :class="activeItem === 'transactional' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="font-medium text-sm flex-1 text-left">Transactional</span>
                    <div x-show="sidebarOpen" x-transition class="w-2 h-1 flex-shrink-0" style="clip-path: polygon(0% 100%, 50% 0%, 100% 100%); background-color: currentColor; transform: rotate(180deg);"></div>
                </button>
                
                {{-- Dropdown Items --}}
                <div x-show="openDropdowns['transactional']" x-transition class="mt-1 ml-4 space-y-1">
                    <a href="{{ route('Admin.TemuDokter.daftar-temu-dokter') }}" @click="setActive('temuDokter')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'temuDokter' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'temuDokter' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Temu Dokter</span>
                    </a>
                    <a href="{{ route('Admin.RekamMedis.daftar-rekam-medis') }}" @click="setActive('rekamMedis')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'rekamMedis' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'rekamMedis' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Rekam Medis</span>
                    </a>
                </div>
            </div>

        {{-- RESEPSIONIS MENU (idrole 4) --}}
        @elseif ($idrole == 4)

            {{-- Pasien Dropdown --}}
            <div>
                <button
                    @click="toggleDropdown('pasien'); setActive('pasien'); if (!sidebarOpen) { sidebarOpen = true; }"
                    class="w-full flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                    :class="activeItem === 'pasien' || activeItem === 'resPemilik' || activeItem === 'resPet' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM4 20h16a2 2 0 002-2v-2a6 6 0 00-12 0v2a2 2 0 002 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="font-medium text-sm flex-1 text-left">Pasien</span>
                    <div x-show="sidebarOpen" x-transition class="w-2 h-1 flex-shrink-0" style="clip-path: polygon(0% 100%, 50% 0%, 100% 100%); background-color: currentColor; transform: rotate(180deg);"></div>
                </button>
                
                {{-- Dropdown Items --}}
                <div x-show="openDropdowns['pasien']" x-transition class="mt-1 ml-4 space-y-1">
                    <a href="{{ route('Resepsionis.Pemilik.daftar-pemilik') }}" @click="setActive('resPemilik')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'resPemilik' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'resPemilik' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Pemilik</span>
                    </a>
                    <a href="{{ route('Resepsionis.Pet.daftar-pet') }}" @click="setActive('resPet')" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                        :class="activeItem === 'resPet' ? 'bg-blue-100 text-blue-700 font-medium' : ''">
                        <span class="w-1.5 h-1.5 rounded-full" :class="activeItem === 'resPet' ? 'bg-blue-600' : 'bg-blue-400'"></span>
                        <span x-show="sidebarOpen">Pet</span>
                    </a>
                </div>
            </div>

            {{-- Reservasi Temu Dokter --}}
            <a href="{{ route('Resepsionis.TemuDokter.daftar-temu-dokter') }}"
                @click="setActive('resReservasi')"
                class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                :class="activeItem === 'resReservasi' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition class="font-medium text-sm">Reservasi Temu Dokter</span>
            </a>

        {{-- PERAWAT MENU (idrole 3) --}}
        @elseif ($idrole == 3)

            {{-- Rekam Medis --}}
            <a href="{{ route('Perawat.RekamMedis.daftar-rekam-medis') }}"
                @click="setActive('perawatRekamMedis')"
                class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                :class="activeItem === 'perawatRekamMedis' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition class="font-medium text-sm">Rekam Medis</span>
            </a>

            {{-- Profil Saya --}}
            <a href="{{ route('Perawat.Profil.profil-saya') }}"
                @click="setActive('perawatProfil')"
                class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                :class="activeItem === 'perawatProfil' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition class="font-medium text-sm">Profil Saya</span>
            </a>

        {{-- DOKTER MENU (idrole 2) --}}
        @elseif ($idrole == 2)

            {{-- Rekam Medis --}}
            <a href="{{ route('Dokter.RekamMedis.daftar-rekam-medis') }}"
                @click="setActive('dokterRekamMedis')"
                class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                :class="activeItem === 'dokterRekamMedis' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition class="font-medium text-sm">Rekam Medis</span>
            </a>

            {{-- Profil Saya --}}
            <a href="{{ route('Dokter.Profil.profil-saya') }}"
                @click="setActive('dokterProfil')"
                class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                :class="activeItem === 'dokterProfil' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition class="font-medium text-sm">Profil Saya</span>
            </a>

        {{-- PEMILIK MENU (idrole 5) --}}
        @elseif ($idrole == 5)

            {{-- Reservasi Saya --}}
            <a href="{{ route('Pemilik.TemuDokter.daftar-reservasi-saya') }}"
                @click="setActive('pemilikReservasi')"
                class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                :class="activeItem === 'pemilikReservasi' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition class="font-medium text-sm">Reservasi Saya</span>
            </a>

            {{-- Rekam Medis --}}
            <a href="{{ route('Pemilik.RekamMedis.daftar-rekam-medis') }}"
                @click="setActive('pemilikRekamMedis')"
                class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                :class="activeItem === 'pemilikRekamMedis' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition class="font-medium text-sm">Rekam Medis</span>
            </a>

            {{-- Profil Saya --}}
            <a href="{{ route('Pemilik.Profil.profil-saya') }}"
                @click="setActive('pemilikProfil')"
                class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:shadow-sm group"
                :class="activeItem === 'pemilikProfil' ? 'bg-blue-100 text-blue-700 shadow-sm' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span x-show="sidebarOpen" x-transition class="font-medium text-sm">Profil Saya</span>
            </a>

        @endif

    </nav>

</div>

<script>
    // Panggil initActiveItem saat Alpine selesai load
    document.addEventListener('alpine:init', () => {
        const sidebar = document.querySelector('[x-data*="initActiveItem"]');
        if (sidebar && sidebar.__x) {
            sidebar.__x.$data.initActiveItem();
        }
    });

    // Fallback jika alpine:init tidak dipanggil
    setTimeout(() => {
        const sidebar = document.querySelector('[x-data*="initActiveItem"]');
        if (sidebar && sidebar.__x && !sidebar.__x.$data.activeItem) {
            sidebar.__x.$data.initActiveItem();
        }
    }, 100);
</script>
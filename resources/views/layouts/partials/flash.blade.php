    {{-- Flash Message Modern Top Center --}}
@if (session('success'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 5000)"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="-translate-y-10 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transform transition ease-in duration-300"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-10 opacity-0"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] flex items-center gap-3 px-5 py-3 bg-green-600 text-white rounded-xl shadow-lg"
    >
        {{-- Icon --}}
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>

        {{-- Pesan --}}
        <span class="font-medium">{{ session('success') }}</span>

        {{-- Close button --}}
        <button type="button" @click="show = false" class="ml-auto text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
@elseif (session('error'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 5000)"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="-translate-y-10 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transform transition ease-in duration-300"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-10 opacity-0"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] flex items-center gap-3 px-5 py-3 bg-red-600 text-white rounded-xl shadow-lg"
    >
        {{-- Icon --}}
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.535-2.636 3.535L16 3.536H13.864L14.5 7.071L15.5 6.071L19 7z"></path>
        </svg>

        {{-- Pesan --}}
        <span class="font-medium">{{ session('error') }}</span>

        {{-- Close button --}}
        <button type="button" @click="show = false" class="ml-auto text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
@endif
{{-- End Flash Message Modern Top Center --}}
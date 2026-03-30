<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RSHP')</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100"
    x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        @include('layouts.partials.sidebar')

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col overflow-hidden">

        @include('layouts.partials.flash')

            {{-- HEADER --}}
            @include('layouts.partials.header')

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- AlpineJS --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</body>
</html>

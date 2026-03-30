<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RSHP</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Card Container --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="mb-4">
                    <img src="{{ asset('assets/logogabungan.png') }}" alt="RSHP" class="h-10 mx-auto">
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Login RSHP</h1>
                <p class="text-gray-500 mt-2">Masuk ke akun Anda</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email Input --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 transition-colors duration-200 @error('email') border-red-500 @enderror"
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email" 
                        autofocus
                        placeholder="Masukkan email Anda">
                    @error('email')
                        <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18.101 12.93a.75.75 0 00-1.06-1.06L10.75 17.19l-3.19-3.19a.75.75 0 00-1.06 1.06l3.72 3.72a.75.75 0 001.06 0l7.72-7.72z" clip-rule="evenodd"/>
                            </svg>
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input 
                        id="password" 
                        type="password" 
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 transition-colors duration-200 @error('password') border-red-500 @enderror"
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="Masukkan password Anda">
                    @error('password')
                        <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18.101 12.93a.75.75 0 00-1.06-1.06L10.75 17.19l-3.19-3.19a.75.75 0 00-1.06 1.06l3.72 3.72a.75.75 0 001.06 0l7.72-7.72z" clip-rule="evenodd"/>
                            </svg>
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>

                {{-- Remember Me Checkbox --}}
                <div class="flex items-center">
                    <input 
                        id="remember" 
                        type="checkbox" 
                        class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
                        name="remember" 
                        {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="ml-3 text-sm text-gray-600 cursor-pointer">
                        Remember me
                    </label>
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg active:scale-95">
                    Login
                </button>

        {{-- Footer --}}
        <div class="text-center mt-8 text-gray-600 text-sm">
            <p>© 2025 RSHP. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>

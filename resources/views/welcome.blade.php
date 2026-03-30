<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang | Rumah Sakit Hewan Pendidikan</title>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body class="welcome-page">
    <div class="welcome-container">
        <img src="{{ asset('assets/logorshp.png') }}" alt="Logo RSHP" class="logo">

        <h1>Selamat Datang</h1>
        <h2>di Rumah Sakit Hewan Pendidikan</h2>
        <h3>Universitas Airlangga</h3>

        <a href="{{ route('rshp') }}" class="enter-btn">Masuk ke Halaman Utama</a>
    </div>
</body>
</html>

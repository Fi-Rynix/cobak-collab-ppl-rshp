<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Remake RSHP Unair</title>
    <link rel="stylesheet" href="{{ asset('CSS/RSHP.css') }}">
  </head>

  <body>
    @include('MainSite.navbar-rshp')
    <header>
      <img src="{{ asset('assets/logogabungan.png') }}" alt="logo_gabungan">
    </header>
    <div class="bar-kuning"></div>

    <div class="container">
      <div class="konten-kiri">
        <h1 id="home">Selamat Datang di <span style="color:#ffd600;"><br>RSHP Universitas Airlangga</span></h1>
        <button class="btn-kuning">PENDAFTARAN ONLINE</button>
        <p>Rumah Sakit Hewan Pendidikan Universitas Airlangga berinovasi untuk selalu meningkatkan kualitas pelayanan, maka dari itu Rumah Sakit Hewan Pendidikan Universitas Airlangga mempunyai fitur pendaftaran online yang mempermudah untuk mendaftarkan hewan kesayangan anda</p>
        <button class="btn-biru">KONSULTASI ONLINE</button>
      </div>
      <div class="konten-kanan">
        <iframe
          src="https://www.youtube.com/embed/rCfvZPECZvE">
        </iframe>
      </div>
    </div>

    <div class="container-berita">
      <h2 style="text-align: center;">Berita Terkini</h2>
      <div class="berita-list">
        <div class="berita-item">
          <img src="{{asset('assets/kucingkecil.jpg')}}" alt="kucingkecil">
          <h3>Tips Merawat Kucing Kecil</h3>
          <p>Pelajari cara merawat kucing kecil kesayangan Anda agar tetap sehat dan aktif setiap hari</p>
        </div>
        <div class="berita-item">
          <img src="{{asset('assets/kucingbesar.jpg')}}" alt="kucingbesar">
          <h3>Tips Merawat Kucing Besar</h3>
          <p>Pelajari cara merawat kucing besar kesayangan Anda agar tetap sehat dan aktif setiap hari</p>
        </div>
        <div class="berita-item">
          <img src="{{ asset('assets/kucingbesarbanget.jpg') }}" alt="kucingbesarbanget">
          <h3>Tips Merawat Kucing Besar Banget</h3>
          <p>Pelajari cara merawat kucing besar banget kesayangan Anda agar tetap sehat dan aktif setiap hari</p>
        </div>
      </div>
    </div>

    <footer class="footer">
      <table style="width:100%;">
        <tr>
          <td style="text-align:left; padding-left:40px;">
            &copy; 2024 Universitas Airlangga. All Rights Reserved
          </td>
          <td style="text-align:right; padding-right:40px;">
            <b>RUMAH SAKIT HEWAN PENDIDIKAN</b><br>
            GEDUNG RS HEWAN PENDIDIKAN<br>
            rshp@fkh.unair.ac.id<br>
            Telp : 031 5927832<br>
            Kampus C Universitas Airlangga<br>
            Surabaya 60115, Jawa Timur
          </td>
        </tr>
      </table>
    </footer>

  </body>
</html>
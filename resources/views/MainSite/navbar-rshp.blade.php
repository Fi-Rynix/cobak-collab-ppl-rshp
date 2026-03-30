<!DOCTYPE html>
<html>
  <head>
    <style>
      nav {
        background: #1436a3;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 50px;
        width: 100%;
      }
      nav ul {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        gap: 20px;
        align-items: center;
        justify-content: center;
      }
      nav ul li {
        margin: 0;
      }
      nav ul li a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        font-size: 15px;
        padding: 10px 18px;
        display: block;
        border-radius: 4px;
      }
      nav ul li a:hover {
        color: #ffd600;
        background: rgba(255,255,255,0.08);
      }
    </style>
  </head>

  <body>
    <header>
      <nav>
        <ul>
          <li><a href="{{ route('rshp') }}">Home</a></li>
          <li><a href="{{ route('struktur-organisasi') }}">Struktur Organisasi</a></li>
          <li><a href="{{ route('layanan-umum') }}">Layanan Umum</a></li>
          <li><a href="#visimisi">Visi, Misi & Tujuan</a></li>
          <li><a href="{{ route('login') }}">Login</a></li>
        </ul>
      </nav>
    </header>
  </body>
</html>
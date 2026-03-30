<!DOCTYPE html>
<html>
<body style="text-align:center; align-items: center; font-family: Arial; font-weight: bold;">
    @include('MainSite.navbar-rshp')
    <table style="margin: auto;">
        <tr>
            <td style="width:80px; text-align:center;">
                <img src="{{ asset('assets/logorshp.png') }}" width="80">
            </td>
            <td style="text-align:center;">
                <h3 style="margin: 0 20px;">
                    STRUKTUR PIMPINAN<br>
                    RUMAH SAKIT HEWAN PENDIDIKAN<br>
                    UNIVERSITAS AIRLANGGA
                </h3>
            </td>
            <td style="width:80px; text-align:center;">
                <img src="{{ asset('assets/logouner.png') }}" width="80">
            </td>
        </tr>
    </table>

    <h4>DIREKTUR</h4>
    <img src="{{ asset('assets/direktur.jpg')}}" width="150"><br>
    <p>Dr, Ira Sari Yudaniayanti, M.P., drh.</p>

    <table style="margin: auto;">
        <tr>
            <td style="padding: 20px;">
                <h4>WAKIL DIREKTUR 1</h4>
                <p>PELAYANAN MEDIS, PENDIDIKAN DAN <br> PENELITIAN</p>
                <img src="{{asset('assets/wadir1.jpg')}}" width="150"><br>
                <p>Dr. Nusidanto Triakso, M.P., drh.</p>
            </td>
            <td style="padding: 20px;">
                <h4>WAKIL DIREKTUR 2</h4>
                <p>SUMBER DAYA MANUSIA, <br> SARANA PRASARANA DAN KEUANGAN</p>
                <img src="{{asset('assets/wadir2.jpg')}}" width="150"><br>
                <p>Dr. Milyua Soneta S, M.Vet., drh.</p>
            </td>
        </tr>
    </table>

    <table style="margin: auto; background-color: rgb(0, 110, 255); color: yellow; font-size: 32px; -webkit-text-stroke: 2px black; font-weight: bold; width: 100%; height: 70px;">
        <tr>
            <td style="text-align: center; letter-spacing: 12px;">
                SMART SERVICES
            </td>
        </tr>
    </table>

</body>
</html>

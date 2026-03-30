<?php

namespace App\Http\Controllers\Resepsionis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TemuDokterResepsionis_Controller extends Controller
{
    // validation & helper
    protected function validate_temu_dokter(Request $request)
    {
        return $request->validate([
            'idpet' => ['required', 'exists:pet,idpet'],
            'idrole_user' => ['required', 'exists:role_user,idrole_user'],
        ], [
            'idpet.required' => 'Pet/Pasien wajib dipilih.',
            'idpet.exists' => 'Pet/Pasien tidak valid.',
            'idrole_user.required' => 'Dokter wajib dipilih.',
            'idrole_user.exists' => 'Dokter tidak valid.',
        ]);
    }

    protected function generate_nomor_urut($idrole_user)
    {
        $today = Carbon::today()->toDateString();
        $latestNomorUrut = DB::table('temu_dokter')
            ->where('idrole_user', $idrole_user)
            ->whereDate('waktu_daftar', $today)
            ->max('no_urut');
        return ($latestNomorUrut ?? 0) + 1;
    }

    // method
    public function daftar_temu_dokter(Request $request)
    {
        $filterTanggal = $request->input('filter_tanggal', 'hari_ini');
        $filterDokter = $request->input('filter_dokter', null);

        $query = DB::table('temu_dokter')
            ->leftJoin('pet', 'temu_dokter.idpet', '=', 'pet.idpet')
            ->leftJoin('pemilik', 'pet.idpemilik', '=', 'pemilik.idpemilik')
            ->leftJoin('user as user_pemilik', 'pemilik.iduser', '=', 'user_pemilik.iduser')
            ->leftJoin('ras_hewan', 'pet.idras_hewan', '=', 'ras_hewan.idras_hewan')
            ->leftJoin('role_user', 'temu_dokter.idrole_user', '=', 'role_user.idrole_user')
            ->leftJoin('user as user_dokter', 'role_user.iduser', '=', 'user_dokter.iduser')
            ->whereNull('temu_dokter.deleted_at');

        if ($filterTanggal === 'hari_ini') {
            $query->whereDate('temu_dokter.waktu_daftar', Carbon::today());
        }

        if ($filterDokter) {
            $query->where('temu_dokter.idrole_user', $filterDokter);
        }

        $temuDokterlist = $query
            ->select(
                'temu_dokter.*',
                'pet.nama as pet_nama',
                'ras_hewan.nama_ras',
                'user_pemilik.nama as pemilik_nama',
                'user_dokter.nama as dokter_nama'
            )
            ->orderBy('temu_dokter.waktu_daftar', 'desc')
            ->get();

        $dokterlist = DB::table('role_user')
            ->leftJoin('user', 'role_user.iduser', '=', 'user.iduser')
            ->where('role_user.idrole', 2)
            ->where('role_user.status', 1)
            ->whereNull('user.deleted_at')
            ->select('role_user.idrole_user', 'user.nama')
            ->orderBy('user.nama')
            ->get();

        return view('Resepsionis.TemuDokter.daftar-temu-dokter', compact('temuDokterlist', 'dokterlist', 'filterTanggal', 'filterDokter'));
    }

    public function store_temu_dokter(Request $request)
    {
        $validated = $this->validate_temu_dokter($request);

        $nomorUrut = $this->generate_nomor_urut($validated['idrole_user']);

        DB::table('temu_dokter')->insert([
            'idpet' => $validated['idpet'],
            'idrole_user' => $validated['idrole_user'],
            'no_urut' => $nomorUrut,
            'status' => 'W',
            'waktu_daftar' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('Resepsionis.TemuDokter.daftar-temu-dokter')
            ->with('success', 'Reservasi dokter berhasil dibuat.');
    }

    public function cancel_temu_dokter($idreservasi_dokter)
    {
        $iduser = session('iduser');

        DB::table('temu_dokter')
            ->where('idreservasi_dokter', $idreservasi_dokter)
            ->update([
                'deleted_at' => now(),
                'deleted_by' => $iduser,
            ]);

        return redirect()
            ->route('Resepsionis.TemuDokter.daftar-temu-dokter')
            ->with('success', 'Reservasi dokter berhasil dibatalkan.');
    }
}
//cihuy
?>

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TemuDokter;
use App\Models\Pet;
use App\Models\RoleUser;
use App\Models\Dokter;
use Carbon\Carbon;

class TemuDokter_Controller extends Controller
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
        $latestNomorUrut = TemuDokter::where('idrole_user', $idrole_user)
            ->max('no_urut');
        return ($latestNomorUrut ?? 0) + 1;
    }



    // method
    public function daftar_temu_dokter(Request $request)
    {
        $filterTanggal = $request->input('filter_tanggal', 'hari_ini');
        $filterDokter = $request->input('filter_dokter', null);

        $query = TemuDokter::whereNull('deleted_at')->with(['pet.pemilik.user', 'pet.rasHewan', 'roleUser.user']);

        if ($filterTanggal === 'hari_ini') {
            $query->whereDate('waktu_daftar', Carbon::today());
        }

        if ($filterDokter) {
            $query->where('idrole_user', $filterDokter);
        }

        $temuDokterlist = $query->orderBy('waktu_daftar', 'desc')->get();

        $dokterlist = RoleUser::where('idrole', 2)
            ->where('status', 1)
            ->with('user')
            ->get();

        return view('Admin.TemuDokter.daftar-temu-dokter', compact('temuDokterlist', 'dokterlist', 'filterTanggal', 'filterDokter'));
    }

    public function store_temu_dokter(Request $request)
    {
        $validated = $this->validate_temu_dokter($request);

        $nomorUrut = $this->generate_nomor_urut($validated['idrole_user']);

        TemuDokter::create([
            'idpet' => $validated['idpet'],
            'idrole_user' => $validated['idrole_user'],
            'no_urut' => $nomorUrut,
            'status' => 'W',
        ]);

        return redirect()
            ->route('Admin.TemuDokter.daftar-temu-dokter')
            ->with('success', 'Reservasi dokter berhasil dibuat.');
    }

    public function cancel_temu_dokter($idreservasi_dokter)
    {
        $temuDokter = TemuDokter::where('idreservasi_dokter', $idreservasi_dokter)->firstOrFail();
        $iduser = session('iduser');
        $temuDokter->update([
            'deleted_at' => now(),
            'deleted_by' => $iduser
        ]);

        return redirect()
            ->route('Admin.TemuDokter.daftar-temu-dokter')
            ->with('success', 'Reservasi dokter berhasil dibatalkan.');
    }
}

?>

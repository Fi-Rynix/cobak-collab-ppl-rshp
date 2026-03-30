<?php

use App\Http\Controllers\Admin\Dashboard_Controller;
use App\Http\Controllers\Admin\Dokter_Controller;
use App\Http\Controllers\Admin\JenisHewan_Controller;

//halaman utama
use App\Http\Controllers\Admin\Kategori_Controller;

//admin import
// use App\Http\Controllers\Admin\AdminSite_Controller;
use App\Http\Controllers\Admin\KategoriKlinis_Controller;
use App\Http\Controllers\Admin\Pemilik_Controller;
use App\Http\Controllers\Admin\Perawat_Controller;
use App\Http\Controllers\Admin\Pet_Controller;
use App\Http\Controllers\Admin\RasHewan_Controller;
use App\Http\Controllers\Admin\RekamMedis_Controller;
use App\Http\Controllers\Admin\Role_Controller;
use App\Http\Controllers\Admin\TemuDokter_Controller;
use App\Http\Controllers\Admin\TindakanTerapi_Controller;
use App\Http\Controllers\Admin\User_Controller;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dokter\DashboardDokter_Controller;
use App\Http\Controllers\Dokter\ProfilDokter_Controller;

//dokter import
use App\Http\Controllers\Dokter\RekamMedisDokter_Controller;
use App\Http\Controllers\MainSite_Controller;
use App\Http\Controllers\Pemilik\DashboardPemilik_Controller;

//perawat import
use App\Http\Controllers\Pemilik\ProfilPemilik_Controller;
use App\Http\Controllers\Pemilik\RekamMedisPemilik_Controller;
use App\Http\Controllers\Pemilik\ReservasiSaya_Controller;

//resepsionis import
use App\Http\Controllers\Perawat\DashboardPerawat_Controller;
use App\Http\Controllers\Perawat\ProfilPerawat_Controller;
use App\Http\Controllers\Perawat\RekamMedisPerawat_Controller;
use App\Http\Controllers\Resepsionis\DashboardResepsionis_Controller;

//pemilik import
use App\Http\Controllers\Resepsionis\PemilikResepsionis_Controller;
use App\Http\Controllers\Resepsionis\PetResepsionis_Controller;
use App\Http\Controllers\Resepsionis\TemuDokterResepsionis_Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


//halaman utama
Route::get('rshp', [MainSite_Controller::class,'rshp'])->name('rshp');
Route::get('struktur-organisasi', [MainSite_Controller::class,'struktur_organisasi'])->name('struktur-organisasi');
Route::get('layanan-umum', [MainSite_Controller::class,'layanan_umum'])->name('layanan-umum');
Route::get('cek-koneksi', [MainSite_Controller::class, 'cek_koneksi' ])->name('cek-koneksi');


Auth::routes();


//admin
Route::middleware(['auth', 'IsAdmin'])->prefix('Admin')->name('Admin.')->group(function () {

Route::get('dashboard-admin', [Dashboard_Controller::class, 'dashboard_admin'])->name('dashboard-admin');

Route::get('JenisHewan/daftar-jenis-hewan', [JenisHewan_Controller::class, 'daftar_jenis_hewan' ])->name('JenisHewan.daftar-jenis-hewan');
Route::post('JenisHewan/store-jenis-hewan', [JenisHewan_Controller::class, 'store_jenis_hewan'])->name('JenisHewan.store-jenis-hewan');
Route::put('JenisHewan/update-jenis-hewan/{id}', [JenisHewan_Controller::class, 'update_jenis_hewan'])->name('JenisHewan.update-jenis-hewan');
Route::delete('JenisHewan/delete-jenis-hewan/{id}', [JenisHewan_Controller::class, 'delete_jenis_hewan'])->name('JenisHewan.delete-jenis-hewan');


Route::get('Kategori/daftar-kategori', [Kategori_Controller::class, 'daftar_kategori'])->name('Kategori.daftar-kategori');
Route::post('Kategori/store-kategori', [Kategori_Controller::class, 'store_kategori'])->name('Kategori.store-kategori');
Route::put('Kategori/update-kategori/{id}', [Kategori_Controller::class, 'update_kategori'])->name('Kategori.update-kategori');
Route::delete('Kategori/delete-kategori/{id}', [Kategori_Controller::class, 'delete_kategori'])->name('Kategori.delete-kategori');


Route::get('KategoriKlinis/daftar-kategori-klinis', [KategoriKlinis_Controller::class, 'daftar_kategori_klinis'])->name('KategoriKlinis.daftar-kategori-klinis');
Route::post('KategoriKlinis/store-kategori-klinis', [KategoriKlinis_Controller::class, 'store_kategori_klinis'])->name('KategoriKlinis.store-kategori-klinis');
Route::put('KategoriKlinis/update-kategori-klinis/{id}', [KategoriKlinis_Controller::class, 'update_kategori_klinis'])->name('KategoriKlinis.update-kategori-klinis');
Route::delete('KategoriKlinis/delete-kategori-klinis/{id}', [KategoriKlinis_Controller::class, 'delete_kategori_klinis'])->name('KategoriKlinis.delete-kategori-klinis');


Route::get('ManajemenRole/daftar-manajemen-role', [Role_Controller::class, 'daftar_manajemen_role'])->name('ManajemenRole.daftar-manajemen-role');
Route::put('ManajemenRole/update-manajemen-role/{id}', [Role_Controller::class, 'update_manajemen_role'])->name('ManajemenRole.update-manajemen-role');


Route::get('Pemilik/daftar-pemilik', [Pemilik_Controller::class, 'daftar_pemilik'])->name('Pemilik.daftar-pemilik');
Route::post('Pemilik/store-pemilik', [Pemilik_Controller::class, 'store_pemilik'])->name('Pemilik.store-pemilik');
Route::put('Pemilik/update-pemilik/{id}', [Pemilik_Controller::class, 'update_pemilik'])->name('Pemilik.update-pemilik');
Route::put('Pemilik/save-pemilik/{iduser}', [Pemilik_Controller::class, 'save_pemilik'])->name('Pemilik.save-pemilik');
Route::delete('Pemilik/delete-pemilik/{id}', [Pemilik_Controller::class, 'delete_pemilik'])->name('Pemilik.delete-pemilik');


Route::get('Pet/daftar-pet', [Pet_Controller::class, 'daftar_pet'])->name('Pet.daftar-pet');
Route::post('Pet/store-pet', [Pet_Controller::class, 'store_pet'])->name('Pet.store-pet');
Route::put('Pet/update-pet/{id}', [Pet_Controller::class, 'update_pet'])->name('Pet.update-pet');
Route::delete('Pet/delete-pet/{id}', [Pet_Controller::class, 'delete_pet'])->name('Pet.delete-pet');


Route::get('Dokter/daftar-dokter', [Dokter_Controller::class, 'daftar_dokter'])->name('Dokter.daftar-dokter');
Route::post('Dokter/store-dokter', [Dokter_Controller::class, 'store_dokter'])->name('Dokter.store-dokter');
Route::put('Dokter/update-dokter/{id}', [Dokter_Controller::class, 'update_dokter'])->name('Dokter.update-dokter');
Route::put('Dokter/save-dokter/{iduser}', [Dokter_Controller::class, 'save_dokter'])->name('Dokter.save-dokter');
Route::delete('Dokter/delete-dokter/{id}', [Dokter_Controller::class, 'delete_dokter'])->name('Dokter.delete-dokter');


Route::get('Perawat/daftar-perawat', [Perawat_Controller::class, 'daftar_perawat'])->name('Perawat.daftar-perawat');
Route::post('Perawat/store-perawat', [Perawat_Controller::class, 'store_perawat'])->name('Perawat.store-perawat');
Route::put('Perawat/update-perawat/{id}', [Perawat_Controller::class, 'update_perawat'])->name('Perawat.update-perawat');
Route::put('Perawat/save-perawat/{iduser}', [Perawat_Controller::class, 'save_perawat'])->name('Perawat.save-perawat');
Route::delete('Perawat/delete-perawat/{id}', [Perawat_Controller::class, 'delete_perawat'])->name('Perawat.delete-perawat');


Route::get('RasHewan/daftar-ras-hewan', [RasHewan_Controller::class, 'daftar_ras_hewan'])->name('RasHewan.daftar-ras-hewan');
Route::post('RasHewan/store-ras-hewan', [RasHewan_Controller::class, 'store_ras_hewan'])->name('RasHewan.store-ras-hewan');
Route::put('RasHewan/update-ras-hewan/{id}', [RasHewan_Controller::class, 'update_ras_hewan'])->name('RasHewan.update-ras-hewan');
Route::delete('RasHewan/delete-ras-hewan/{id}', [RasHewan_Controller::class, 'delete_ras_hewan'])->name('RasHewan.delete-ras-hewan');


Route::get('TindakanTerapi/daftar-tindakan-terapi', [TindakanTerapi_Controller::class, 'daftar_tindakan_terapi'])->name('TindakanTerapi.daftar-tindakan-terapi');
Route::post('TindakanTerapi/store-tindakan-terapi', [TindakanTerapi_Controller::class, 'store_tindakan_terapi'])->name('TindakanTerapi.store-tindakan-terapi');
Route::put('TindakanTerapi/update-tindakan-terapi/{id}', [TindakanTerapi_Controller::class, 'update_tindakan_terapi'])->name('TindakanTerapi.update-tindakan-terapi');
Route::delete('TindakanTerapi/delete-tindakan-terapi/{id}', [TindakanTerapi_Controller::class, 'delete_tindakan_terapi'])->name('TindakanTerapi.delete-tindakan-terapi');


Route::get('TemuDokter/daftar-temu-dokter', [TemuDokter_Controller::class, 'daftar_temu_dokter'])->name('TemuDokter.daftar-temu-dokter');
Route::post('TemuDokter/store-temu-dokter', [TemuDokter_Controller::class, 'store_temu_dokter'])->name('TemuDokter.store-temu-dokter');
Route::put('TemuDokter/cancel-temu-dokter/{id}', [TemuDokter_Controller::class, 'cancel_temu_dokter'])->name('TemuDokter.cancel-temu-dokter');


Route::get('User/daftar-user', [User_Controller::class, 'daftar_user'])->name('User.daftar-user');
Route::post('User/store-user', [User_Controller::class, 'store_user'])->name('User.store-user');
Route::put('User/update-user/{id}', [User_Controller::class, 'update_user'])->name('User.update-user');
Route::delete('User/delete-user/{id}', [User_Controller::class, 'delete_user'])->name('User.delete-user');
Route::put('User/reset-password/{id}', [User_Controller::class, 'reset_password'])->name('User.reset-password');
Route::put('User/random-password/{id}', [User_Controller::class, 'random_password_update'])->name('User.random-password');


Route::get('RekamMedis/daftar-rekam-medis', [RekamMedis_Controller::class, 'daftar_rekam_medis'])->name('RekamMedis.daftar-rekam-medis');
Route::post('RekamMedis/store-rekam-medis', [RekamMedis_Controller::class, 'store_rekam_medis'])->name('RekamMedis.store-rekam-medis');
Route::put('RekamMedis/update-rekam-medis/{id}', [RekamMedis_Controller::class, 'update_rekam_medis'])->name('RekamMedis.update-rekam-medis');
Route::delete('RekamMedis/delete-rekam-medis/{id}', [RekamMedis_Controller::class, 'delete_rekam_medis'])->name('RekamMedis.delete-rekam-medis');
});



//dokter
Route::middleware(['auth', 'IsDokter'])->prefix('Dokter')->name('Dokter.')->group(function () {
    Route::get('dashboard-dokter', [DashboardDokter_Controller::class, 'dashboard_dokter'])->name('dashboard-dokter');
    
    Route::get('RekamMedis/daftar-rekam-medis', [RekamMedisDokter_Controller::class, 'daftar_rekam_medis'])->name('RekamMedis.daftar-rekam-medis');
    Route::get('RekamMedis/detail-rekam-medis/{id}', [RekamMedisDokter_Controller::class, 'detail_rekam_medis'])->name('RekamMedis.detail-rekam-medis');
    Route::post('RekamMedis/store-detail/{idrekam_medis}', [RekamMedisDokter_Controller::class, 'store_detail'])->name('RekamMedis.store-detail');
    Route::put('RekamMedis/update-detail/{id}', [RekamMedisDokter_Controller::class, 'update_detail'])->name('RekamMedis.update-detail');
    
    Route::delete('RekamMedis/delete-detail/{id}', [RekamMedisDokter_Controller::class, 'delete_detail'])->name('RekamMedis.delete-detail');
    Route::get('Profil/profil-saya', [ProfilDokter_Controller::class, 'profil_saya'])->name('Profil.profil-saya');
});

//cihuy

//perawat
Route::middleware(['auth', 'IsPerawat'])->prefix('Perawat')->name('Perawat.')->group(function () {
    Route::get('dashboard-perawat', [DashboardPerawat_Controller::class, 'dashboard_perawat'])->name('dashboard-perawat');
    
    Route::get('RekamMedis/daftar-rekam-medis', [RekamMedisPerawat_Controller::class, 'daftar_rekam_medis'])->name('RekamMedis.daftar-rekam-medis');
    Route::get('RekamMedis/detail-rekam-medis/{id}', [RekamMedisPerawat_Controller::class, 'detail_rekam_medis'])->name('RekamMedis.detail-rekam-medis');
    Route::post('RekamMedis/store-rekam-medis', [RekamMedisPerawat_Controller::class, 'store_rekam_medis'])->name('RekamMedis.store-rekam-medis');
    Route::put('RekamMedis/update-rekam-medis/{id}', [RekamMedisPerawat_Controller::class, 'update_rekam_medis'])->name('RekamMedis.update-rekam-medis');
    Route::delete('RekamMedis/delete-rekam-medis/{id}', [RekamMedisPerawat_Controller::class, 'delete_rekam_medis'])->name('RekamMedis.delete-rekam-medis');
    
    Route::get('Profil/profil-saya', [ProfilPerawat_Controller::class, 'profil_saya'])->name('Profil.profil-saya');
});



//resepsionis
Route::middleware(['auth', 'IsResepsionis'])->prefix('Resepsionis')->name('Resepsionis.')->group(function () {
    Route::get('dashboard-resepsionis', [DashboardResepsionis_Controller::class, 'dashboard_resepsionis'])->name('dashboard-resepsionis');
    
    Route::get('Pemilik/daftar-pemilik', [PemilikResepsionis_Controller::class, 'daftar_pemilik'])->name('Pemilik.daftar-pemilik');
    Route::post('Pemilik/store-pemilik', [PemilikResepsionis_Controller::class, 'store_pemilik'])->name('Pemilik.store-pemilik');
    Route::put('Pemilik/save-pemilik/{iduser}', [PemilikResepsionis_Controller::class, 'save_pemilik'])->name('Pemilik.save-pemilik');
    Route::delete('Pemilik/delete-pemilik/{id}', [PemilikResepsionis_Controller::class, 'delete_pemilik'])->name('Pemilik.delete-pemilik');

    Route::get('Pet/daftar-pet', [PetResepsionis_Controller::class, 'daftar_pet'])->name('Pet.daftar-pet');
    Route::post('Pet/store-pet', [PetResepsionis_Controller::class, 'store_pet'])->name('Pet.store-pet');
    Route::put('Pet/update-pet/{id}', [PetResepsionis_Controller::class, 'update_pet'])->name('Pet.update-pet');
    Route::delete('Pet/delete-pet/{id}', [PetResepsionis_Controller::class, 'delete_pet'])->name('Pet.delete-pet');

    Route::get('TemuDokter/daftar-temu-dokter', [TemuDokterResepsionis_Controller::class, 'daftar_temu_dokter'])->name('TemuDokter.daftar-temu-dokter');
    Route::post('TemuDokter/store-temu-dokter', [TemuDokterResepsionis_Controller::class, 'store_temu_dokter'])->name('TemuDokter.store-temu-dokter');
    Route::put('TemuDokter/cancel-temu-dokter/{idreservasi_dokter}', [TemuDokterResepsionis_Controller::class, 'cancel_temu_dokter'])->name('TemuDokter.cancel-temu-dokter');
});



//pemilik
Route::middleware(['auth','IsPemilik'])->prefix('Pemilik')->name('Pemilik.')->group(function () {
    Route::get('dashboard-pemilik', [DashboardPemilik_Controller::class, 'dashboard_pemilik'])->name('dashboard-pemilik');
    Route::get('TemuDokter/daftar-reservasi-saya', [ReservasiSaya_Controller::class, 'daftar_reservasi_saya'])->name('TemuDokter.daftar-reservasi-saya');
    Route::get('RekamMedis/daftar-rekam-medis', [RekamMedisPemilik_Controller::class, 'daftar_rekam_medis'])->name('RekamMedis.daftar-rekam-medis');
    Route::get('RekamMedis/detail-rekam-medis/{id}', [RekamMedisPemilik_Controller::class, 'detail_rekam_medis'])->name('RekamMedis.detail-rekam-medis');
    Route::get('Profil/profil-saya', [ProfilPemilik_Controller::class, 'profil_saya'])->name('Profil.profil-saya');
});



//logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

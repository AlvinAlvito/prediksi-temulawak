<?php

use App\Http\Controllers\Api\ChartController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PengecekanController;
use App\Http\Controllers\KoefisieniController;
use App\Http\Controllers\PrediksiController;

// ===================
// Halaman Login
// ===================
Route::get('/', function () {
    return view('login');
})->name('login');

// ===================
// Proses Login Manual
// ===================
Route::post('/', function (Request $request) {
    $username = $request->username;
    $password = $request->password;

    if ($username === 'admin' && $password === '123') {
        session(['is_admin' => true]);
        return redirect('/admin');
    }

    return back()->withErrors(['login' => 'Username atau Password salah!']);
})->name('login.proses');

// ===================
// Logout
// ===================
Route::get('/logout', function () {
    session()->forget('is_admin');
    return redirect('/');
})->name('logout');

// ===================
// Dashboard Admin
// ===================
use App\Http\Controllers\DashboardController;

Route::get('/admin', function () {
    if (!session('is_admin')) {
        return redirect('/');
    }

    // panggil langsung controller agar tetap sesuai struktur kamu
    $controller = app(DashboardController::class);
    return $controller->index();

})->name('index');


// ===================
// CRUD Data pengecekan
// ===================
Route::get('/admin/data-pengecekan', function () {
    if (!session('is_admin')) return redirect('/');
    return app(PengecekanController::class)->index();
})->name('pengecekan.index');

Route::post('/admin/data-pengecekan', function (Request $request) {
    if (!session('is_admin')) return redirect('/');
    return app(PengecekanController::class)->store($request);
})->name('pengecekan.store');

Route::put('/admin/data-pengecekan/{id}', function (Request $request, $id) {
    if (!session('is_admin')) return redirect('/');
    return app(PengecekanController::class)->update($request, $id);
})->name('pengecekan.update');

Route::delete('/admin/data-pengecekan/{id}', function ($id) {
    if (!session('is_admin')) return redirect('/');
    return app(PengecekanController::class)->destroy($id);
})->name('pengecekan.destroy');


// ===================
// VIEW - Tabel Prediksi (Matriks Kemiripan)
// ===================
Route::get('/admin/hasil-prediksi', function (Request $req) {
    if (!session('is_admin')) return redirect('/');
    // optional: ?anchor=C1..C6  | ?user_id=ID (jika mau pilih user tertentu)
    return app(PengecekanController::class)->hasilPrediksi($req);
})->name('hasil.prediksi');


// Hitung & simpan prediksi (POST agar eksplisit proses)
Route::post('/admin/hitung-prediksi', function (\Illuminate\Http\Request $request) {
    if (!session('is_admin')) return redirect('/');
    return app(PengecekanController::class)->hitungPrediksi($request);
})->name('prediksi.hitung');

// Tampilkan rangking
Route::get('/admin/hasil-rangking', function (\Illuminate\Http\Request $request) {
    if (!session('is_admin')) return redirect('/');
    return app(PengecekanController::class)->rangkingPrediksi($request);
})->name('prediksi.rangking');


// ===================
// VIEW - Detail Produk & Grafik Prediksi
// ===================
Route::get('/admin/detail-produk/{id}', function ($id) {
    if (!session('is_admin')) return redirect('/');
    return app(PrediksiController::class)->show($id);
})->name('produk.detail');


Route::get('/chart/sektor', [ChartController::class, 'buahPerSektor']);
Route::get('/chart/pegawai', [ChartController::class, 'buahPerPegawai']);
Route::get('/chart/cuaca', [ChartController::class, 'buahPerCuaca']);
Route::get('/chart/pendapatan-tertinggi', [ChartController::class, 'pendapatanTertinggi']);
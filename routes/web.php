<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\FormulirController;
use App\Http\Controllers\DepartemenTerlibatController;
use App\Http\Controllers\TugasProduksiController;
use App\Http\Controllers\PersetujuanManagerController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DaftarPenggunaController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\ModelSizeController;
use App\Http\Controllers\ThermalShockController;
use App\Http\Controllers\Master\SpesifikasiController;
use App\Http\Controllers\Master\OvenController;
use App\Http\Controllers\Master\ThermalOvenController;
use App\Http\Controllers\Master\ThermalPintuController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\Master\TinggiFormerController;
use App\Http\Controllers\Master\JamKeluarOvenController;
use App\Http\Controllers\WaterAbsorptionController;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::get('testing', [DashboardController::class, 'testing'])->name('testing');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
    Route::get('register', [RegisterController::class, 'index'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware('auth', 'role:admin')->group(function () {
    // ... route lainnya
    Route::get('master/users', [UserController::class, 'index'])->name('users.index');
    Route::get('master/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('master/users/create', [UserController::class, 'store'])->name('users.store');
    Route::get('master/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('master/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('master/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('master/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('master/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('master/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('master/roles/create', [RoleController::class, 'store'])->name('roles.store');
    Route::get('master/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('master/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('master/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('master/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('master/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('master/customer/create', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('master/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('master/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('master/customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');

    Route::get('master/modelsize', [ModelSizeController::class, 'index'])->name('modelsize.index');
    Route::get('master/modelsize/create', [ModelSizeController::class, 'create'])->name('modelsize.create');
    Route::post('master/modelsize/create', [ModelSizeController::class, 'store'])->name('modelsize.store');
    Route::get('master/modelsize/{modelsize}/edit', [ModelSizeController::class, 'edit'])->name('modelsize.edit');
    Route::put('master/modelsize/{modelsize}', [ModelSizeController::class, 'update'])->name('modelsize.update');
    Route::delete('master/modelsize/{modelsize}', [ModelSizeController::class, 'destroy'])->name('modelsize.destroy');

    Route::get('master/spesifikasi', [SpesifikasiController::class, 'index'])->name('spesifikasi.index');
    Route::get('master/spesifikasi/create', [SpesifikasiController::class, 'create'])->name('spesifikasi.create');
    Route::post('master/spesifikasi/create', [SpesifikasiController::class, 'store'])->name('spesifikasi.store');
    Route::get('master/spesifikasi/{spesifikasi}/edit', [SpesifikasiController::class, 'edit'])->name('spesifikasi.edit');
    Route::put('master/spesifikasi/{spesifikasi}', [SpesifikasiController::class, 'update'])->name('spesifikasi.update');
    Route::delete('master/spesifikasi/{spesifikasi}', [SpesifikasiController::class, 'destroy'])->name('spesifikasi.destroy');

    Route::get('master/oven', [OvenController::class, 'index'])->name('oven.index');
    Route::get('master/oven/create', [OvenController::class, 'create'])->name('oven.create');
    Route::post('master/oven/create', [OvenController::class, 'store'])->name('oven.store');
    Route::get('master/oven/{oven}/edit', [OvenController::class, 'edit'])->name('oven.edit');
    Route::put('master/oven/{oven}', [OvenController::class, 'update'])->name('oven.update');
    Route::delete('master/oven/{oven}', [OvenController::class, 'destroy'])->name('oven.destroy');

    Route::get('master/thermal-oven', [ThermalOvenController::class, 'index'])->name('thermaloven.index');
    Route::get('master/thermal-oven/create', [ThermalOvenController::class, 'create'])->name('thermaloven.create');
    Route::post('master/thermal-oven/create', [ThermalOvenController::class, 'store'])->name('thermaloven.store');
    Route::get('master/thermal-oven/{thermaloven}/edit', [ThermalOvenController::class, 'edit'])->name('thermaloven.edit');
    Route::put('master/thermal-oven/{thermaloven}', [ThermalOvenController::class, 'update'])->name('thermaloven.update');
    Route::delete('master/thermal-oven/{thermaloven}', [ThermalOvenController::class, 'destroy'])->name('thermaloven.destroy');

    Route::get('master/thermal-pintu', [ThermalPintuController::class, 'index'])->name('thermalpintu.index');
    Route::get('master/thermal-pintu/create', [ThermalPintuController::class, 'create'])->name('thermalpintu.create');
    Route::post('master/thermal-pintu/create', [ThermalPintuController::class, 'store'])->name('thermalpintu.store');
    Route::get('master/thermal-pintu/{thermalpintu}/edit', [ThermalPintuController::class, 'edit'])->name('thermalpintu.edit');
    Route::put('master/thermal-pintu/{thermalpintu}', [ThermalPintuController::class, 'update'])->name('thermalpintu.update');
    Route::delete('master/thermal-pintu/{thermalpintu}', [ThermalPintuController::class, 'destroy'])->name('thermalpintu.destroy');

    Route::get('master/tinggi-former', [TinggiFormerController::class, 'index'])->name('tinggiformer.index');
    Route::get('master/tinggi-former/create', [TinggiFormerController::class, 'create'])->name('tinggiformer.create');
    Route::post('master/tinggi-former/create', [TinggiFormerController::class, 'store'])->name('tinggiformer.store');
    Route::get('master/tinggi-former/{tinggiformer}/edit', [TinggiFormerController::class, 'edit'])->name('tinggiformer.edit');
    Route::put('master/tinggi-former/{tinggiformer}', [TinggiFormerController::class, 'update'])->name('tinggiformer.update');
    Route::delete('master/tinggi-former/{tinggiformer}', [TinggiFormerController::class, 'destroy'])->name('tinggiformer.destroy');

    Route::get('master/jam-keluar-oven', [JamKeluarOvenController::class, 'index'])->name('jamkeluaroven.index');
    Route::get('master/jam-keluar-oven/create', [JamKeluarOvenController::class, 'create'])->name('jamkeluaroven.create');
    Route::post('master/jam-keluar-oven/create', [JamKeluarOvenController::class, 'store'])->name('jamkeluaroven.store');
    Route::get('master/jam-keluar-oven/{jamkeluaroven}/edit', [JamKeluarOvenController::class, 'edit'])->name('jamkeluaroven.edit');
    Route::put('master/jam-keluar-oven/{jamkeluaroven}', [JamKeluarOvenController::class, 'update'])->name('jamkeluaroven.update');
    Route::delete('master/jam-keluar-oven/{jamkeluaroven}', [JamKeluarOvenController::class, 'destroy'])->name('jamkeluaroven.destroy');


});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('daftar-pengguna', [DaftarPenggunaController::class, 'index'])->name('daftar.pengguna.index');
    Route::get('thermal-shock', [ThermalShockController::class, 'index'])->name('thermalshock.index');
    Route::get('thermal-shock/create', [ThermalShockController::class, 'create'])->name('thermalshock.create');
    Route::post('thermal-shock/create', [ThermalShockController::class, 'store'])->name('thermalshock.store');
    Route::get('thermal-shock/{thermalshock}', [ThermalShockController::class, 'show'])->name('thermalshock.show');
    Route::get('thermal-shock/{thermalshock}/edit', [ThermalShockController::class, 'edit'])->name('thermalshock.edit');
    Route::put('thermal-shock/{thermalshock}/edit', [ThermalShockController::class, 'update'])->name('thermalshock.update');
    Route::delete('thermal-shock/{thermalshock}', [ThermalShockController::class, 'destroy'])->name('thermalshock.destroy');

    Route::get('thermal-shock/{thermalshock}/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('thermal-shock/{thermalshock}/produk/create', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('thermal-shock/{thermalshock}/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('thermal-shock/{thermalshock}/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::put('thermal-shock/{thermalshock}/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('thermal-shock/{thermalshock}/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');

    Route::get('thermal-shock/{thermalshock}/produk/{produk}/pengerjaan', [ProdukController::class, 'pengerjaan'])->name('produk.pengerjaan');
    Route::post('thermal-shock/{thermalshock}/produk/{produk}/pengerjaan', [ProdukController::class, 'simpanPengerjaan'])->name('produk.pengerjaan.store');

    Route::get('water-absorption', [WaterAbsorptionController::class, 'index'])->name('waterabsorption.index');
    Route::get('water-absorption/create', [WaterAbsorptionController::class, 'create'])->name('waterabsorption.create');
    Route::post('water-absorption/create', [WaterAbsorptionController::class, 'store'])->name('waterabsorption.store');
    Route::get('water-absorption/{waterabsorption}', [WaterAbsorptionController::class, 'show'])->name('waterabsorption.show');
    Route::get('water-absorption/{waterabsorption}/edit', [WaterAbsorptionController::class, 'edit'])->name('waterabsorption.edit');
    Route::put('water-absorption/{waterabsorption}/edit', [WaterAbsorptionController::class, 'update'])->name('waterabsorption.update');
    Route::delete('water-absorption/{waterabsorption}', [WaterAbsorptionController::class, 'destroy'])->name('waterabsorption.destroy');
});


<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BAPController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\JamaahController;
use App\Http\Controllers\KanwilController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\PengunduranController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\JamaahHajiKhususController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\UserManagementController;

Route::get('/jamaah/template-test', function () {
    return "Route Berhasil";
});;

Route::get('/', [AuthController::class, 'showLanding']);

Route::get('/list-travel', [AuthController::class, 'showListTravel'])->name('list.travel');

Route::post('/register', [RegisterController::class, 'store'])->middleware('guest')->name('register.perform');
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login.perform');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('home')->middleware('auth', 'password.changed', 'kabupaten');

Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('user.changePassword');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('user.updatePassword');

Route::get('/test', function () {
    return 'Middleware test';
})->middleware('auth', 'password.changed');

// Tambahkan route baru di web.php
Route::get('/logout-redirect', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout.redirect');

Route::get('/keberangkatan/events', [BAPController::class, 'getEvents'])->name('calendar.events');

Route::group(['middleware' => ['auth', 'password.changed']], function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Impersonate routes
    Route::get('/impersonate', [ImpersonateController::class, 'index'])->name('impersonate.index');
    Route::get('/impersonate/{id}', [ImpersonateController::class, 'impersonate'])->name('impersonate.take');
    Route::get('/impersonate-leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');

    Route::get('/jamaah/haji', [JamaahController::class, 'indexHaji'])->name('jamaah.haji');
    Route::get('/jamaah/haji/create', [JamaahController::class, 'createHaji'])->name('jamaah.haji.create');
    Route::post('/jamaah/haji', [JamaahController::class, 'storeHaji'])->name('jamaah.haji.store');

    Route::get('/jamaah/umrah', [JamaahController::class, 'indexUmrah'])->name('jamaah.umrah');
    Route::get('/jamaah/umrah/create', [JamaahController::class, 'createUmrah'])->name('jamaah.umrah.create');
    Route::post('/jamaah/umrah', [JamaahController::class, 'storeUmrah'])->name('jamaah.umrah.store');

    // Jamaah Haji Khusus routes - MUST BE BEFORE generic jamaah routes
    Route::resource('jamaah/haji-khusus', JamaahHajiKhususController::class)->names([
        'index' => 'jamaah.haji-khusus.index',
        'create' => 'jamaah.haji-khusus.create',
        'store' => 'jamaah.haji-khusus.store',
        'show' => 'jamaah.haji-khusus.show',
        'edit' => 'jamaah.haji-khusus.edit',
        'update' => 'jamaah.haji-khusus.update',
        'destroy' => 'jamaah.haji-khusus.destroy',
    ]);
    Route::put('/jamaah/haji-khusus/{jamaahHajiKhusus}/status', [JamaahHajiKhususController::class, 'updateStatus'])->name('jamaah.haji-khusus.update-status');
    Route::post('/jamaah/haji-khusus/{jamaahHajiKhusus}/verify-bukti-setor', [JamaahHajiKhususController::class, 'verifyBuktiSetor'])->name('jamaah.haji-khusus.verify-bukti-setor');
    Route::post('/jamaah/haji-khusus/{jamaahHajiKhusus}/assign-porsi', [JamaahHajiKhususController::class, 'assignPorsiNumber'])->name('jamaah.haji-khusus.assign-porsi');
    Route::get('/jamaah/haji-khusus/export', [JamaahHajiKhususController::class, 'export'])->name('jamaah.haji-khusus.export');

    Route::get('/jamaah/template', [JamaahController::class, 'downloadTemplate'])->name('jamaah.template');
    Route::get('/jamaah/{id}', [JamaahController::class, 'detail'])->name('jamaah.detail');
    Route::get('/jamaah/edit/{id}', [JamaahController::class, 'edit'])->name('jamaah.edit');
    Route::put('/jamaah/{id}', [JamaahController::class, 'update'])->name('jamaah.update');
    Route::delete('/jamaah/{id}', [JamaahController::class, 'destroy'])->name('jamaah.destroy');
    Route::post('/jamaah/import', [JamaahController::class, 'import'])->name('jamaah.import');
    Route::get('/jamaah/export', [JamaahController::class, 'export'])->name('jamaah.export');

    // Pengaduan routes - accessible by admin only
    Route::middleware(['admin'])->group(function () {
        Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan');
        Route::get('/pengaduan/create', [PengaduanController::class, 'create'])->name('pengaduan.create');
        Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
        Route::get('/pengaduan/{id}', [PengaduanController::class, 'detail'])->name('pengaduan.show');
        Route::post('/pengaduan/{id}/status', [PengaduanController::class, 'updateStatus'])->name('pengaduan.update-status');
        Route::get('/pengaduan/{id}/download-pdf', [PengaduanController::class, 'downloadPDF'])->name('pengaduan.download-pdf');
    });

    // Public pengaduan routes (no authentication required)
    Route::get('/pengaduan-public/{id}', [PengaduanController::class, 'publicView'])->name('pengaduan.public');
    Route::post('/pengaduan-public', [PengaduanController::class, 'storePublic'])->name('pengaduan.store-public');

    Route::get('/pengunduran', [PengunduranController::class, 'index'])->name('pengunduran');
    Route::get('/pengunduran/create', [PengunduranController::class, 'create'])->name('pengunduran.create');
    Route::post('/pengunduran', [PengunduranController::class, 'store'])->name('pengunduran.store');

    Route::put('/pengajuan/{id}/status', [KanwilController::class, 'updateStatus'])->name('update.status');

    Route::get('/bap', [BAPController::class, 'index'])->name('bap');
    Route::get('/form-bap', [BAPController::class, 'showFormBAP'])->name('form.bap');
    Route::post('/bap', [BAPController::class, 'simpan'])->name('post.bap');
    Route::get('/cetak-bap/{id}', [BAPController::class, 'printBAP'])->name('cetak.bap');
    Route::get('/bap/detail/{id}', [BAPController::class, 'detail'])->name('detail.bap');
    Route::post('bap/upload/{id}', [BAPController::class, 'uploadPDF'])->name('bap.upload');
    Route::post('bap/ajukan/{id}', [BAPController::class, 'ajukan'])->name('bap.ajukan');
    Route::post('bap/update-status/{id}', [BAPController::class, 'updateStatus'])->name('bap.updateStatus');
    Route::get('/keberangkatan', [BAPController::class, 'showKeberangkatan'])->name('keberangkatan');

    Route::get('/travel', [KanwilController::class, 'showTravel'])->name('travel');
    Route::get('/travel/form', [KanwilController::class, 'showFormTravel'])->name('form.travel');
    Route::post('/travel/form', [KanwilController::class, 'store'])->name('post.travel');
    Route::get('/travel/{id}/edit', [KanwilController::class, 'edit'])->name('travel.edit');
    Route::put('/travel/{id}', [KanwilController::class, 'update'])->name('travel.update');
    
    // Update travel status route - using POST method to avoid method override issues
    Route::post('/travel/{id}/status', [KanwilController::class, 'updateStatus'])
        ->name('travel.update-status')
        ->middleware('auth', 'password.changed');

    // Sertifikat routes
    Route::resource('sertifikat', SertifikatController::class)->except(['show', 'edit', 'update']);
Route::get('/sertifikat/{id}/generate', [SertifikatController::class, 'generate'])->name('sertifikat.generate');
Route::get('/sertifikat/{id}/download', [SertifikatController::class, 'download'])->name('sertifikat.download');
Route::get('/sertifikat/travel-data/{id}', [SertifikatController::class, 'getTravelData'])->name('sertifikat.travel-data');
Route::get('/sertifikat/cabang-data/{id}', [SertifikatController::class, 'getCabangData'])->name('sertifikat.cabang-data');
Route::get('/sertifikat/settings', [SertifikatController::class, 'getSettings'])->name('sertifikat.settings');
Route::post('/sertifikat/settings', [SertifikatController::class, 'updateSettings'])->name('sertifikat.settings.update');

// Travel certificates (for travel companies to view their own certificates)
Route::get('/travel/certificates', [SertifikatController::class, 'travelCertificates'])->name('travel.certificates');
    
    // Cabang Travel routes - accessible by admin and kabupaten only
    Route::middleware(['kabupaten.access'])->group(function () {
        Route::get('/cabang-travel', [KanwilController::class, 'showCabangTravel'])->name('cabang.travel');
        Route::get('/cabang-travel/form', [KanwilController::class, 'createCabangTravel'])->name('form.cabang_travel');
        Route::post('/cabang-travel/form', [KanwilController::class, 'storeCabangTravel'])->name('post.cabang_travel');
        Route::get('/cabang-travel/{id}/edit', [KanwilController::class, 'editCabangTravel'])->name('cabang.travel.edit');
        Route::put('/cabang-travel/{id}', [KanwilController::class, 'updateCabangTravel'])->name('cabang.travel.update');
        Route::delete('/cabang-travel/{id}', [KanwilController::class, 'destroyCabangTravel'])->name('cabang.travel.destroy');
        Route::post('/import-cabang-travel', [KanwilController::class, 'import'])->name('import.cabang_travel');
        Route::get('/download-template-cabang-travel', [KanwilController::class, 'downloadTemplateCabang'])->name('download.template.cabang_travel');
    });

    Route::get('import-form', [ExcelImportController::class, 'importForm'])->name('import.form');
    Route::post('import-data', [ExcelImportController::class, 'import'])->name('import.data');
    Route::get('users/template', [KanwilController::class, 'downloadTemplate'])->name('travel.template');

    Route::middleware(['admin'])->prefix('kanwil')->group(function () {
        Route::get('/form', [KanwilController::class, 'showFormTravel'])->name('form');
        Route::post('/form', [KanwilController::class, 'store'])->name('post.form');

        Route::get('/travels', [AuthController::class, 'showUsers'])->name('travels');

        Route::get('/tambah-akun-travel', [AuthController::class, 'showForm'])->name('form.addUser');
        Route::post('/tambah-akun-travel', [AuthController::class, 'addUser'])->name('addUser');
        Route::put('/reset-password/{id}', [AuthController::class, 'resetPassword'])->name('resetPassword');
    });

    // User Management routes - Admin only
    Route::middleware(['admin'])->group(function () {
        // Kabupaten User Management
        Route::get('/kabupaten', [UserManagementController::class, 'indexKabupaten'])->name('kabupaten.index');
        Route::get('/kabupaten/create', [UserManagementController::class, 'createKabupaten'])->name('kabupaten.create');
        Route::post('/kabupaten', [UserManagementController::class, 'storeKabupaten'])->name('kabupaten.store');
        
        // Travel User Management
        Route::get('/travel-users', [UserManagementController::class, 'indexTravel'])->name('travels.index');
        Route::get('/travel-users/create', [UserManagementController::class, 'createTravel'])->name('travels.create');
        Route::post('/travel-users', [UserManagementController::class, 'storeTravel'])->name('travels.store');
        
        // General User Management
        Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });


});

// API routes for province, city, and district selection
Route::get('/api/provinces', [ApiController::class, 'getProvinces'])->name('api.provinces');
Route::get('/api/cities', [ApiController::class, 'getCities'])->name('api.cities');
Route::get('/api/districts', [ApiController::class, 'getDistricts'])->name('api.districts');

// Public verification route (no authentication required)
Route::get('/verifikasi-sertifikat/{uuid}', [SertifikatController::class, 'verifikasi'])->name('sertifikat.verifikasi');

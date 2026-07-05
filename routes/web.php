<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BAPController;
use App\Http\Controllers\UtilityController;

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
use App\Http\Controllers\PublicTravelController;

Route::get('/jamaah/template-test', function () {
    return "Route Berhasil";
});;

Route::get('/', [AuthController::class, 'showLanding']);

Route::get('/list-travel', [AuthController::class, 'showListTravel'])->name('list.travel')->middleware('auth');
Route::middleware('throttle:public')->group(function () {
    Route::get('/travel-public', [PublicTravelController::class, 'index'])->name('travel.public');
    Route::get('/travel-public/{travel:public_uuid}', [PublicTravelController::class, 'show'])
        ->name('travel.public.show')
        ->whereUuid('travel');
});

Route::post('/register', [RegisterController::class, 'store'])->middleware(['guest', 'throttle:auth'])->name('register.perform');
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware(['guest', 'throttle:auth'])->name('login.perform');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('home')->middleware('auth', 'password.changed');

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

    // Change Password routes
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('user.changePassword');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('user.updatePassword');

    // User Profile routes
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [UserProfileController::class, 'update'])->name('profile.update');

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
    Route::get('/jamaah/haji-khusus/export', [JamaahHajiKhususController::class, 'export'])->name('jamaah.haji-khusus.export');
    Route::get('/jamaah/haji-khusus/export-pdf', [JamaahHajiKhususController::class, 'exportPDF'])->name('jamaah.haji-khusus.export-pdf');
    Route::resource('jamaah/haji-khusus', JamaahHajiKhususController::class, ['parameters' => ['haji-khusus' => 'id']])->names([
        'index' => 'jamaah.haji-khusus.index',
        'create' => 'jamaah.haji-khusus.create',
        'store' => 'jamaah.haji-khusus.store',
        'show' => 'jamaah.haji-khusus.show',
        'edit' => 'jamaah.haji-khusus.edit',
        'update' => 'jamaah.haji-khusus.update',
        'destroy' => 'jamaah.haji-khusus.destroy',
    ]);
    Route::put('/jamaah/haji-khusus/{id}/status', [JamaahHajiKhususController::class, 'updateStatus'])->name('jamaah.haji-khusus.update-status');
    Route::post('/jamaah/haji-khusus/{id}/verify-bukti-setor', [JamaahHajiKhususController::class, 'verifyBuktiSetor'])->name('jamaah.haji-khusus.verify-bukti-setor');
    Route::post('/jamaah/haji-khusus/{id}/assign-porsi', [JamaahHajiKhususController::class, 'assignPorsiNumber'])->name('jamaah.haji-khusus.assign-porsi');

    Route::get('/jamaah/template', [JamaahController::class, 'downloadTemplate'])->name('jamaah.template');
    Route::get('/jamaah/{id}', [JamaahController::class, 'detail'])->name('jamaah.detail');
    Route::get('/jamaah/edit/{id}', [JamaahController::class, 'edit'])->name('jamaah.edit');
    Route::put('/jamaah/{id}', [JamaahController::class, 'update'])->name('jamaah.update');
    Route::delete('/jamaah/{id}', [JamaahController::class, 'destroy'])->name('jamaah.destroy');
    Route::post('/jamaah/import', [JamaahController::class, 'import'])->name('jamaah.import');
    Route::get('/jamaah/export', [JamaahController::class, 'export'])->name('jamaah.export');

    // Export routes for jamaah umrah and haji
    Route::get('/jamaah/umrah/export', [JamaahController::class, 'exportUmrah'])->name('jamaah.umrah.export');
    Route::get('/jamaah/haji/export', [JamaahController::class, 'exportHaji'])->name('jamaah.haji.export');

    // Pengaduan routes - accessible by admin only
    Route::middleware(['admin'])->group(function () {
        Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan');
        Route::get('/pengaduan/create', [PengaduanController::class, 'create'])->name('pengaduan.create');
        Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
        Route::get('/pengaduan/{id}', [PengaduanController::class, 'detail'])->name('pengaduan.show');
        Route::get('/pengaduan/{id}/berkas', [PengaduanController::class, 'downloadBerkas'])->name('pengaduan.download-berkas');
        Route::post('/pengaduan/{id}/status', [PengaduanController::class, 'updateStatus'])->name('pengaduan.update-status');
        Route::get('/pengaduan/{id}/download-pdf', [PengaduanController::class, 'downloadPDF'])->name('pengaduan.download-pdf');
    });

    Route::get('/pengunduran', [PengunduranController::class, 'index'])->name('pengunduran');
    Route::get('/pengunduran/create', [PengunduranController::class, 'create'])->name('pengunduran.create');
    Route::post('/pengunduran', [PengunduranController::class, 'store'])->name('pengunduran.store');

    Route::put('/pengajuan/{id}/status', [KanwilController::class, 'updateStatus'])->name('update.status');

    Route::get('/bap', [BAPController::class, 'index'])->name('bap');
    Route::get('/form-bap', [BAPController::class, 'showFormBAP'])->name('form.bap');
    Route::get('/form-bap/{id}/edit', [BAPController::class, 'editFormBAP'])->name('form.bap.edit');
    Route::get('/form-bap/{id}/upload', [BAPController::class, 'showWizardUpload'])->name('bap.wizard.upload');
    Route::get('/form-bap/{id}/review', [BAPController::class, 'showWizardReview'])->name('bap.wizard.review');
    Route::get('/bap/jamaah-options', [BAPController::class, 'jamaahPickerOptions'])->name('bap.jamaah.options');
    Route::get('/bap/template-surat-pernyataan', [BAPController::class, 'downloadSuratPernyataanTemplate'])->name('bap.template.surat-pernyataan');
    Route::post('/bap', [BAPController::class, 'simpan'])->name('post.bap');
    Route::put('/bap/{id}', [BAPController::class, 'update'])->name('put.bap');
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
    Route::get('/travel/export', [KanwilController::class, 'exportTravelPusat'])->name('travel.export');

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
    Route::get('/sertifikat/get-next-nomor', [SertifikatController::class, 'getNextNomor'])->name('sertifikat.get-next-nomor');
    Route::get('/sertifikat/{id}/view', [SertifikatController::class, 'view'])->name('sertifikat.view');
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
        Route::get('/cabang-travel/export', [KanwilController::class, 'exportTravelCabang'])->name('cabang.travel.export');
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
    Route::middleware(['admin', 'throttle:sensitive'])->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');

        // Legacy user listings (redirect-friendly)
        Route::get('/kabupaten', [UserManagementController::class, 'indexKabupaten'])->name('kabupaten.index');
        Route::get('/kabupaten/create', [UserManagementController::class, 'createKabupaten'])->name('kabupaten.create');
        Route::post('/kabupaten', [UserManagementController::class, 'storeKabupaten'])->name('kabupaten.store');

        // Travel User Management
        Route::get('/travel-users', [UserManagementController::class, 'indexTravel'])->name('travels.index');
        Route::get('/travel-users/create', [UserManagementController::class, 'createTravel'])->name('travels.create');
        Route::post('/travel-users', [UserManagementController::class, 'storeTravel'])->name('travels.store');
        
        // Travel User Import (PUSAT)
        Route::get('/travel-users/import', [UserManagementController::class, 'importTravelForm'])->name('travels.import.form');
        Route::post('/travel-users/import', [UserManagementController::class, 'importTravelUsers'])->name('travels.import');
        Route::get('/travel-users/template', [UserManagementController::class, 'downloadTravelUserTemplate'])->name('travels.template');
        
        // Travel User Import (CABANG)
        Route::get('/cabang-users/import', [UserManagementController::class, 'importCabangForm'])->name('cabang.import.form');
        Route::post('/cabang-users/import', [UserManagementController::class, 'importCabangUsers'])->name('cabang.import');
        Route::get('/cabang-users/template', [UserManagementController::class, 'downloadCabangUserTemplate'])->name('cabang.template');

        // General User Management
        Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });
});

// API routes for province, city, and district selection
Route::middleware('throttle:public')->group(function () {
    Route::get('/api/provinces', [ApiController::class, 'getProvinces'])->name('api.provinces');
    Route::get('/api/cities', [ApiController::class, 'getCities'])->name('api.cities');
    Route::get('/api/districts', [ApiController::class, 'getDistricts'])->name('api.districts');
});

// Public verification routes (no authentication required)
Route::middleware('throttle:public')->group(function () {
    Route::get('/verifikasi-sertifikat/{uuid}', [SertifikatController::class, 'verifikasi'])->name('sertifikat.verifikasi')->whereUuid('uuid');
    Route::post('/bap/verify-qr', [BAPController::class, 'verifyQRCode'])->name('bap.verify-qr');
    Route::get('/verify-e-sign', [BAPController::class, 'showVerifyQR'])->name('verify-e-sign');
    Route::get('/public/verify-e-sign', [BAPController::class, 'showVerifyQRPublic'])->name('verify-e-sign.public');

    Route::get('/public/pengaduan/{token}/download-pdf', [PengaduanController::class, 'downloadPDFPublic'])->name('pengaduan.download-pdf.public')->whereUuid('token');
    Route::get('/pengaduan-public/{token}', [PengaduanController::class, 'publicView'])->name('pengaduan.public')->whereUuid('token');
    Route::post('/pengaduan-public', [PengaduanController::class, 'storePublic'])->name('pengaduan.store-public');
});

Route::get('/storage-link', [UtilityController::class, 'storageLink'])->name('utility.storage-link');

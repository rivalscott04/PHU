<?php

use App\Http\Controllers\AuthController;
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
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\JamaahController;
use App\Http\Controllers\KanwilController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\UserProfileController;


Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');
Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest')->name('register.perform');
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login.perform');
Route::get('/dashboard', [HomeController::class, 'index'])->name('home')->middleware('auth', 'password.changed');

Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('user.changePassword');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('user.updatePassword');

Route::get('/test', function () {
    return 'Middleware test';
})->middleware('auth', 'password.changed');


Route::group(['middleware' => ['auth', 'password.changed']], function () {
    Route::get('/virtual-reality', [PageController::class, 'vr'])->name('virtual-reality');
    Route::get('/rtl', [PageController::class, 'rtl'])->name('rtl');
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile-static', [PageController::class, 'profile'])->name('profile-static');
    Route::get('/sign-in-static', [PageController::class, 'signin'])->name('sign-in-static');
    Route::get('/sign-up-static', [PageController::class, 'signup'])->name('sign-up-static');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/jamaah', [JamaahController::class, 'index'])->name('jamaah');
    Route::get('/jamaah/{jamaah}', [JamaahController::class, 'show'])->name('jamaah.detail');
    Route::post('/jamaah/import', [JamaahController::class, 'import'])->name('jamaah.import');
    Route::get('/jamaah/export', [JamaahController::class, 'export'])->name('jamaah.export');
    Route::get('/jamaah/template', [JamaahController::class, 'downloadTemplate'])->name('jamaah.template');

    Route::put('/pengajuan/{id}/status', [KanwilController::class, 'updateStatus'])->name('update.status');

    Route::get('/bap', [BAPController::class, 'index'])->name('bap');
    Route::get('/form-bap', [BAPController::class, 'showFormBAP'])->name('form.bap');
    Route::post('/bap', [BAPController::class, 'simpan'])->name('post.bap');
    Route::get('/cetak-bap/{id}', [BAPController::class, 'printBAP'])->name('cetak.bap');
    Route::get('/bap/detail/{id}', [BAPController::class, 'detail'])->name('detail.bap');
    Route::post('bap/upload/{id}', [BAPController::class, 'uploadPDF'])->name('bap.upload');
    Route::post('bap/ajukan/{id}', [BAPController::class, 'ajukan'])->name('bap.ajukan');
    Route::post('bap/update-status/{id}', [BAPController::class, 'updateStatus'])->name('bap.updateStatus');

    Route::get('/travel', [KanwilController::class, 'showTravel'])->name('travel');
    Route::get('/travel/form', [KanwilController::class, 'showFormTravel'])->name('form.travel');
    Route::post('/travel/form', [KanwilController::class, 'store'])->name('post.travel');
    Route::get('/cabang-travel', [KanwilController::class, 'showCabangTravel'])->name('cabang.travel');
    Route::get('/cabang-travel/form', [KanwilController::class, 'createCabangTravel'])->name('form.cabang_travel');
    Route::post('/cabang-travel/form', [KanwilController::class, 'storeCabangTravel'])->name('post.cabang_travel');


    Route::get('import-form', [ExcelImportController::class, 'importForm'])->name('import.form');
    Route::post('import-data', [ExcelImportController::class, 'import'])->name('import.data');

    Route::middleware(['admin'])->prefix('kanwil')->group(function () {
        Route::get('/form', [KanwilController::class, 'showForm'])->name('form');
        Route::post('/form', [KanwilController::class, 'store'])->name('post.form');

        Route::get('/travels', [AuthController::class, 'showUsers'])->name('travels');

        Route::get('/tambah-akun-travel', [AuthController::class, 'showForm'])->name('form.addUser');
        Route::post('/tambah-akun-travel', [AuthController::class, 'addUser'])->name('addUser');
        Route::put('/reset-password/{id}', [AuthController::class, 'resetPassword'])->name('resetPassword');
    });
});

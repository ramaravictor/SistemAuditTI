<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\JawabanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\CobitItemController;
use App\Http\Controllers\QuisionerController;
use App\Http\Controllers\UserProgressController;
use App\Http\Controllers\Admin\ProgressController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ResubmissionRequestController;
use App\Mail\NewUserForApproval;
use Illuminate\Support\Facades\Mail;

// Main routes
Route::get('/', function () {
    return view('auth/login');
});

Route::get('/menunggu-persetujuan', [PageController::class, 'pending'])->name('registration.pending');


// Dashboard yang bisa diakses semua user yg sudah login & verified
// web.php

// INI YANG BENAR
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');
});

// User profile routes - semua user yg login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-email', function () {
    // Ambil user admin pertama sebagai contoh penerima
    $admin = User::where('role', 'admin')->first();

    // Jika tidak ada admin, buat user dummy untuk tes
    if (!$admin) {
        return "Gagal: Tidak ada user dengan role 'admin' di database untuk dijadikan penerima tes.";
    }

    // Buat user dummy sebagai pendaftar baru
    $dummyUser = new User([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    try {
        // Kirim email menggunakan Mailable Anda
        Mail::to($admin->email)->send(new NewUserForApproval($dummyUser));
        return "Email tes berhasil dikirim ke " . $admin->email . "! Silakan periksa inbox Mailtrap Anda.";
    } catch (\Exception $e) {
        // Jika ada error, tampilkan pesannya
        return "Gagal mengirim email. Error: " . $e->getMessage();
    }
});
// Admin routes - hanya untuk role admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('admin/progress', [ProgressController::class, 'index'])->name('admin.progress.index');
    // Route::get('admin/progress/{user}/pdf', [ProgressController::class, 'exportPdf'])->name('progress.exportPdf');
    Route::get('/admin/progress/{user}/pdf', [\App\Http\Controllers\Admin\ProgressController::class, 'downloadPDF'])->name('admin.progress.downloadPDF');

    Route::get('/progress/{user}', [ProgressController::class, 'show'])->name('admin.progress.show');
    // Admin dashboard
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // User approval
    // Route::get('/admin/approvals', [AdminController::class, 'showApprovals'])->name('admin.approvals');
    // Route::patch('/admin/approve/{id}', [AdminController::class, 'approveUser'])->name('admin.approve');

    Route::get('/approvals', [UserApprovalController::class, 'index'])->name('admin.approvals.index');

    Route::post('/approvals/{user}', [UserApprovalController::class, 'approve'])->name('admin.approvals.approve');
    Route::post('/approvals/{user}/reject', [UserApprovalController::class, 'reject'])->name('admin.approvals.reject');


    //manage user
    Route::resource('users', UserController::class);

    // CRUD Cobit Item
    Route::resource('cobititem', CobitItemController::class);

    // Resource routes kategori, level, quisioner
    Route::resource('kategori', KategoriController::class);
    Route::resource('level', LevelController::class);
    Route::resource('quisioner', QuisionerController::class);

    Route::get('/resubmission-requests', [ResubmissionRequestController::class, 'adminIndex'])->name('resubmissions.index');
    Route::post('/resubmission-requests/{resubmissionRequest}/approve', [ResubmissionRequestController::class, 'approve'])->name('resubmissions.approve');
    Route::post('/resubmission-requests/{resubmissionRequest}/reject', [ResubmissionRequestController::class, 'reject'])->name('resubmissions.reject');

    Route::post('/import', [ExcelController::class,'import'])->name('excel.import');
    Route::get('/import', [ExcelController::class,'index'])->name('excel.index');

    // Route untuk Halaman Profil Admin
    Route::get('/profileadmin', [AdminProfileController::class, 'edit'])->name('profileadmin.edit');
    Route::patch('/profileadmin', [AdminProfileController::class, 'update'])->name('profileadmin.update');
});

// User routes - hanya untuk role user
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
    Route::post('/levels/{level}/request-resubmission', [ResubmissionRequestController::class, 'store'])->name('resubmission.request');

    // Audit routes user
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('/audit/{cobitItem}', [AuditController::class, 'showCategories'])->name('audit.showCategories');
    Route::get('/audit/{cobitItem}/{kategori}', [AuditController::class, 'showLevels'])->name('audit.showLevels');
    Route::get('/audit/{cobitItem}/{kategori}/{level}', [AuditController::class, 'showQuisioner'])->name('audit.showQuisioner');
    Route::post('/audit/{level}/jawaban', [JawabanController::class, 'store'])->name('jawaban.store');
    Route::get('/progress', [UserProgressController::class, 'index'])->name('user.progress.index');
    Route::get('/my-progress', [UserProgressController::class, 'downloadPDF'])->name('user.progress.download');
});

// Halaman waiting approval
Route::get('/waiting-approval', function () {
    return view('waiting-approval');
})->name('waiting-approval');

require __DIR__ . '/auth.php';

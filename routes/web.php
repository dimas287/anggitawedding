    <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvitationOrderController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\InvitationController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\User\DocumentController;
use App\Http\Controllers\User\ProfileCompletionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\RundownController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\InvitationController as AdminInvitationController;
use App\Http\Controllers\Admin\InvitationBookingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\Admin\PortfolioMediaController;
use App\Http\Controllers\Admin\AccountController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// ============================================================
// PUBLIC ROUTES
// ============================================================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/fix-db', function () {
    if (!\Illuminate\Support\Facades\Schema::hasColumn('posts', 'views')) {
        \Illuminate\Support\Facades\Schema::table('posts', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->unsignedBigInteger('views')->default(0)->after('is_published');
        });
        return 'Kolom views berhasil ditambahkan ke database!';
    }
    return 'Kolom views sudah ada di database.';
});

Route::get('/fix-assets', function () {
    $results = [];
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
    $laravelPublic = public_path();

    $results[] = "Document Root (Server): " . $docRoot;
    $results[] = "Laravel Public Path: " . $laravelPublic;

    if (realpath($docRoot) !== realpath($laravelPublic)) {
        $results[] = "PERBEDAAN PATH TERDETEKSI! Server menggunakan folder berbeda dari Laravel.";
        
        // Fix Build
        if (file_exists("$laravelPublic/build")) {
            \Illuminate\Support\Facades\File::copyDirectory("$laravelPublic/build", "$docRoot/build");
            $results[] = "✅ Copy 'build' folder ke Document Root BERHASIL.";
        }

        // Fix Storage
        if (!file_exists("$docRoot/storage")) {
            try {
                symlink(storage_path('app/public'), "$docRoot/storage");
                $results[] = "✅ Symlink 'storage' di Document Root BERHASIL dibuat.";
            } catch (\Exception $e) {
                $results[] = "❌ Gagal membuat symlink: " . $e->getMessage();
            }
        } else {
            $results[] = "✅ Symlink 'storage' sudah ada di Document Root.";
        }
    } else {
        $results[] = "Path sudah sama, tidak perlu sinkronisasi ke public_html.";
        
        try {
            \Illuminate\Support\Facades\Artisan::call('storage:link');
            $results[] = "Storage Link: " . \Illuminate\Support\Facades\Artisan::output();
        } catch (\Exception $e) {
            $results[] = "Storage Link Error: " . $e->getMessage();
        }
    }

    $cssPath = "$docRoot/build/assets/app-CMB86oAG.css";
    $exists = file_exists($cssPath);
    $results[] = "CSS File di Server ($cssPath): " . ($exists ? 'Yes' : 'No');

    if ($exists) {
        chmod($cssPath, 0644);
        $results[] = "Permissions fixed.";
    }

    return implode("<br><br>", $results);
});
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/paket', [LandingController::class, 'packages'])->name('packages');
Route::get('/portofolio', [LandingController::class, 'portfolio'])->name('portfolio');
Route::get('/faq', [LandingController::class, 'faq'])->name('faq');
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
Route::get('/undangan-digital', [LandingController::class, 'digitalInvitations'])->middleware('invitation.maintenance')->name('digital-invitations');
Route::view('/undangan-maintenance', 'invitation-maintenance')->name('invitation.maintenance');
Route::view('/mulai-booking', 'select-service')->name('booking.start');
Route::get('/checkout/undangan-digital', [InvitationOrderController::class, 'start'])->middleware('invitation.maintenance')->name('invitation-order.start');

// Date availability check (AJAX)
Route::get('/booking/check-date', [BookingController::class, 'checkDate'])->middleware('throttle:booking')->name('booking.check-date');

// Consultation form (accessible without login, but needs login to submit)
Route::get('/konsultasi', [ConsultationController::class, 'form'])->name('consultation.form');
Route::post('/konsultasi', [ConsultationController::class, 'store'])->middleware(['throttle:5,1', 'honeypot'])->name('consultation.store');

// Digital Invitation (public)
Route::middleware('invitation.maintenance')->group(function () {
    Route::get('/undangan/{slug}', function ($slug) {
        $invitation = \App\Models\Invitation::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $invitation->increment('view_count');
        return view('invitation.react', compact('invitation'));
    })->name('invitation.show');
    Route::post('/undangan/{slug}/rsvp', [\App\Http\Controllers\User\InvitationController::class, 'rsvp'])->middleware(['throttle:5,1', 'honeypot'])->name('invitation.rsvp');
    Route::get('/undangan/{slug}/qris', [InvitationController::class, 'qrisImage'])->name('invitation.qris');
    Route::get('/undangan/{slug}/media/{type}', [\App\Http\Controllers\InvitationMediaController::class, 'serve'])->name('invitation.media');
});

// ============================================================
// AUTH ROUTES
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/anggita-access', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/anggita-access', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
    Route::get('/anggita-register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/anggita-register', [AuthController::class, 'register'])->middleware('throttle:3,1')->name('register.post');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin password confirmation (email verification link)
Route::get('/admin/password/confirm/{token}', [AccountController::class, 'confirmPasswordChange'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('admin.password.confirm');

// ============================================================
// EMAIL VERIFICATION
// ============================================================
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    $redirect = $request->user()->isAdmin()
        ? route('admin.dashboard')
        : route('user.dashboard');

    return redirect($redirect)->with('success', 'Email Anda berhasil diverifikasi.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return back()->with('info', 'Email Anda sudah terverifikasi.');
    }

    try {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Link verifikasi baru telah dikirim.');
    } catch (\Throwable $e) {
        \Log::error('Verification mail failed', [
            'user_id' => $request->user()->id,
            'error' => $e->getMessage(),
        ]);
        return back()->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi beberapa menit.');
    }
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ============================================================
// BOOKING (requires auth)
// ============================================================
Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::get('/booking/pilih-paket', [BookingController::class, 'selectPackage'])->name('booking.select-package');
    Route::get('/booking/form', [BookingController::class, 'form'])->name('booking.form');
    Route::post('/booking', [BookingController::class, 'store'])->middleware(['throttle:5,1', 'honeypot'])->name('booking.store');

    // Invitation only checkout
    Route::post('/checkout/undangan-digital', [InvitationOrderController::class, 'checkout'])->middleware('invitation.maintenance')->name('invitation-order.checkout');

    // Payment
    Route::get('/payment/{booking}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/{booking}/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::post('/payment/{booking}/manual', [PaymentController::class, 'manual'])->name('payment.manual');
    Route::get('/payment/{booking}/success', [PaymentController::class, 'success'])->name('payment.success');
});

// Midtrans webhook dipindah ke routes/api.php agar tidak terkena CSRF protection

// ============================================================
// USER DASHBOARD
// ============================================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/lengkapi-profil', [ProfileCompletionController::class, 'show'])->name('user.profile.complete');
    Route::post('/lengkapi-profil', [ProfileCompletionController::class, 'store'])->name('user.profile.complete.store');
});

Route::middleware(['auth', 'verified', 'profile.complete'])->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profil', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [DashboardController::class, 'changePassword'])->name('profile.password');

    Route::get('/booking/{booking}', [DashboardController::class, 'bookingShow'])->name('booking.show');
    Route::get('/booking/{booking}/invoice', [DashboardController::class, 'downloadInvoice'])->name('booking.invoice');
    Route::post('/booking/{booking}/fitting', [DashboardController::class, 'storeFitting'])->name('booking.fitting.store');
    Route::delete('/booking/{booking}/fitting/{fitting}', [DashboardController::class, 'deleteFitting'])->name('booking.fitting.delete');
    Route::put('/booking/{booking}/change-package', [DashboardController::class, 'changePackage'])->name('booking.change-package');
    Route::put('/booking/{booking}/reschedule', [DashboardController::class, 'rescheduleBooking'])->name('booking.reschedule');
    Route::post('/booking/{booking}/cancel', [DashboardController::class, 'cancelBooking'])->name('booking.cancel');
    Route::post('/booking/{booking}/review', [DashboardController::class, 'review'])->name('booking.review');

    // Invitation management
    Route::get('/booking/{booking}/undangan', [InvitationController::class, 'index'])->name('invitation.index');
    Route::get('/booking/{booking}/undangan/edit', [InvitationController::class, 'edit'])->name('invitation.edit');
    Route::put('/booking/{booking}/undangan', [InvitationController::class, 'update'])->name('invitation.update');
    Route::post('/booking/{booking}/undangan/publish', [InvitationController::class, 'publish'])->name('invitation.publish');
    Route::get('/booking/{booking}/undangan/rsvp', [InvitationController::class, 'rsvpStats'])->name('invitation.rsvp');

    // Chat
    Route::get('/booking/{booking}/chat', [UserChatController::class, 'index'])->name('chat.index');
    Route::post('/booking/{booking}/chat', [UserChatController::class, 'send'])->middleware('throttle:chat')->name('chat.send');
    Route::get('/booking/{booking}/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/booking/{booking}/chat/typing', [UserChatController::class, 'typing'])->name('chat.typing');
    Route::get('/chat/attachment/{chat}', [UserChatController::class, 'downloadAttachment'])->name('chat.download');

    // Documents
    Route::post('/booking/{booking}/dokumen', [DocumentController::class, 'store'])->name('document.store');
    Route::get('/dokumen/{document}/download', [DocumentController::class, 'download'])->name('document.download');
    Route::delete('/dokumen/{document}', [DocumentController::class, 'destroy'])->name('document.destroy');

});

// ============================================================
// ADMIN PANEL
// ============================================================
Route::middleware(['auth', 'verified', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Blog / Articles
    Route::post('posts/upload', [\App\Http\Controllers\Admin\PostController::class, 'uploadImage'])->name('posts.upload');
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);

    // Account Settings
    Route::get('/pengaturan-akun', [AccountController::class, 'settings'])->name('account.settings');
    Route::post('/pengaturan-akun/password', [AccountController::class, 'requestPasswordChange'])->name('account.password.request');

    Route::get('/aktivitas', [\App\Http\Controllers\Admin\AdminActivityController::class, 'index'])->name('activities.index');
    Route::get('/aktivitas/export', [\App\Http\Controllers\Admin\AdminActivityController::class, 'export'])->name('activities.export');

    // Bookings
    Route::get('/booking', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::post('/booking', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::get('/booking/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::delete('/booking/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/booking/{booking}/invoice-email', [AdminBookingController::class, 'sendInvoiceEmail'])->name('bookings.invoice-email');
    Route::put('/booking/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
    Route::post('/booking/{booking}/payment-offline', [AdminBookingController::class, 'addOfflinePayment'])->name('bookings.payment-offline');
    Route::post('/booking/{booking}/extra-charge', [AdminBookingController::class, 'addExtraCharge'])->name('bookings.extra-charge');
    Route::put('/booking/{booking}/extra-charge/{charge}', [AdminBookingController::class, 'updateExtraCharge'])->name('bookings.extra-charge.update');
    Route::delete('/booking/{booking}/extra-charge/{charge}', [AdminBookingController::class, 'deleteExtraCharge'])->name('bookings.extra-charge.delete');
    Route::post('/booking/{booking}/fitting', [AdminBookingController::class, 'addFitting'])->name('bookings.fitting.store');
    Route::delete('/booking/{booking}/fitting/{fitting}', [AdminBookingController::class, 'deleteFitting'])->name('bookings.fitting.delete');
    Route::post('/booking/{booking}/wardrobe', [AdminBookingController::class, 'addWardrobeItem'])->name('bookings.wardrobe.store');
    Route::delete('/booking/{booking}/wardrobe/{item}', [AdminBookingController::class, 'deleteWardrobeItem'])->name('bookings.wardrobe.delete');
    Route::put('/booking/{booking}/notes', [AdminBookingController::class, 'updateAdminNotes'])->name('bookings.notes');
    Route::put('/booking/{booking}/info', [AdminBookingController::class, 'updateInfo'])->name('bookings.info');
    Route::post('/konsultasi/{consultation}/convert', [AdminBookingController::class, 'convertConsultation'])->name('consultations.convert');

    Route::get('/booking-undangan', [InvitationBookingController::class, 'index'])->name('invitation-bookings.index');
    Route::post('/booking-undangan', [InvitationBookingController::class, 'store'])->name('invitation-bookings.store');

    // Consultations
    Route::get('/konsultasi', [AdminConsultationController::class, 'index'])->name('consultations.index');
    Route::post('/konsultasi', [AdminConsultationController::class, 'store'])->name('consultations.store');
    Route::get('/konsultasi/{consultation}', [AdminConsultationController::class, 'show'])->name('consultations.show');
    Route::put('/konsultasi/{consultation}/status', [AdminConsultationController::class, 'updateStatus'])->name('consultations.status');
    Route::put('/konsultasi/{consultation}/reschedule', [AdminConsultationController::class, 'reschedule'])->name('consultations.reschedule');
    Route::put('/konsultasi/{consultation}/notes', [AdminConsultationController::class, 'saveMeetingNotes'])->name('consultations.notes');
    Route::post('/konsultasi/{consultation}/reminder', [AdminConsultationController::class, 'sendReminder'])->name('consultations.reminder');

    // Vendors
    Route::get('/vendor', [VendorController::class, 'index'])->name('vendors.index');
    Route::post('/vendor', [VendorController::class, 'store'])->name('vendors.store');
    Route::put('/vendor/{vendor}', [VendorController::class, 'update'])->name('vendors.update');
    Route::delete('/vendor/{vendor}', [VendorController::class, 'destroy'])->name('vendors.destroy');
    Route::post('/booking/{booking}/vendor', [VendorController::class, 'assignToBooking'])->name('vendors.assign');
    Route::put('/booking-vendor/{bookingVendor}', [VendorController::class, 'updateBookingVendor'])->name('vendors.booking-update');
    Route::delete('/booking-vendor/{bookingVendor}', [VendorController::class, 'removeFromBooking'])->name('vendors.booking-remove');

    // Financial
    Route::get('/keuangan', [FinancialController::class, 'index'])->name('financial.index');
    Route::post('/keuangan', [FinancialController::class, 'store'])->name('financial.store');
    Route::delete('/keuangan/{financialTransaction}', [FinancialController::class, 'destroy'])->name('financial.destroy');
    Route::get('/keuangan/booking/{booking}', [FinancialController::class, 'bookingFinancial'])->name('financial.booking');
    Route::get('/keuangan/export-pdf', [FinancialController::class, 'exportPdf'])->name('financial.export-pdf');

    // Admin Chat Download
    Route::get('/chat/attachment/{chat}', [\App\Http\Controllers\Admin\ChatController::class, 'downloadAttachment'])->name('chat.download');

    // Secure Storage Downloads
    Route::get('/secure/vendor-proof/{bookingVendor}', [\App\Http\Controllers\Admin\SecureDownloadController::class, 'downloadVendorProof'])->name('secure.vendor-proof');
    Route::get('/secure/payment-proof/{payment}', [\App\Http\Controllers\Admin\SecureDownloadController::class, 'downloadPaymentProof'])->name('secure.payment-proof');

    // Rundown
    Route::post('/booking/{booking}/rundown', [RundownController::class, 'store'])->name('rundown.store');
    Route::put('/rundown/{rundown}', [RundownController::class, 'update'])->name('rundown.update');
    Route::delete('/rundown/{rundown}', [RundownController::class, 'destroy'])->name('rundown.destroy');
    Route::post('/booking/{booking}/rundown/reorder', [RundownController::class, 'reorder'])->name('rundown.reorder');
    Route::get('/booking/{booking}/rundown/pdf', [RundownController::class, 'exportPdf'])->name('rundown.pdf');

    // Payments
    Route::put('/payments/{payment}', [AdminPaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [AdminPaymentController::class, 'destroy'])->name('payments.destroy');

    // Calendar
    Route::get('/kalender', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/kalender/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Chat
    Route::get('/pesan', [AdminChatController::class, 'allInbox'])->name('chat.inbox');
    Route::get('/booking/{booking}/pesan', [AdminChatController::class, 'index'])->name('chat.index');
    Route::post('/booking/{booking}/pesan', [AdminChatController::class, 'send'])->name('chat.send');
    Route::get('/booking/{booking}/pesan/messages', [AdminChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/booking/{booking}/pesan/typing', [AdminChatController::class, 'typing'])->name('chat.typing');

    // Clients
    Route::get('/klien', [AdminUserController::class, 'index'])->name('clients.index');
    Route::get('/klien/{user}', [AdminUserController::class, 'show'])->name('clients.show');

    // Invitations / Templates
    Route::get('/template-undangan', [AdminInvitationController::class, 'templates'])->name('invitation.templates');
    Route::post('/template-undangan', [AdminInvitationController::class, 'storeTemplate'])->name('invitation.templates.store');
    Route::put('/template-undangan/{template}', [AdminInvitationController::class, 'updateTemplate'])->name('invitation.templates.update');
    Route::delete('/template-undangan/{template}', [AdminInvitationController::class, 'destroyTemplate'])->name('invitation.templates.destroy');
    Route::get('/booking/{booking}/undangan', [AdminInvitationController::class, 'clientInvitation'])->name('invitation.client');
    Route::put('/booking/{booking}/undangan', [AdminInvitationController::class, 'updateClientInvitation'])->name('invitation.client.update');
    Route::post('/booking/{booking}/undangan/reset-link', [AdminInvitationController::class, 'resetLink'])->name('invitation.reset-link');

    // Packages Management
    Route::resource('packages', AdminPackageController::class)->except(['show', 'create', 'edit']);
    Route::delete('packages/{package}/media/{media}', [AdminPackageController::class, 'deleteMedia'])->name('packages.media.destroy');
    Route::post('packages/{package}/media/reorder', [AdminPackageController::class, 'reorderMedia'])->name('packages.media.reorder');
    Route::get('packages/{package}/poster', [AdminPackageController::class, 'downloadPoster'])->name('packages.poster');

    Route::resource('hero-slides', HeroSlideController::class)->except(['create', 'edit', 'show']);
    Route::get('site-content', [SiteContentController::class, 'edit'])->name('site-content.edit');
    Route::post('site-content/hero', [SiteContentController::class, 'updateHero'])->name('site-content.hero');
    Route::post('site-content/dream', [SiteContentController::class, 'updateDream'])->name('site-content.dream');
    Route::post('site-content/process', [SiteContentController::class, 'updateProcess'])->name('site-content.process');
    Route::post('site-content/highlight-cards', [SiteContentController::class, 'storeHighlightCard'])->name('site-content.highlight-cards.store');
    Route::put('site-content/highlight-cards/{card}', [SiteContentController::class, 'updateHighlightCard'])->name('site-content.highlight-cards.update');
    Route::delete('site-content/highlight-cards/{card}', [SiteContentController::class, 'destroyHighlightCard'])->name('site-content.highlight-cards.destroy');
    Route::post('site-content/stats', [SiteContentController::class, 'updateStats'])->name('site-content.stats');
    Route::post('site-content/portfolio-stats', [SiteContentController::class, 'updatePortfolioStats'])->name('site-content.portfolio-stats');
    Route::post('site-content/footer', [SiteContentController::class, 'updateFooter'])->name('site-content.footer');
    Route::post('site-content/brand', [SiteContentController::class, 'updateBrand'])->name('site-content.brand');
    Route::post('site-content/consultation', [SiteContentController::class, 'updateConsultationSettings'])->name('site-content.consultation');
    Route::post('site-content/maintenance', [SiteContentController::class, 'updateMaintenance'])->name('site-content.maintenance');
    Route::post('/portofolio/{portfolioImage}/media', [PortfolioMediaController::class, 'store'])->name('portfolio.media.store');
    Route::delete('/portofolio/{portfolioImage}/media/{media}', [PortfolioMediaController::class, 'destroy'])->name('portfolio.media.destroy');

    // Portfolio Images
    Route::get('/portofolio', [\App\Http\Controllers\Admin\PortfolioImageController::class, 'index'])->name('portfolio.index');
    Route::post('/portofolio', [\App\Http\Controllers\Admin\PortfolioImageController::class, 'store'])->name('portfolio.store');
    Route::put('/portofolio/{portfolioImage}', [\App\Http\Controllers\Admin\PortfolioImageController::class, 'update'])->name('portfolio.update');
    Route::delete('/portofolio/{portfolioImage}', [\App\Http\Controllers\Admin\PortfolioImageController::class, 'destroy'])->name('portfolio.destroy');

    // Reports / PDF
    Route::get('/booking/{booking}/laporan-pdf', [ReportController::class, 'eventReport'])->name('reports.event');
    Route::get('/booking/{booking}/invoice-pdf', [ReportController::class, 'invoicePdf'])->name('reports.invoice');

});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvitationPublicController;
use App\Http\Controllers\Api\GuestbookController;
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Undangan Digital (Public API)
Route::middleware('invitation.maintenance')->group(function () {
    Route::get('/invitations/{slug}', [InvitationPublicController::class, 'show'])->middleware('throttle:api_heavy');
    Route::get('/invitations/{slug}/stats', [InvitationPublicController::class, 'stats'])->middleware('throttle:api_heavy');
    Route::post('/invitations/{slug}/rsvp', [InvitationPublicController::class, 'storeRsvp'])->middleware(['throttle:5,1', 'honeypot']);

    // Guestbook
    Route::get('/invitations/{slug}/guestbook', [GuestbookController::class, 'index'])->middleware('throttle:api_heavy');
    Route::post('/invitations/{slug}/guestbook', [GuestbookController::class, 'store'])->middleware(['throttle:20,1', 'honeypot']);
});

// Midtrans Payment Webhook — harus di api.php agar tidak terkena CSRF protection
Route::post('/payment/notification', [PaymentController::class, 'notification'])
    ->middleware('throttle:60,1')
    ->name('payment.notification');

<?php

use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\FreelancerProfileController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HireRequestController;
use App\Http\Controllers\Workspace\AcbaBillingController;
use App\Http\Controllers\Workspace\PayPalBillingController;
use App\Http\Controllers\Workspace\WeeklySubscriptionController;
use App\Http\Controllers\Workspace\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'site.index')->name('home');
Route::redirect('/index.html', '/');

Route::redirect('/how-it-works', '/how-it-works.html');
Route::view('/how-it-works.html', 'site.how-it-works')->name('how-it-works');

Route::redirect('/our-priorities', '/our-priorities.html');
Route::view('/our-priorities.html', 'site.our-priorities')->name('our-priorities');

Route::redirect('/categories', '/categories.html');
Route::view('/categories.html', 'site.categories')->name('categories');

Route::redirect('/terms', '/terms.html');
Route::view('/terms.html', 'site.terms')->name('terms');

Route::redirect('/privacy', '/privacy.html');
Route::view('/privacy.html', 'site.privacy')->name('privacy');

Route::redirect('/sitemap', '/sitemap.html');
Route::view('/sitemap.html', 'site.sitemap')->name('sitemap');

Route::redirect('/contact', '/contact.html');
Route::get('/contact.html', [ContactMessageController::class, 'create'])->name('contact.show');
Route::post('/contact.html', [ContactMessageController::class, 'store'])->name('contact.store');

Route::redirect('/start-hiring', '/client/register');
Route::redirect('/start-hiring.html', '/client/register')->name('hire.start');
Route::post('/start-hiring.html', fn () => redirect('/client/register'))->name('hire.store');
Route::get('/request-received.html', [HireRequestController::class, 'thankYou'])->name('hire.received');

Route::prefix('services')->name('services.')->group(function () {
    Route::view('/web-development.html', 'site.services.web-development')->name('web-development');
    Route::view('/mobile-development.html', 'site.services.mobile-development')->name('mobile-development');
    Route::view('/ecommerce.html', 'site.services.ecommerce')->name('ecommerce');
    Route::view('/ui-ux-design.html', 'site.services.ui-ux-design')->name('ui-ux-design');
});

Route::get('/freelancer/{freelancer}/index.html', [FreelancerProfileController::class, 'showById'])->whereNumber('freelancer')->name('freelancers.show-id');
Route::get('/freelancers/{slug}', [FreelancerProfileController::class, 'showBySlug'])->name('freelancers.show');

Route::get('/help', fn () => redirect('/help/index.html'));
Route::prefix('help')->name('help.')->group(function () {
    Route::view('/index.html', 'site.help.index')->name('index');
    Route::view('/getting-started-as-a-client.html', 'site.help.getting-started-as-a-client')->name('getting-started-as-a-client');
    Route::view('/how-to-write-a-strong-project-brief.html', 'site.help.how-to-write-a-strong-project-brief')->name('how-to-write-a-strong-project-brief');
    Route::view('/how-to-review-fit-and-compare-specialists.html', 'site.help.how-to-review-fit-and-compare-specialists')->name('how-to-review-fit-and-compare-specialists');
    Route::view('/making-an-offer-and-starting-work.html', 'site.help.making-an-offer-and-starting-work')->name('making-an-offer-and-starting-work');
    Route::view('/payments-limits-and-invoices.html', 'site.help.payments-limits-and-invoices')->name('payments-limits-and-invoices');
    Route::view('/ndas-ip-and-confidentiality.html', 'site.help.ndas-ip-and-confidentiality')->name('ndas-ip-and-confidentiality');
    Route::view('/support-and-safety.html', 'site.help.support-and-safety')->name('support-and-safety');
});

Route::redirect('/workspace', '/client-workspace.html');
Route::get('/client-workspace.html', [WorkspaceController::class, 'landing'])->name('workspace.index');

Route::prefix('client')->name('client.')->group(function () {
    Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ClientAuthController::class, 'register'])->middleware('throttle:5,1');

    Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClientAuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::get('/client/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/client/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/client/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/client/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::post('/client/logout', [ClientAuthController::class, 'logout'])->name('client.logout')->middleware('auth');

Route::get('/login-token/{token}', function (string $token) {
    $loginToken = \App\Models\LoginToken::where('token', $token)->first();

    if (! $loginToken || ! $loginToken->isValid()) {
        return redirect()->route('client.login')->with('error', 'This login link is invalid or has expired.');
    }

    $loginToken->update(['used_at' => now()]);

    \Illuminate\Support\Facades\Auth::login($loginToken->user);
    request()->session()->regenerate();

    return redirect()->route('workspace.dashboard');
})->name('login-token');

Route::middleware('auth')->group(function () {
    Route::post('/email/verification-send', [EmailVerificationController::class, 'send'])->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware('signed');
});

Route::redirect('/app', '/app/dashboard.html');
Route::middleware('auth')->prefix('app')->name('workspace.')->group(function () {
    Route::get('/welcome.html', [WorkspaceController::class, 'welcome'])->name('welcome');
    Route::get('/dashboard.html', [WorkspaceController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard-live.html', [WorkspaceController::class, 'dashboardLive'])->name('dashboard-live');

    Route::get('/hire-flow.html', [WorkspaceController::class, 'hireFlow'])->name('hire-flow');
    Route::post('/hire-flow.html', [WorkspaceController::class, 'storeBrief'])->name('hire-flow.store');

    Route::get('/invite-offer.html', [WorkspaceController::class, 'inviteOffer'])->name('invite-offer');
    Route::post('/invite-offer.html', [WorkspaceController::class, 'storeOffer'])->name('invite-offer.store');

    Route::get('/billing-method.html', [WorkspaceController::class, 'billingMethod'])->name('billing-method');
    Route::post('/billing-method.html', [WorkspaceController::class, 'storeBillingMethod'])->name('billing-method.store');
    Route::post('/billing-method/set-primary', [WorkspaceController::class, 'setPrimaryBillingMethod'])->name('billing-method.primary');
    Route::post('/billing-method/remove', [WorkspaceController::class, 'destroyBillingMethod'])->name('billing-method.destroy');
    Route::post('/billing-method/acba/start', [AcbaBillingController::class, 'start'])->name('billing-method.acba.start');
    Route::get('/billing-method/acba/return', [AcbaBillingController::class, 'handleReturn'])->name('billing-method.acba.return');
    Route::post('/billing-method/paypal/start', [PayPalBillingController::class, 'start'])->name('billing-method.paypal.start');
    Route::get('/billing-method/paypal/return', [PayPalBillingController::class, 'handleReturn'])->name('billing-method.paypal.return');
    Route::get('/billing-method/paypal/cancel', [PayPalBillingController::class, 'cancel'])->name('billing-method.paypal.cancel');

    Route::post('/weekly-subscription/start', [WeeklySubscriptionController::class, 'start'])->name('weekly-subscription.start');
    Route::get('/weekly-subscription/return', [WeeklySubscriptionController::class, 'handleReturn'])->name('weekly-subscription.return');
    Route::get('/weekly-subscription/cancel', [WeeklySubscriptionController::class, 'cancelReturn'])->name('weekly-subscription.cancel');

    Route::get('/invoice-details.html', [WorkspaceController::class, 'invoiceDetails'])->name('invoice-details');
    Route::post('/invoice-details.html', [WorkspaceController::class, 'storeInvoiceDetails'])->name('invoice-details.store');

    Route::get('/project-pending.html', [WorkspaceController::class, 'projectPending'])->name('project-pending');
    Route::post('/project-pending/activate', [WorkspaceController::class, 'activateProject'])->name('project.activate');

    Route::get('/project-active.html', [WorkspaceController::class, 'projectActive'])->name('project-active');
    Route::post('/project-active/close', [WorkspaceController::class, 'closeProject'])->name('project.close');
    Route::post('/project-active/update-weekly-limit', [WorkspaceController::class, 'updateWeeklyLimit'])->name('project.update-weekly-limit');
    Route::post('/project-active/pay-bonus', [WorkspaceController::class, 'payBonus'])->name('project.pay-bonus');
    Route::get('/project-active/bonus-return/{bonus}', [WorkspaceController::class, 'bonusReturn'])->name('project.bonus-return');
    Route::get('/project-active/bonus-cancel/{bonus}', [WorkspaceController::class, 'bonusCancel'])->name('project.bonus-cancel');

    Route::post('/project-active/pay-now', [WorkspaceController::class, 'payNow'])->name('project.pay-now');
    Route::get('/project-active/pay-now-return', [WorkspaceController::class, 'payNowReturn'])->name('project.pay-now-return');
    Route::get('/project-active/pay-now-cancel', [WorkspaceController::class, 'payNowCancel'])->name('project.pay-now-cancel');

    Route::get('/messages.html', [WorkspaceController::class, 'messages'])->name('messages');
    Route::post('/messages.html', [WorkspaceController::class, 'storeMessage'])->name('messages.store');

    Route::get('/reports.html', [WorkspaceController::class, 'reports'])->name('reports');
    Route::get('/invoice/{invoice}', [WorkspaceController::class, 'viewInvoice'])->name('invoice.view');

    Route::get('/settings.html', [WorkspaceController::class, 'settings'])->name('settings');
    Route::post('/settings.html', [WorkspaceController::class, 'updateSettings'])->name('settings.update');
});

Route::view('/404.html', 'errors.404')->name('preview.404');
Route::fallback(fn () => response()->view('errors.404', [], 404));

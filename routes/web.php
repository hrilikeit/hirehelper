<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HireRequestController;
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

Route::redirect('/start-hiring', '/client/register', 302);
Route::get('/start-hiring.html', fn () => redirect('/client/register'))->name('hire.start');
Route::post('/start-hiring.html', fn () => redirect('/client/register'))->name('hire.store');
Route::get('/request-received.html', [HireRequestController::class, 'thankYou'])->name('hire.received');

Route::prefix('services')->name('services.')->group(function () {
    Route::view('/web-development.html', 'site.services.web-development')->name('web-development');
    Route::view('/mobile-development.html', 'site.services.mobile-development')->name('mobile-development');
    Route::view('/ecommerce.html', 'site.services.ecommerce')->name('ecommerce');
    Route::view('/ui-ux-design.html', 'site.services.ui-ux-design')->name('ui-ux-design');
});

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
Route::view('/client-workspace.html', 'workspace.index')->name('workspace.index');
Route::redirect('/app', '/app/dashboard.html');
Route::prefix('app')->name('workspace.')->group(function () {
    Route::view('/welcome.html', 'workspace.app.welcome')->name('welcome');
    Route::view('/dashboard.html', 'workspace.app.dashboard')->name('dashboard');
    Route::view('/dashboard-live.html', 'workspace.app.dashboard-live')->name('dashboard-live');
    Route::view('/invite-offer.html', 'workspace.app.invite-offer')->name('invite-offer');
    Route::view('/billing-method.html', 'workspace.app.billing-method')->name('billing-method');
    Route::view('/project-pending.html', 'workspace.app.project-pending')->name('project-pending');
    Route::view('/project-active.html', 'workspace.app.project-active')->name('project-active');
    Route::view('/messages.html', 'workspace.app.messages')->name('messages');
    Route::view('/reports.html', 'workspace.app.reports')->name('reports');
    Route::view('/settings.html', 'workspace.app.settings')->name('settings');
    Route::view('/hire-flow.html', 'workspace.app.hire-flow')->name('hire-flow');
});

Route::view('/404.html', 'errors.404')->name('preview.404');
Route::fallback(fn () => response()->view('errors.404', [], 404));

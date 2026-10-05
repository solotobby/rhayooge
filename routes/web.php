<?php

use App\Http\Controllers\PaystackController;
use App\Http\Controllers\SocialAuthController;
use App\Livewire\Admin\Clients;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Executives;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\Messages;
use App\Livewire\Admin\MessageShow;
use App\Livewire\Admin\Orders;
use App\Livewire\Admin\OrderShow;
use App\Livewire\Admin\ProductForm;
use App\Livewire\Admin\Products;
use App\Livewire\Auth\Account;
use App\Livewire\Auth\Profile;
use App\Livewire\Executive\MagicLogin;
use App\Livewire\Executive\Portal as ExecutivePortal;
use App\Livewire\Store\About;
use App\Livewire\Store\Checkout;
use App\Livewire\Store\Contact;
use App\Livewire\Store\Home;
use App\Livewire\Store\ProductShow;
use App\Livewire\Store\Shop;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/shop', Shop::class)->name('shop');
Route::get('/shop/{product}', ProductShow::class)->name('product');
Route::get('/about', About::class)->name('about');
Route::get('/contact', Contact::class)->name('contact');
Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/account', Account::class)->name('account');

// Paystack Gateway Endpoints
Route::get('/paystack/callback', [PaystackController::class, 'callback'])->name('paystack.callback');
Route::post('/paystack/webhook', [PaystackController::class, 'webhook'])->name('paystack.webhook');

// Executive Partner Portal & Magic Link Access
Route::get('/executive/login', MagicLogin::class)->name('executive.login');
Route::get('/executive/dashboard', ExecutivePortal::class)->name('executive.dashboard');
Route::get('/executive/portal', ExecutivePortal::class);
Route::get('/executive', fn () => session('executive_id') ? redirect()->route('executive.dashboard') : redirect()->route('executive.login'));
Route::get('/executive/portal/{token}', ExecutivePortal::class)->name('executive.portal');

Route::middleware('auth')->group(function () {
    Route::get('/profile', Profile::class)->name('profile');
});

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('social.callback');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', Login::class)->middleware('guest')->name('login');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/products', Products::class)->name('products');
        Route::get('/products/new', ProductForm::class)->name('products.create');
        Route::get('/products/{product}/edit', ProductForm::class)->name('products.edit');
        Route::get('/orders', Orders::class)->name('orders');
        Route::get('/orders/{order}', OrderShow::class)->name('orders.show');
        Route::get('/executives', Executives::class)->name('executives');
        Route::get('/messages', Messages::class)->name('messages');
        Route::get('/messages/{message}', MessageShow::class)->name('messages.show');
        Route::get('/clients', Clients::class)->name('clients');
    });
});

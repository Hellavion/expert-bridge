<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CuratorController;
use App\Http\Controllers\Admin\ExpertController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\ClientRegistrationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Публичная страница регистрации клиентов
Route::get('/', [ClientRegistrationController::class, 'index'])->name('client.registration');
Route::post('/register-client', [ClientRegistrationController::class, 'store'])->name('client.register');

// Админка - защищена авторизацией
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Редирект с /admin на /admin/dashboard
    Route::redirect('/', '/admin/dashboard');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // CRUD для экспертов
    Route::resource('experts', ExpertController::class);

    // CRUD для кураторов
    Route::resource('curators', CuratorController::class);

    // CRUD для продуктов
    Route::resource('products', ProductController::class);

    // CRUD для промокодов
    Route::resource('promocodes', PromocodeController::class);

    // Список клиентов
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('clients/{client}/toggle-payment', [ClientController::class, 'togglePayment'])->name('clients.toggle-payment');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
});

require __DIR__.'/settings.php';

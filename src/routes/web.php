<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('appointments');
    }
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

    Route::inertia('/appointments', 'Appointments')->name('appointments');

    Route::get('/pets', function () {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($user && $user->isUser(), 403);

        return Inertia::render('Pets');
    })->name('pets');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';


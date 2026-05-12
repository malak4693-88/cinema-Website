<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Models\Movie;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (session()->has('username') && session('server_pid') !== getmypid()) {
        session()->forget(['username', 'dashboard_access_key', 'server_pid']);
    }

    $latestMovies = Movie::latest()->take(6)->get();

    return view('index', [
        'latestMovies' => $latestMovies,
        'username' => session('username'),
        'dashboardAccess' => session('dashboard_access_key'),
    ]);
})->name('home');

Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard-login-required', [AuthController::class, 'dashboardLoginRequired'])->name('dashboard.login.required');

Route::get('/dashboard', [MovieController::class, 'index'])->name('dashboard');
Route::get('/movies/create', [MovieController::class, 'create'])->name('movies.create');
Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');
Route::get('/movies/{movie}/edit', [MovieController::class, 'edit'])->name('movies.edit');
Route::put('/movies/{movie}', [MovieController::class, 'update'])->name('movies.update');
Route::delete('/movies/{movie}', [MovieController::class, 'destroy'])->name('movies.destroy');

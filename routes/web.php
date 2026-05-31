<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\Auth\GoogleController;
use App\Models\Movie;
use Illuminate\Support\Facades\Route;


// This route shows the public home page of the cinema website.
Route::get('/', function () {
    // Get the newest movies so they can appear in the home page slider.
    $latestMovies = Movie::latest()->take(6)->get();

    return view('index', [
        'latestMovies' => $latestMovies,
        // Send the username to the home page if the user is logged in.
        'username' => session('username'),
    ]);
})->name('home');

// This route shows the login form.
Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
// This route receives the login form data and checks username/password.
Route::post('/login', [AuthController::class, 'login'])->name('login');
// This route logs the user out and clears the session username.
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Google login routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('login.google.callback');

// These routes are protected, so only a logged-in user with a username session can access them.
Route::middleware('username.session')->group(function () {
    // This route shows the dashboard with movie list, search, and statistics.
    Route::get('/dashboard', [MovieController::class, 'index'])->name('dashboard');
    // These routes use TMDb API to search movies and fill the movie form.
    Route::get('/movies/tmdb/search', [MovieController::class, 'tmdbSearch'])->name('movies.tmdb.search');
    Route::get('/movies/tmdb/{tmdbId}', [MovieController::class, 'tmdbDetails'])->name('movies.tmdb.details');
    // This route opens the add movie form.
    Route::get('/movies/create', [MovieController::class, 'create'])->name('movies.create');
    // This route saves a new movie in the database.
    Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');
    // This route opens the edit form for one selected movie.
    Route::get('/movies/{movie}/edit', [MovieController::class, 'edit'])->name('movies.edit');
    // This route updates one selected movie.
    Route::put('/movies/{movie}', [MovieController::class, 'update'])->name('movies.update');
    // This route deletes one selected movie.
    Route::delete('/movies/{movie}', [MovieController::class, 'destroy'])->name('movies.destroy');
});

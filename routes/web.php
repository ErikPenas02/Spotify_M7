<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListsController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('login');
})->name('login');

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Ruta para búsqueda global (ahora renderiza la vista con resultados)
Route::get('/search', [HomeController::class, 'showSearchResults'])->name('search')->middleware('auth');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/validate-login', [AuthController::class, 'validateLogin'])->name('validate-login');
Route::post('/validate-register', [AuthController::class, 'validateRegister'])->name('validate-register');

Route::post('/playlists/search', [ListsController::class, 'searchPlaylists'])->name('playlists.search');

// Rutas para la creación de playlists
Route::post('/api/songs/search', [PlaylistController::class, 'searchSongs'])->name('songs.search');
Route::get('/api/genres', [PlaylistController::class, 'getGenres'])->name('genres.list');
Route::post('/api/playlists', [PlaylistController::class, 'store'])->name('playlists.store');

// Nueva ruta API para la búsqueda global
Route::post('/api/search', [HomeController::class, 'search'])->name('api.search');

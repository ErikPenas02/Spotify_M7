<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListsController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;

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


Route::middleware(['auth', 'isAdmin'])->prefix('dashboard')->name('dashboard.')->group(function () {

    /** ---------- VISTAS ---------- **/

    // Álbumes
    Route::get('/albumes', [AdminController::class, 'indexAlbumes'])->name('albumes.index');
    Route::get('/albumes/crear', [AdminController::class, 'crearAlbum'])->name('albumes.create');
    Route::get('/albumes/{id}/editar', [AdminController::class, 'editarAlbum'])->name('albumes.edit');

    // Canciones
    Route::get('/canciones', [AdminController::class, 'indexCanciones'])->name('canciones.index');
    Route::get('/canciones/crear', [AdminController::class, 'crearCancion'])->name('canciones.create');
    Route::get('/canciones/{id}/editar', [AdminController::class, 'editarCancion'])->name('canciones.edit');

    /** ---------- API (AJAX con fetch) ---------- **/

    // Álbumes
    Route::get('/api/albumes', [AdminController::class, 'fetchAlbumes'])->name('albumes.fetch');
    Route::post('/api/albumes', [AdminController::class, 'guardarAlbum'])->name('albumes.store');
    Route::put('/api/albumes/{id}', [AdminController::class, 'actualizarAlbum'])->name('albumes.update');
    Route::delete('/api/albumes/{id}', [AdminController::class, 'eliminarAlbum'])->name('albumes.destroy');

    // Canciones
    Route::get('/api/canciones', [AdminController::class, 'fetchCanciones'])->name('canciones.fetch');
    Route::post('/api/canciones', [AdminController::class, 'guardarCancion'])->name('canciones.store');
    Route::put('/api/canciones/{id}', [AdminController::class, 'actualizarCancion'])->name('canciones.update');
    Route::delete('/api/canciones/{id}', [AdminController::class, 'eliminarCancion'])->name('canciones.destroy');
});

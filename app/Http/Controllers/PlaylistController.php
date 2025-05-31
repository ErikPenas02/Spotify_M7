<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Playlist;
use App\Models\Cancion;
use App\Models\Genero;

class PlaylistController extends Controller
{
    public function searchSongs(Request $request)
    {
        try {
            Log::info('searchSongs request received', ['request_data' => $request->all()]);
            $query = Cancion::with(['album.artista', 'generos']);

            // Búsqueda por término
            if ($request->has('search_term') && !empty($request->search_term)) {
                $searchTerm = $request->search_term;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('titulo_cancion', 'like', "%{$searchTerm}%")
                      ->orWhereHas('album', function($q) use ($searchTerm) {
                          $q->where('titulo_album', 'like', "%{$searchTerm}%")
                            ->orWhereHas('artista', function($q) use ($searchTerm) {
                                $q->where('n_artista', 'like', "%{$searchTerm}%");
                            });
                      });
                });
            }

            // Filtro por géneros
            if ($request->has('genres') && !empty($request->genres)) {
                $genres = json_decode($request->genres);
                if (!empty($genres)) {
                    $query->whereHas('generos', function($q) use ($genres) {
                        $q->whereIn('id_gen', $genres);
                    });
                }
            }

            $songs = $query->get();
            // dd($query->toSql(), $query->getBindings()); // Descomentar para depurar
            return response()->json($songs->toArray());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al buscar canciones: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getGenres()
    {
        $genres = Genero::all();
        return response()->json($genres);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'n_playlist' => 'required|string|max:255',
                'img_playlist' => 'nullable|image|max:2048',
                'is_public' => 'boolean',
                'is_collab' => 'boolean',
                'songs' => 'required|json'
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            // Crear la playlist
            $playlist = new Playlist();
            $playlist->n_playlist = $request->n_playlist;
            $playlist->is_public = $request->boolean('is_public', false);
            $playlist->is_collab = $request->boolean('is_collab', false);
            $playlist->creator = $user->id_user;

            // Guardar la imagen si se proporcionó una
            if ($request->hasFile('img_playlist')) {
                $path = $request->file('img_playlist')->store('playlists', 'public');
                $playlist->img_playlist = $path;
            }

            $playlist->save();

            // Añadir las canciones seleccionadas
            $songs = json_decode($request->songs);
            $playlist->canciones()->attach($songs);

            return response()->json([
                'success' => true,
                'message' => 'Playlist creada correctamente',
                'playlist' => $playlist
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear la playlist: ' . $e->getMessage()
            ], 500);
        }
    }
} 
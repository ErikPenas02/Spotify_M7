<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genero;
use App\Models\Artista;
use App\Models\Cancion;
use App\Models\Album;
use App\Models\Playlist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener datos para las secciones

        // 1. Canciones de un Género Aleatorio
        $randomGenero = Genero::inRandomOrder()->first();
        $cancionesByGenero = $randomGenero ? $randomGenero->canciones()->with('album.artista')->get() : collect();

        // 2. Artistas Destacados (ej. los primeros 6)
        $artistasDestacados = Artista::take(6)->get();

        // 3. Canciones de un Artista Aleatorio
        $randomArtista = Artista::inRandomOrder()->first();
        $cancionesByArtista = $randomArtista ? Cancion::whereHas('album', function($query) use ($randomArtista) {
            $query->where('id_artista', $randomArtista->id_artista);
        })->with('album.artista')->get() : collect(); // Incluir canciones de sus álbumes

        // 4. Canciones de un Álbum Aleatorio
        $randomAlbum = Album::inRandomOrder()->first();
        $cancionesByAlbum = $randomAlbum ? $randomAlbum->canciones()->with('album.artista')->get() : collect();

        // 5. Canciones Recién Añadidas (ej. las últimas 10)
        $cancionesRecientes = Cancion::latest()->with('album.artista')->take(10)->get();

        // 6. Álbumes Recientes (ej. los últimos 6)
        $albumsRecientes = Album::latest()->with('artista')->take(6)->get();

        // Pasar los datos a la vista
        return view('client.home', compact(
            'randomGenero', 'cancionesByGenero',
            'artistasDestacados',
            'randomArtista', 'cancionesByArtista',
            'randomAlbum', 'cancionesByAlbum',
            'cancionesRecientes',
            'albumsRecientes'
        ));
    }

    public function search(Request $request)
    {
        // Log::info('API Search Request', ['request' => $request->all()]); // Opcional para depurar

        $searchTerm = $request->input('search_term');
        // Asegurarse de que genre sea un array, incluso si viene vacío o como string simple
        $genreIds = $request->input('genre', []);
        if (!is_array($genreIds)) {
            $genreIds = [$genreIds];
        }

        // Si no hay término de búsqueda ni géneros, devolver un resultado vacío
        if (empty($searchTerm) && empty($genreIds)) {
            return response()->json(['songs' => [], 'albums' => [], 'artists' => [], 'playlists' => []]);
        }

        // Buscar canciones
        $songsQuery = Cancion::with(['album.artista']);

        if (!empty($searchTerm)) {
            $songsQuery->where(function ($query) use ($searchTerm) {
                $query->where('titulo_cancion', 'like', "%" . $searchTerm . "%")
                    ->orWhereHas('album', function ($query) use ($searchTerm) {
                        $query->where('titulo_album', 'like', "%" . $searchTerm . "%")
                            ->orWhereHas('artista', function ($query) use ($searchTerm) {
                                $query->where('n_artista', 'like', "%" . $searchTerm . "%");
                            });
                    });
            });
        }

        // Filtrar canciones por género si se selecciona uno o varios
        if (!empty($genreIds)) {
            $songsQuery->whereHas('generos', function ($query) use ($genreIds) {
                $query->whereIn('id_genero', $genreIds);
            });
        }

        $songs = $songsQuery->get();

        // Buscar álbumes
        $albumsQuery = Album::with('artista');
        if (!empty($searchTerm)) {
             $albumsQuery->where(function ($query) use ($searchTerm) {
                    $query->where('titulo_album', 'like', "%" . $searchTerm . "%")
                        ->orWhereHas('artista', function ($query) use ($searchTerm) {
                            $query->where('n_artista', 'like', "%" . $searchTerm . "%");
                        });
             });
        }
        // Filtrar álbumes que contienen canciones de los géneros seleccionados
        if (!empty($genreIds)) {
             $albumsQuery->whereHas('canciones.generos', function ($query) use ($genreIds) {
                 $query->whereIn('id_genero', $genreIds);
             });
        }
        $albums = $albumsQuery->get();

        // Buscar artistas
         $artistsQuery = Artista::query();
         if (!empty($searchTerm)) {
             $artistsQuery->where('n_artista', 'like', "%" . $searchTerm . "%");
         }
         // Filtrar artistas que tienen canciones de los géneros seleccionados
         if (!empty($genreIds)) {
              $artistsQuery->whereHas('albums.canciones.generos', function ($query) use ($genreIds) {
                  $query->whereIn('id_genero', $genreIds);
              });
         }
        $artists = $artistsQuery->get();

        // Buscar playlists públicas
        $playlistsQuery = Playlist::where('is_public', true);

        if (!empty($searchTerm)) {
            $playlistsQuery->where('n_playlist', 'like', "%" . $searchTerm . "%");
        } else { // Si no hay término de búsqueda, filtramos solo por género (si hay) en playlists
             if (empty($genreIds)) {
                 // Si no hay término ni géneros, no deberíamos llegar aquí por la comprobación inicial
                 // pero por seguridad, devolvemos vacío si no hay filtros aplicados a playlists
                 $playlistsQuery->whereRaw('1 = 0'); // No devolver resultados
             }
        }

        // Filtrar playlists que contienen canciones de los géneros seleccionados
        if (!empty($genreIds)) {
             // Si ya filtramos por searchTerm, añadimos el filtro de género con AND
             if (!empty($searchTerm)) {
                  $playlistsQuery->whereHas('canciones.generos', function ($query) use ($genreIds) {
                      $query->whereIn('id_genero', $genreIds);
                  });
             } else { // Si no hay searchTerm, filtramos solo por género en playlists
                 $playlistsQuery->whereHas('canciones.generos', function ($query) use ($genreIds) {
                     $query->whereIn('id_genero', $genreIds);
                 });
             }
        }

        $playlists = $playlistsQuery->get();


        // Determinar el resultado principal (podría ser el primer álbum, canción, artista o playlist)
        $mainResult = null;
        if ($albums->count() > 0) {
            $mainResult = ['type' => 'album', 'data' => $albums->first()];
        } elseif ($songs->count() > 0) {
             $mainResult = ['type' => 'song', 'data' => $songs->first()];
        } elseif ($artists->count() > 0) {
             $mainResult = ['type' => 'artist', 'data' => $artists->first()];
        } elseif ($playlists->count() > 0) {
             $mainResult = ['type' => 'playlist', 'data' => $playlists->first()];
        }

        // Obtener los nombres de los géneros seleccionados para mostrarlos en la vista (opcional, se puede hacer en frontend)
        // $selectedGenres = !empty($genreIds) ? Genero::whereIn('id_gen', $genreIds)->get() : collect();

        // Devolver los resultados como JSON
        return response()->json([
            'searchTerm' => $searchTerm,
            'genreIds' => $genreIds,
            // 'selectedGenres' => $selectedGenres, // Opcional si se obtiene en frontend
            'songs' => $songs,
            'albums' => $albums,
            'artists' => $artists,
            'playlists' => $playlists,
            'mainResult' => $mainResult
        ]);
    }

    // Método para mostrar la página de resultados de búsqueda
    public function showSearchResults(Request $request)
    {
        $searchTerm = $request->input('search_term');
        $genreIds = $request->input('genre', []); // Obtener IDs de género (puede ser array)
        if (!is_array($genreIds)) {
            $genreIds = [$genreIds];
        }

        // Si no hay término de búsqueda ni géneros, redirigir a la página principal de inicio
        if (empty($searchTerm) && empty($genreIds)) {
            return redirect()->route('home');
        }

        // Obtener los datos de búsqueda reutilizando la lógica del método search (pero sin devolver JSON)
        // Tendremos que duplicar parte de la lógica o refactorizar el método search para ser reutilizable.
        // Por ahora, duplicaremos la lógica para simplificar y luego refactorizar si es necesario.

        // === Duplicado de lógica de búsqueda del método search ===

        // Buscar canciones
        $songsQuery = Cancion::with(['album.artista']);
        if (!empty($searchTerm)) {
            $songsQuery->where(function ($query) use ($searchTerm) {
                $query->where('titulo_cancion', 'like', "%" . $searchTerm . "%")
                    ->orWhereHas('album', function ($query) use ($searchTerm) {
                        $query->where('titulo_album', 'like', "%" . $searchTerm . "%")
                            ->orWhereHas('artista', function ($query) use ($searchTerm) {
                                $query->where('n_artista', 'like', "%" . $searchTerm . "%");
                            });
                    });
            });
        }
        if (!empty($genreIds)) {
            $songsQuery->whereHas('generos', function ($query) use ($genreIds) {
                $query->whereIn('id_genero', $genreIds);
            });
        }
        $songs = $songsQuery->get();

        // Buscar álbumes
        $albumsQuery = Album::with('artista');
        if (!empty($searchTerm)) {
            $albumsQuery->where(function ($query) use ($searchTerm) {
                $query->where('titulo_album', 'like', "%" . $searchTerm . "%")
                    ->orWhereHas('artista', function ($query) use ($searchTerm) {
                        $query->where('n_artista', 'like', "%" . $searchTerm . "%");
                    });
            });
        }
        if (!empty($genreIds)) {
            $albumsQuery->whereHas('canciones.generos', function ($query) use ($genreIds) {
                $query->whereIn('id_genero', $genreIds);
            });
        }
        $albums = $albumsQuery->get();

        // Buscar artistas
        $artistsQuery = Artista::query();
        if (!empty($searchTerm)) {
            $artistsQuery->where('n_artista', 'like', "%" . $searchTerm . "%");
        }
        if (!empty($genreIds)) {
            $artistsQuery->whereHas('albums.canciones.generos', function ($query) use ($genreIds) {
                $query->whereIn('id_genero', $genreIds);
            });
        }
        $artists = $artistsQuery->get();

        // Buscar playlists públicas
        $playlistsQuery = Playlist::where('is_public', true);
        if (!empty($searchTerm)) {
            $playlistsQuery->where('n_playlist', 'like', "%" . $searchTerm . "%");
        }
        if (!empty($genreIds)) {
            if (!empty($searchTerm) || empty($playlistsQuery->get()->toArray())) { // Añadir condición para no filtrar por género si no hay search term y no hay playlists iniciales
                $playlistsQuery->whereHas('canciones.generos', function ($query) use ($genreIds) {
                    $query->whereIn('id_genero', $genreIds);
                });
            } else {
                 $playlistsQuery->whereHas('canciones.generos', function ($query) use ($genreIds) {
                     $query->whereIn('id_genero', $genreIds);
                 });
            }
        } else if (empty($searchTerm)) { // Si no hay search term ni géneros, no mostrar playlists
             $playlistsQuery->whereRaw('1 = 0');
        }

        $playlists = $playlistsQuery->get();


        // Determinar el resultado principal
        $mainResult = null;
        if ($albums->count() > 0) {
            $mainResult = ['type' => 'album', 'data' => $albums->first()];
        } elseif ($songs->count() > 0) {
            $mainResult = ['type' => 'song', 'data' => $songs->first()];
        } elseif ($artists->count() > 0) {
            $mainResult = ['type' => 'artist', 'data' => $artists->first()];
        } elseif ($playlists->count() > 0) {
            $mainResult = ['type' => 'playlist', 'data' => $playlists->first()];
        }

        // Obtener los nombres de los géneros seleccionados para mostrarlos en la vista
        $selectedGenres = !empty($genreIds) ? Genero::whereIn('id_gen', $genreIds)->get() : collect();

        // Pasar los datos a la vista client.home
        return view('client.home', compact('searchTerm', 'genreIds', 'selectedGenres', 'songs', 'albums', 'artists', 'playlists', 'mainResult'));
    }
}

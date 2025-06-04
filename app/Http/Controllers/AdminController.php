<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cancion;
use App\Models\Album;
use App\Models\Artista;
use App\Models\Genero;

class AdminController extends Controller
{
    /** ---------------- VISTAS ---------------- **/

    public function indexCanciones()
    {
        return view('admin.canciones.index');
    }

    public function crearCancion()
    {
        $albumes = Album::with('artista')->get();
        $artistas = Artista::all();
        $generos = Genero::all();

        return view('admin.canciones.create', compact('albumes', 'artistas', 'generos'));
    }

    public function editarCancion($id)
    {
        $cancion = Cancion::with(['artistasColaboradores', 'generos'])->findOrFail($id);
        $albumes = Album::with('artista')->get();
        $artistas = Artista::all();
        $generos = Genero::all();

        return view('admin.canciones.edit', compact('cancion', 'albumes', 'artistas', 'generos'));
    }

    public function indexAlbumes()
    {
        return view('admin.albumes.index');
    }

    public function crearAlbum()
    {
        $artistas = Artista::all();
        return view('admin.albumes.create', compact('artistas'));
    }

    public function editarAlbum($id)
    {
        $album = Album::findOrFail($id);
        $artistas = Artista::all();
        return view('admin.albumes.edit', compact('album', 'artistas'));
    }

    /** ---------------- API: CANCIONES ---------------- **/

    public function fetchCanciones(Request $request)
{
    $query = Cancion::with(['album.artista', 'artistasColaboradores', 'generos']);

    // Búsqueda genérica en varios campos
    if ($search = $request->input('search')) {
        $query->where(function($q) use ($search) {
            $q->where('titulo_cancion', 'like', "%$search%")
              ->orWhere('duracion', 'like', "%$search%")
              ->orWhereHas('album', function($q2) use ($search) {
                  $q2->where('titulo_album', 'like', "%$search%");
              })
              ->orWhereHas('album.artista', function($q3) use ($search) {
                  $q3->where('n_artista', 'like', "%$search%");
              })
              ->orWhereHas('artistasColaboradores', function($q4) use ($search) {
                  $q4->where('n_artista', 'like', "%$search%");
              })
              ->orWhereHas('generos', function($q5) use ($search) {
                  $q5->where('n_genero', 'like', "%$search%");
              });
        });
    }

    // Filtro por id_album (álbum)
    if ($albumId = $request->input('album_id')) {
        $query->where('id_album', $albumId);
    }

    // Filtro por id_artista (artista principal del álbum)
    if ($artistaId = $request->input('artista_id')) {
        $query->whereHas('album.artista', function($q) use ($artistaId) {
            $q->where('id_artista', $artistaId);
        });
    }

    // Filtro por género (puede ser varios, ejemplo: géneros[] = [1,2])
    if ($generos = $request->input('generos')) {
        $query->whereHas('generos', function($q) use ($generos) {
            $q->whereIn('id_gen', (array) $generos);
        });
    }

    // Puedes agregar más filtros si quieres, solo agrégalos aquí...

    $canciones = $query->get();

    return response()->json($canciones);
}


    public function guardarCancion(Request $request)
{
    $validated = $request->validate([
        'titulo_cancion' => 'required|string|max:255',
        'duracion' => 'required',
        'id_album' => 'required|exists:albums,id_album',
        // demás validaciones...
    ]);

    // Crear canción...
    $cancion = Cancion::create($validated);

    // Puedes devolver:
    return response()->json([
        'success' => true,
        'cancion' => $cancion
    ]);
}


    public function actualizarCancion(Request $request, $id)
    {
        $request->validate([
            'titulo_cancion' => 'required|string|max:255',
            'duracion' => 'required|string|max:10',
            'id_album' => 'required|exists:albums,id_album',
            'artistas_colaboradores' => 'nullable|array',
            'artistas_colaboradores.*' => 'exists:artistas,id_artista',
            'generos' => 'nullable|array',
            'generos.*' => 'exists:generos,id_gen',
        ]);

        $cancion = Cancion::findOrFail($id);
        $cancion->update($request->only('titulo_cancion', 'duracion', 'id_album'));

        $cancion->artistasColaboradores()->sync($request->input('artistas_colaboradores', []));
        $cancion->generos()->sync($request->input('generos', []));

        return response()->json(['success' => true, 'message' => 'Canción actualizada correctamente.']);
    }

    public function eliminarCancion($id)
    {
        $cancion = Cancion::findOrFail($id);
        $cancion->artistasColaboradores()->detach();
        $cancion->generos()->detach();
        $cancion->delete();

        return response()->json(['success' => true, 'message' => 'Canción eliminada correctamente.']);
    }

    /** ---------------- API: ALBUMES ---------------- **/

    public function fetchAlbumes(Request $request)
    {
        $query = Album::with('artista');

        if ($search = $request->input('search')) {
            $query->where('titulo_album', 'like', "%$search%");
        }

        $albumes = $query->get();

        return response()->json($albumes);
    }

    public function guardarAlbum(Request $request)
    {
        $request->validate([
            'titulo_album' => 'required|string|max:255',
            'anio' => 'required|integer',
            'id_artista' => 'required|exists:artistas,id_artista',
        ]);

        $album = Album::create($request->only('titulo_album', 'anio', 'id_artista'));

        return response()->json(['success' => true, 'message' => 'Álbum creado correctamente.']);
    }

    public function actualizarAlbum(Request $request, $id)
    {
        $request->validate([
            'titulo_album' => 'required|string|max:255',
            'anio' => 'required|integer',
            'id_artista' => 'required|exists:artistas,id_artista',
        ]);

        $album = Album::findOrFail($id);
        $album->update($request->only('titulo_album', 'anio', 'id_artista'));

        return response()->json(['success' => true, 'message' => 'Álbum actualizado correctamente.']);
    }

    public function eliminarAlbum($id)
    {
        $album = Album::findOrFail($id);
        $album->delete();

        return response()->json(['success' => true, 'message' => 'Álbum eliminado correctamente.']);
    }
}

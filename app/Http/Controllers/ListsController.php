<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Playlist;

class ListsController extends Controller
{
    public function searchPlaylists(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            Log::info('Usuario autenticado:', ['id' => $user->id_user, 'username' => $user->username]);

            // Obtener las playlists usando la relación
            $query = $user->playlists();

            // Obtener parámetros de búsqueda y filtro
            $searchTerm = $request->input('search_term');
            $isPublic = $request->input('is_public');
            $isCollab = $request->boolean('is_collab', null);

            Log::info('Parámetros de búsqueda:', [
                'search_term' => $searchTerm,
                'is_public' => $isPublic,
                'is_collab' => $isCollab
            ]);

            // Aplicar filtro por nombre si hay término de búsqueda
            if ($searchTerm) {
                $query->where('n_playlist', 'like', '%' . $searchTerm . '%');
            }

            // Aplicar filtro por pública/privada
            if ($isPublic !== null) {
                if ($isPublic === 'public') {
                    $query->where('is_public', true);
                } elseif ($isPublic === 'private') {
                    $query->where('is_public', false);
                }
            }

            // Aplicar filtro por colaborativa
            if ($isCollab !== null) {
                $query->where('is_collab', $isCollab);
            }

            // Si no hay término de búsqueda ni filtros aplicados, mostrar las últimas 10
            if (empty($searchTerm) && $isPublic === null && $isCollab === null) {
                $playlists = $query->latest()->take(10)->get();
            } else {
                $playlists = $query->orderBy('n_playlist')->get();
            }

            Log::info('Playlists encontradas: ' . $playlists->count());

            return response()->json($playlists);

        } catch (\Exception $e) {
            Log::error('Error en searchPlaylists: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Error al obtener las playlists',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Si necesitas otros métodos para crear, actualizar, eliminar, etc., irían aquí
}

@extends('layouts.adminMain')

@section('title', 'Editar Canción')

@section('content')
<div class="container mt-4">
    <h1>Editar Canción</h1>

    <form id="formEditarCancion" 
          action="{{ route('dashboard.canciones.update', $cancion->id_cancion) }}" 
          method="POST" 
          data-redirect="{{ route('dashboard.canciones.index') }}">
        @csrf
        @method('PUT')

        <div id="errores" class="mb-3 text-danger"></div> <!-- Aquí mostramos errores -->

        <div class="mb-3">
            <label for="titulo_cancion" class="form-label">Título</label>
            <input type="text" name="titulo_cancion" id="titulo_cancion" class="form-control" value="{{ old('titulo_cancion', $cancion->titulo_cancion) }}" required>
        </div>

        <div class="mb-3">
            <label for="duracion" class="form-label">Duración</label>
            <input type="text" name="duracion" id="duracion" class="form-control" value="{{ old('duracion', $cancion->duracion) }}" required placeholder="Ej: 03:30">
        </div>

        <div class="mb-3">
            <label for="id_album" class="form-label">Álbum</label>
            <select name="id_album" id="id_album" class="form-select" required>
                <option value="">-- Selecciona un álbum --</option>
                @foreach($albumes as $album)
                    <option value="{{ $album->id_album }}" 
                        {{ (old('id_album', $cancion->id_album) == $album->id_album) ? 'selected' : '' }}>
                        {{ $album->titulo_album }} ({{ $album->artista->n_artista ?? 'Sin artista' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="artistas_colaboradores" class="form-label">Artistas Colaboradores</label>
            <select name="artistas_colaboradores[]" id="artistas_colaboradores" class="form-select" multiple>
                @foreach($artistas as $artista)
                    <option value="{{ $artista->id_artista }}"
                        {{ (collect(old('artistas_colaboradores', $cancion->artistas_colaboradores_ids))->contains($artista->id_artista)) ? 'selected' : '' }}>
                        {{ $artista->n_artista }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Mantén Ctrl/Cmd para seleccionar varios.</small>
        </div>

        <div class="mb-3">
            <label for="nuevo_artista" class="form-label">Nuevo Artista (si no está en la lista)</label>
            <input type="text" name="nuevo_artista" id="nuevo_artista" class="form-control" value="{{ old('nuevo_artista') }}" placeholder="Escribe el nombre del artista para crearlo">
            <small class="form-text text-muted">Si el artista no está en la lista, escribe su nombre aquí para añadirlo automáticamente.</small>
        </div>

        <div class="mb-3">
            <label for="generos" class="form-label">Géneros</label>
            <select name="generos[]" id="generos" class="form-select" multiple>
                @foreach($generos as $genero)
                    <option value="{{ $genero->id_gen }}"
                        {{ (collect(old('generos', $cancion->generos_ids))->contains($genero->id_gen)) ? 'selected' : '' }}>
                        {{ $genero->n_genero }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Mantén Ctrl/Cmd para seleccionar varios.</small>
        </div>

        <button type="submit" class="btn btn-success">Actualizar Canción</button>
        <a href="{{ route('dashboard.canciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/canciones/editar.js') }}"></script>
@endsection

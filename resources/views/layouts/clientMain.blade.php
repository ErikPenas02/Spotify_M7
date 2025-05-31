<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeatHive</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layouts/clientMain.css') }}"> {{-- Enlazaremos un CSS para el cliente --}}
    <link rel="stylesheet" href="{{ asset('css/layouts/createPlaylist.css') }}">
</head>
<body>
    <div class="top-bar">
        <div class="nav-buttons">
             {{-- Botones de navegación --}}
             <a href="#">&#x2039;</a> {{-- Icono de flecha izquierda --}}
             <a href="#">&#x203A;</a> {{-- Icono de flecha derecha --}}
             {{-- Botón de inicio (icono de casa) --}}
             <a href="{{ route('home') }}">
                 <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#ffbf00"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.69l5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/></svg>
             </a>
        </div>
        <div class="search-bar">
            <input id="search-bar" type="text" placeholder="¿Qué quieres reproducir?">
        </div>
        <div class="user-profile">
            <img src="{{ asset('img/default.jpg') }}" alt="Foto de perfil">
            <span>{{ Auth::user()->username ?? 'Invitado' }}</span>
        </div>
    </div>

    <div class="content">
        @yield('content') {{-- Aquí irá el contenido específico de cada página --}}
    </div>

    {{-- Modal de creación de playlist --}}
    <div id="createPlaylistModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Crear nueva playlist</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="createPlaylistForm">
                    <div class="form-group">
                        <label for="playlistName">Nombre de la playlist</label>
                        <input type="text" id="playlistName" name="n_playlist" required>
                    </div>

                    <div class="form-group">
                        <label for="playlistCover">Portada</label>
                        <input type="file" id="playlistCover" name="img_playlist" accept="image/*">
                        <div class="cover-preview"></div>
                    </div>

                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="is_public" id="isPublic">
                            Playlist pública
                        </label>
                        <label>
                            <input type="checkbox" name="is_collab" id="isCollab">
                            Playlist colaborativa
                        </label>
                    </div>

                    <div class="form-group">
                        <h3>Añadir canciones</h3>
                        <div class="search-filters">
                            <div class="search-input">
                                <input type="text" id="songSearch" placeholder="Buscar canciones...">
                            </div>
                            <div class="filter-group">
                                <select id="genreFilter" multiple>
                                    <option value="">Todos los géneros</option>
                                    <!-- Se llenará dinámicamente -->
                                </select>
                            </div>
                        </div>
                        <div class="songs-list">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-create">Crear playlist</button>
                        <button type="button" class="btn-cancel">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de filtro de género para búsqueda --}}
    <div id="genreFilterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Filtrar por Género</h2>
                <button id="close-genre-filter-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="genreFilterOptions" class="genre-options-grid">
                    {{-- Los botones de género se cargarán aquí dinámicamente con JS --}}
                </div>
            </div>
            <div class="modal-footer">
                 <button class="btn-cancel close-genre-filter-modal">Cancelar</button>
                 <button id="applyGenreFilterBtn" class="btn-create">Aplicar filtros</button>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/client/playlistList.js') }}"></script>
    <script src="{{ asset('js/client/createPlaylist.js') }}"></script>
    <script src="{{ asset('js/client/home.js') }}"></script> {{-- Script para la lógica del buscador y home --}}
</body>
</html>

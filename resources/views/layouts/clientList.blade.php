<link rel="stylesheet" href="{{ asset('css/layouts/clientList.css') }}"> {{-- Enlazaremos un CSS para el cliente --}}

<div class="playlist-sidebar">
    <div class="sidebar-header">
        <h3>Tus Playlists</h3>
        <div class="header-actions">
            <button class="create-playlist-btn" title="Crear Playlist">+</button>
        </div>
    </div>
    <div class="playlist-filters">
        <div class="playlist-search">
            <input type="text" id="playlistSearchInput" placeholder="Buscar en tus playlists">
        </div>
        <div class="filter-options">
            <label for="publicPrivateFilter">Estado:</label>
            <select id="publicPrivateFilter">
                <option value="">Todas</option>
                <option value="public">Públicas</option>
                <option value="private">Privadas</option>
            </select>
            <label for="collabFilter">Colaborativa:</label>
            <input type="checkbox" id="collabFilter">
        </div>
    </div>
    <div class="playlist-list" id="playlistListContainer">
        {{-- Las playlists se cargarán aquí dinámicamente con JS --}}
        {{-- Ejemplo de un elemento de playlist --}}
        {{-- <div class="playlist-item">
            <img src="{{ asset('img/playlist.png') }}" alt="Portada Playlist" class="playlist-cover">
            <span class="playlist-name">Mi primera playlist</span>
        </div> --}}
    </div>
</div>

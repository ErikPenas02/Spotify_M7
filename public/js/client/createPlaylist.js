document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('createPlaylistModal');
    const createBtn = document.querySelector('.create-playlist-btn');
    const closeBtn = document.querySelector('.close-modal');
    const cancelBtn = document.querySelector('.btn-cancel');
    const form = document.getElementById('createPlaylistForm');
    const songSearch = document.getElementById('songSearch');
    const genreFilter = document.getElementById('genreFilter');
    const songsList = document.querySelector('.songs-list');
    const coverInput = document.getElementById('playlistCover');
    const coverPreview = document.querySelector('.cover-preview');
    
    let selectedSongs = new Set();
    let genres = [];

    // Cargar géneros al inicio
    loadGenres();

    // Abrir modal
    createBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    // Cerrar modal
    function closeModal() {
        modal.style.display = 'none';
        form.reset();
        selectedSongs.clear();
        coverPreview.style.display = 'none';
        coverPreview.innerHTML = '';
        songsList.innerHTML = '';
    }

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Vista previa de la portada
    coverInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                coverPreview.style.display = 'block';
                coverPreview.innerHTML = `<img src="${e.target.result}" alt="Vista previa">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Cargar géneros
    function loadGenres() {
        fetch('/api/genres')
            .then(response => response.json())
            .then(data => {
                genres = data;
                genreFilter.innerHTML = '<option value="">Todos los géneros</option>';
                genres.forEach(genre => {
                    genreFilter.innerHTML += `<option value="${genre.id_gen}">${genre.n_genero}</option>`;
                });
            })
            .catch(error => console.error('Error al cargar géneros:', error));
    }

    // Buscar canciones
    function searchSongs() {
        const searchTerm = songSearch.value;
        const selectedGenres = Array.from(genreFilter.selectedOptions).map(option => option.value);

        const formData = new FormData();
        formData.append('search_term', searchTerm);
        formData.append('genres', JSON.stringify(selectedGenres));

        fetch('/api/songs/search', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la búsqueda de canciones');
            }
            return response.json();
        })
        .then(data => {
            songsList.innerHTML = '';
            if (data.error) {
                songsList.innerHTML = `<div class="error-message">${data.error}</div>`;
                return;
            }
            if (!Array.isArray(data)) {
                console.error('La respuesta no es un array:', data);
                songsList.innerHTML = '<div class="error-message">Error en el formato de la respuesta</div>';
                return;
            }
            if (data.length === 0) {
                songsList.innerHTML = '<div class="no-results">No se encontraron canciones</div>';
                return;
            }
            data.forEach(song => {
                const songElement = createSongElement(song);
                songsList.appendChild(songElement);
            });
        })
        .catch(error => {
            console.error('Error al buscar canciones:', error);
            songsList.innerHTML = `<div class="error-message">Error al buscar canciones: ${error.message}</div>`;
        });
    }

    // Crear elemento de canción
    function createSongElement(song) {
        const div = document.createElement('div');
        div.className = `song-item ${selectedSongs.has(song.id_cancion) ? 'selected' : ''}`;
        div.innerHTML = `
            <img src="${song.album.portada_album ? '/public/img/playlists' + song.album.portada_album : '/img/default-album.png'}" 
                 alt="Portada" 
                 onerror="this.src='/img/default-album.png'">
            <div class="song-info">
                <div class="song-title">${song.titulo_cancion}</div>
                <div class="song-artist">${song.album.artista.n_artista} - ${song.album.titulo_album}</div>
            </div>
        `;

        div.addEventListener('click', () => {
            if (selectedSongs.has(song.id_cancion)) {
                selectedSongs.delete(song.id_cancion);
                div.classList.remove('selected');
            } else {
                selectedSongs.add(song.id_cancion);
                div.classList.add('selected');
            }
        });

        return div;
    }

    // Eventos de búsqueda
    songSearch.addEventListener('input', searchSongs);
    genreFilter.addEventListener('change', searchSongs);

    // Enviar formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('songs', JSON.stringify(Array.from(selectedSongs)));

        fetch('/api/playlists', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                // Recargar la lista de playlists
                if (typeof loadPlaylists === 'function') {
                    loadPlaylists();
                }
            } else {
                alert(data.error || 'Error al crear la playlist');
            }
        })
        .catch(error => {
            console.error('Error al crear playlist:', error);
            alert('Error al crear la playlist');
        });
    });
}); 
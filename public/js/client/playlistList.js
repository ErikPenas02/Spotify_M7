// JavaScript para la barra lateral de playlists

document.addEventListener('DOMContentLoaded', function() {

    const playlistSearchInput = document.getElementById('playlistSearchInput');
    const publicPrivateFilter = document.getElementById('publicPrivateFilter');
    const collabFilter = document.getElementById('collabFilter');
    const playlistListContainer = document.getElementById('playlistListContainer');

    // Función para cargar y mostrar playlists
    function loadPlaylists(searchTerm = '', isPublic = null, isCollab = null) {
        // Limpiar lista actual
        playlistListContainer.innerHTML = '';

        // Construir los datos a enviar en la petición
        const formData = new FormData();
        formData.append('search_term', searchTerm);
        
        // Añadir is_public solo si se ha seleccionado un filtro específico
        if (isPublic === 'public' || isPublic === 'private') {
             formData.append('is_public', isPublic);
        }

        // Añadir is_collab solo si se ha marcado o desmarcado explícitamente
        if (isCollab !== null) {
             formData.append('is_collab', isCollab);
        }

        // Añadir CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/playlists/search', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(function(response) {
            if (!response.ok) {
                // Manejar errores HTTP
                console.error('Error en la petición:', response.statusText);
                return { error: 'Error al cargar playlists.' }; // Devuelve un objeto con error
            }
            return response.json();
        })
        .then(function(data) {
            if (data.error) {
                // Mostrar mensaje de error si viene del backend o del manejo de error HTTP
                playlistListContainer.innerHTML = '<div style="color: red;">' + data.error + '</div>';
            } else if (data.length === 0) {
                // Mostrar mensaje si no hay resultados
                playlistListContainer.innerHTML = '<div style="color: #b3b3b3;">No se encontraron playlists.</div>';
            } else {
                // Renderizar las playlists recibidas
                data.forEach(function(playlist) {
                    const playlistItem = `
                        <div class="playlist-item">
                            <img src="${playlist.img_playlist ? '/storage/' + playlist.img_playlist : '/img/playlist.png'}" 
                                 alt="Portada Playlist" 
                                 class="playlist-cover"
                                 onerror="this.src='/img/playlist.png'">
                            <span class="playlist-name">${playlist.n_playlist}</span>
                        </div>
                    `;
                    playlistListContainer.innerHTML += playlistItem;
                });
            }
        })
        .catch(function(error) {
            // Manejar errores de red o de procesamiento de Fetch
            console.error('Error en Fetch:', error);
            playlistListContainer.innerHTML = '<div style="color: red;">Ocurrió un error al cargar las playlists.</div>';
        });
    }

    // Cargar playlists al inicio
    loadPlaylists();

    // Añadir listeners a los elementos de filtro
    playlistSearchInput.addEventListener('input', function() {
        loadPlaylists(
            this.value,
            publicPrivateFilter.value,
            collabFilter.checked ? collabFilter.checked : null // Enviar true/false o null
        );
    });

    publicPrivateFilter.addEventListener('change', function() {
         loadPlaylists(
             playlistSearchInput.value,
             this.value,
             collabFilter.checked ? collabFilter.checked : null
         );
    });

    collabFilter.addEventListener('change', function() {
         loadPlaylists(
             playlistSearchInput.value,
             publicPrivateFilter.value,
             this.checked ? this.checked : null
         );
    });

}); 
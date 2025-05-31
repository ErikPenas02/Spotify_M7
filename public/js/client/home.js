document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-bar');
    const genreFilterModal = document.getElementById('genreFilterModal');
    const closeGenreFilterModalBtns = document.getElementById('close-genre-filter-modal');
    const genreFilterOptions = document.getElementById('genreFilterOptions');
    const applyGenreFilterBtn = document.getElementById('applyGenreFilterBtn');
    const openGenreFilterModalBtn = document.createElement('button');

    window.selectedSearchGenreIds = [];

    // Añadir botón de filtro de género a la barra de búsqueda
    openGenreFilterModalBtn.textContent = 'Géneros';
    openGenreFilterModalBtn.classList.add('btn', 'btn-secondary', 'genre-filter-btn');
    openGenreFilterModalBtn.style.marginLeft = '10px';
    searchInput.parentNode.insertBefore(openGenreFilterModalBtn, searchInput.nextSibling);

    // Referencia al contenedor de resultados en home.blade.php
    const searchResultsContainer = document.getElementById('mainContentArea'); // Necesitarás añadir este ID al div en home.blade.php

    // Cargar géneros en el modal
    function loadGenresIntoModal() {
        fetch('/api/genres')
            .then(response => response.json())
            .then(data => {
                genreFilterOptions.innerHTML = '';
                data.forEach(genre => {
                    const button = document.createElement('button');
                    button.textContent = genre.n_genero;
                    button.classList.add('genre-button');
                    button.setAttribute('data-genre-id', genre.id_gen);
                    if (window.selectedSearchGenreIds.includes(genre.id_gen)) {
                        button.classList.add('selected');
                    }
                    button.addEventListener('click', () => {
                        button.classList.toggle('selected');
                    });
                    genreFilterOptions.appendChild(button);
                });
            })
            .catch(error => console.error('Error al cargar géneros:', error));
    }

    // Abrir modal
    openGenreFilterModalBtn.addEventListener('click', () => {
        loadGenresIntoModal();
        genreFilterModal.style.display = 'block';
    });

    // Cerrar modal
    closeGenreFilterModalBtns.addEventListener('click', () => {
            genreFilterModal.style.display = 'none';
        });

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', (e) => {
        if (e.target === genreFilterModal) {
            genreFilterModal.style.display = 'none';
        }
    });

    // Aplicar filtros
    applyGenreFilterBtn.addEventListener('click', () => {
        window.selectedSearchGenreIds = [];
        genreFilterOptions.querySelectorAll('.genre-button.selected').forEach(button => {
            window.selectedSearchGenreIds.push(button.getAttribute('data-genre-id'));
        });
        genreFilterModal.style.display = 'none';

        // Disparar la búsqueda con los nuevos filtros aplicados
        triggerSearch(); // Llamar a la función de búsqueda
    });

    // Función para obtener los IDs de los géneros seleccionados
    function getSelectedGenreIds() {
            return window.selectedSearchGenreIds;
    }

    // Función para realizar la búsqueda con Fetch
    function triggerSearch() {
        const searchTerm = searchInput.value.trim();
        const selectedGenreIds = getSelectedGenreIds();

        // Si no hay término de búsqueda ni géneros y estamos en la página de búsqueda, redirigir a home.
        if (searchTerm === '' && selectedGenreIds.length === 0 && window.location.pathname === '/search') {
            window.location.href = '/home';
            return; // Salir de la función
        }

        // Solo buscar si hay término o géneros seleccionados, o si ya estamos en /home o /search
        // Esto evita peticiones innecesarias al cargar otras páginas.
        if (searchTerm.length > 0 || selectedGenreIds.length > 0 || ['/home', '/search'].includes(window.location.pathname)) {

            const queryParams = new URLSearchParams();
                if (searchTerm.length > 0) {
                queryParams.append('search_term', searchTerm);
                }
            selectedGenreIds.forEach(genreId => {
                queryParams.append('genre[]', genreId); // Usar 'genre[]' para array en PHP
            });

            // Actualizar la URL sin recargar la página (HTML5 History API)
            const newUrl = `/search?${queryParams.toString()}`;
            window.history.pushState({ path: newUrl }, '', newUrl);

            // Determinar la URL de la petición Fetch
            const fetchUrl = '/api/search'; // Usaremos una nueva ruta API

            fetch(fetchUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest' // Indicar que es una petición AJAX
                },
                body: JSON.stringify({ search_term: searchTerm, genre: selectedGenreIds })
            })
            .then(response => response.json())
            .then(data => {
                    // Aquí es donde manejaremos los resultados y actualizaremos la vista dinámicamente
                    console.log('Resultados de búsqueda:', data);
                    // Llama a una función para renderizar los resultados en home.blade.php
                    if (window.renderSearchResults) {
                        window.renderSearchResults(data, searchTerm, selectedGenreIds);
                    } else {
                    console.error('Función renderSearchResults no definida. No se actualizará la UI dinámicamente.');
                    }
            })
            .catch(error => {
                console.error('Error en la búsqueda Fetch:', error);
                // Opcional: Mostrar un mensaje de error en la UI en clientMain.blade.php
            });

        }
    }

    // Cambiamos el evento a 'keyup' (sin timeout)
    searchInput.addEventListener('keyup', function(event) {
        // No disparamos la búsqueda si son teclas como Shift, Ctrl, Alt, etc.
            if (['Shift', 'Control', 'Alt', 'Meta', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Enter'].includes(event.key)) { // Añadir 'Enter' si no quieres buscar al presionar Enter
                return;
            }
            triggerSearch(); // Disparar la búsqueda en cada keyup relevante
    });
    
        // Añadir evento 'input' también para capturar cambios por pegado, etc.
        searchInput.addEventListener('input', function() {
            triggerSearch();
        });

        // Si la página actual es /search, disparamos la búsqueda al cargar la página para mostrar los resultados iniciales
        if (window.location.pathname === '/search') {
            // Puedes obtener el término de búsqueda y géneros de la URL si es necesario
            const urlParams = new URLSearchParams(window.location.search);
            const initialSearchTerm = urlParams.get('search_term') || '';
            const initialGenreParams = urlParams.getAll('genre[]');
            const initialGenreIds = initialGenreParams.map(id => id.toString()); // Asegurarse de que son strings para la comparación

            // Establecer el valor del input de búsqueda y los géneros seleccionados iniciales
            searchInput.value = initialSearchTerm;
            window.selectedSearchGenreIds = initialGenreIds;

            // Disparar la búsqueda inicial solo si hay término o géneros en la URL
            if (initialSearchTerm.length > 0 || initialGenreIds.length > 0) {
                triggerSearch();
            } else {
                // Si estamos en /search pero sin parámetros, redirigir a home
                window.location.href = '/home';
            }
        }

    // Función para renderizar los resultados de búsqueda en el mainContentArea
    function renderSearchResults(data, searchTerm, genreIds) {
        const mainContentArea = document.getElementById('mainContentArea');
        if (!mainContentArea) {
            console.error('Elemento #mainContentArea no encontrado.');
            return; // Salir si no encontramos el contenedor
        }

        mainContentArea.innerHTML = ''; // Limpiar contenido actual

        let html = '';

        // Mostrar término de búsqueda
         if (searchTerm && searchTerm.length > 0) {
             html += `<h2>Resultados para "${searchTerm}"</h2>`;
         }

         // Mostrar géneros seleccionados
         if (genreIds && genreIds.length > 0) {
             html += '<div class="selected-genres-display"><span>Filtrando por géneros:</span>';
             genreIds.forEach(genreId => {
                 // Aquí podrías buscar el nombre del género si tienes los datos cargados en frontend
                 html += `<span class="selected-genre-tag">ID: ${genreId}</span>`;
             });
              html += '</div>';
         }

        // Mostrar Resultado principal
         if (data.mainResult) {
            html += '<section class="home-section"><div class="section-header"><h2>Resultado principal</h2></div><div class="section-content">';
            const item = data.mainResult.data;
            const type = data.mainResult.type;

            if (type === 'album') {
                html += `
                     <div class="album-item-home">
                        <img src="${item.portada_album ? '/storage/' + item.portada_album : '/img/playlist.png'}" alt="Portada" class="item-cover" onerror="this.src='/img/playlist.png'">
                         <div class="item-info">
                            <div class="item-title">${item.titulo_album}</div>
                            <div class="item-subtitle">${item.artista ? item.artista.n_artista : 'Artista Desconocido'}</div>
                        </div>
                     </div>
                 `;
            } else if (type === 'song') {
                html += `
                     <div class="song-item-home">
                        <img src="${item.album && item.album.portada_album ? '/storage/' + item.album.portada_album : '/img/playlist.png'}" alt="Portada" class="item-cover" onerror="this.src='/img/playlist.png'">
                         <div class="item-info">
                            <div class="item-title">${item.titulo_cancion}</div>
                            <div class="item-subtitle">${item.album && item.album.artista ? item.album.artista.n_artista : 'Artista Desconocido'} - ${item.album ? item.album.titulo_album : 'Álbum Desconocido'}</div>
                        </div>
                     </div>
                 `;
            } else if (type === 'artist') {
                html += `
                     <div class="artist-item-home">
                        <img src="${item.img_artista ? '/storage/' + item.img_artista : '/img/default.jpg'}" alt="Artista" class="item-cover rounded" onerror="this.src='/img/default.jpg'">
                         <div class="item-info text-center">
                            <div class="item-title">${item.n_artista}</div>
                        </div>
                     </div>
                 `;
            } else if (type === 'playlist') {
                html += `
                     <div class="playlist-item-home">
                        <img src="${item.img_playlist ? '/storage/' + item.img_playlist : '/img/playlist.png'}" alt="Portada" class="item-cover" onerror="this.src='/img/playlist.png'">
                         <div class="item-info">
                            <div class="item-title">${item.n_playlist}</div>
                            <div class="item-subtitle">Playlist</div>
                        </div>
                     </div>
                 `;
            }
            html += '</div></section>';
         }

         // Mostrar Canciones
         if (data.songs && data.songs.length > 0) {
             html += '<section class="home-section"><div class="section-header"><h2>Canciones</h2></div><div class="section-content vertical-list">';
             data.songs.forEach(song => {
                 html += `
                     <div class="search-result-item song-item">
                         <img src="${song.album && song.album.portada_album ? '/storage/' + song.album.portada_album : '/img/playlist.png'}" alt="Portada" onerror="this.src='/img/playlist.png'">
                         <div class="item-info">
                             <div class="item-title">${song.titulo_cancion}</div>
                             <div class="item-subtitle">${song.album && song.album.artista ? song.album.artista.n_artista : 'Artista Desconocido'} - ${song.album ? song.album.titulo_album : 'Álbum Desconocido'}</div>
                         </div>
                     </div>
                 `;
             });
             html += '</div></section>';
         }

         // Mostrar Artistas
         if (data.artists && data.artists.length > 0) {
             html += '<section class="home-section"><div class="section-header"><h2>Artistas</h2></div><div class="section-content horizontal-scroll">';
             data.artists.forEach(artist => {
                 html += `
                      <div class="artist-item-home">
                         <img src="${artist.img_artista ? '/storage/' + artist.img_artista : '/img/default.jpg'}" alt="Artista" class="item-cover rounded" onerror="this.src='/img/default.jpg'">
                          <div class="item-info text-center">
                             <div class="item-title">${artist.n_artista}</div>
                         </div>
                      </div>
                   `;
             });
             html += '</div></section>';
         }

         // Mostrar Álbumes
         if (data.albums && data.albums.length > 0) {
             html += '<section class="home-section"><div class="section-header"><h2>Álbumes</h2></div><div class="section-content horizontal-scroll">';
             data.albums.forEach(album => {
                 html += `
                      <div class="album-item-home">
                         <img src="${album.portada_album ? '/storage/' + album.portada_album : '/img/playlist.png'}" alt="Portada" class="item-cover" onerror="this.src='/img/playlist.png'">
                          <div class="item-info">
                             <div class="item-title">${album.titulo_album}</div>
                             <div class="item-subtitle">${album.artista ? album.artista.n_artista : 'Artista Desconocido'}</div>
                         </div>
                      </div>
                   `;
             });
             html += '</div></section>';
         }

         // Mostrar Playlists
         if (data.playlists && data.playlists.length > 0) {
              html += '<section class="home-section"><div class="section-header"><h2>Playlists</h2></div><div class="section-content horizontal-scroll">';
             data.playlists.forEach(playlist => {
                 html += `
                      <div class="playlist-item-home">
                         <img src="${playlist.img_playlist ? '/storage/' + playlist.img_playlist : '/img/playlist.png'}" alt="Portada" class="item-cover" onerror="this.src='/img/playlist.png'">
                          <div class="item-info">
                             <div class="item-title">${playlist.n_playlist}</div>
                             <div class="item-subtitle">Playlist</div>
                         </div>
                      </div>
                   `;
             });
             html += '</div></section>';
         }

         // Si no hay resultados
         if (data.songs.length === 0 && data.albums.length === 0 && data.artists.length === 0 && data.playlists.length === 0) {
             html += '<p>No se encontraron resultados.</p>';
         }

         mainContentArea.innerHTML = html;

         // Opcional: actualizar el título de la página
         document.title = searchTerm ? `Buscar: ${searchTerm}` : 'BeatHive';

     };

});
@extends('layouts.clientMain')

@section('content')
    <div class="content-flex-container"> {{-- Contenedor para barra lateral y contenido principal --}}
        @include('layouts.clientList')
        
        {{-- Área para el resto del contenido de la página principal --}}
        <div id="mainContentArea" class="main-content-area"> {{-- Añadir ID para referencia JS --}}
            
            @if(isset($songs) || isset($albums) || isset($artists) || isset($playlists)) {{-- Mostrar resultados de búsqueda si están presentes --}}
                <div class="search-results-view">
                    {{-- Aquí va la estructura para mostrar los resultados de búsqueda --}}

                     @if(isset($searchTerm) && !empty($searchTerm))
                         <h2>Resultados para "{{ $searchTerm }}"</h2>
                     @endif

                     @if(isset($selectedGenres) && $selectedGenres->count() > 0)
                         <div class="selected-genres-display">
                             <span>Filtrando por géneros:</span>
                             @foreach($selectedGenres as $genre)
                                 <span class="selected-genre-tag">{{ $genre->n_genero }}</span>
                             @endforeach
                         </div>
                     @endif

                    @if(isset($mainResult) && $mainResult)
                        <section class="home-section">
                             <div class="section-header">
                                <h2>Resultado principal</h2>
                             </div>
                            <div class="section-content">
                                @if($mainResult['type'] === 'album')
                                     <div class="album-item-home">
                                        <img src="{{ $mainResult['data']->portada_album ? asset('storage/' . $mainResult['data']->portada_album) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                         <div class="item-info">
                                            <div class="item-title">{{ $mainResult['data']->titulo_album }}</div>
                                            <div class="item-subtitle">{{ $mainResult['data']->artista->n_artista ?? 'Artista Desconocido' }}</div>
                                        </div>
                                     </div>
                                @elseif($mainResult['type'] === 'song')
                                     <div class="song-item-home">
                                        <img src="{{ $mainResult['data']->album->portada_album ? asset('storage/' . $mainResult['data']->album->portada_album) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                         <div class="item-info">
                                            <div class="item-title">{{ $mainResult['data']->titulo_cancion }}</div>
                                            <div class="item-subtitle">{{ $mainResult['data']->album->artista->n_artista ?? 'Artista Desconocido' }} - {{ $mainResult['data']->album->titulo_album ?? 'Álbum Desconocido' }}</div>
                                        </div>
                                     </div>
                                @elseif($mainResult['type'] === 'artist')
                                     <div class="artist-item-home">
                                        <img src="{{ $mainResult['data']->img_artista ? asset('storage/' . $mainResult['data']->img_artista) : asset('img/default.jpg') }}" alt="Artista" class="item-cover rounded" onerror="this.src='{{ asset('img/default.jpg') }}'">
                                         <div class="item-info text-center">
                                            <div class="item-title">{{ $mainResult['data']->n_artista }}</div>
                                        </div>
                                     </div>
                                @elseif($mainResult['type'] === 'playlist')
                                     <div class="playlist-item-home">
                                        <img src="{{ $mainResult['data']->img_playlist ? asset('storage/' . $mainResult['data']->img_playlist) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                         <div class="item-info">
                                            <div class="item-title">{{ $mainResult['data']->n_playlist }}</div>
                                            <div class="item-subtitle">Playlist</div> {{-- O podrías poner el nombre del creador --}}
                                        </div>
                                     </div>
                                @endif
                            </div>
                        </section>
                    @endif

                    {{-- Sección de Canciones --}}
                    @if(isset($songs) && $songs->count() > 0)
                         <section class="home-section">
                            <div class="section-header">
                                <h2>Canciones</h2>
                            </div>
                            <div class="section-content vertical-list">
                                @foreach($songs as $song)
                                    <div class="search-result-item song-item">
                                        <img src="{{ $song->album->portada_album ? asset('storage/' . $song->album->portada_album) : asset('img/playlist.png') }}" alt="Portada" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                        <div class="item-info">
                                            <div class="item-title">{{ $song->titulo_cancion }}</div>
                                            <div class="item-subtitle">{{ $song->album->artista->n_artista ?? 'Artista Desconocido' }} - {{ $song->album->titulo_album ?? 'Álbum Desconocido' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Sección de Artistas --}}
                    @if(isset($artists) && $artists->count() > 0)
                         <section class="home-section">
                            <div class="section-header">
                                <h2>Artistas</h2>
                            </div>
                             <div class="section-content horizontal-scroll">
                                @foreach($artists as $artist)
                                     <div class="artist-item-home">
                                        <img src="{{ $artist->img_artista ? asset('storage/' . $artist->img_artista) : asset('img/default.jpg') }}" alt="Artista" class="item-cover rounded" onerror="this.src='{{ asset('img/default.jpg') }}'">
                                         <div class="item-info text-center">
                                            <div class="item-title">{{ $artist->n_artista }}</div>
                                        </div>
                                     </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                     {{-- Sección de Álbumes --}}
                    @if(isset($albums) && $albums->count() > 0)
                         <section class="home-section">
                            <div class="section-header">
                                <h2>Álbumes</h2>
                            </div>
                            <div class="section-content horizontal-scroll">
                                @foreach($albums as $album)
                                     <div class="album-item-home">
                                        <img src="{{ $album->portada_album ? asset('storage/' . $album->portada_album) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                         <div class="item-info">
                                            <div class="item-title">{{ $album->titulo_album }}</div>
                                            <div class="item-subtitle">{{ $album->artista->n_artista ?? 'Artista Desconocido' }}</div>
                                        </div>
                                     </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                     {{-- Sección de Playlists --}}
                    @if(isset($playlists) && $playlists->count() > 0)
                         <section class="home-section">
                            <div class="section-header">
                                <h2>Playlists</h2>
                            </div>
                            <div class="section-content horizontal-scroll">
                                @foreach($playlists as $playlist)
                                     <div class="playlist-item-home">
                                        <img src="{{ $playlist->img_playlist ? asset('storage/' . $playlist->img_playlist) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                         <div class="item-info">
                                            <div class="item-title">{{ $playlist->n_playlist }}</div>
                                            <div class="item-subtitle">Playlist</div> {{-- O podrías poner el nombre del creador --}}
                                        </div>
                                     </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                </div>
            @else
                {{-- Mostrar secciones de inicio si no hay resultados de búsqueda --}}
                
                {{-- Sección: Canciones por Género --}}
                @if($randomGenero && $cancionesByGenero->count() > 0)
                    <section class="home-section">
                        <div class="section-header">
                            <h2>Canciones de {{ $randomGenero->n_genero }}</h2>
                            {{-- <a href="#" class="show-all">Mostrar todos</a> --}}{{-- Opcional: enlace para ver todo --}}
                        </div>
                        <div class="section-content horizontal-scroll">
                            @foreach($cancionesByGenero as $cancion)
                                <div class="song-item-home">
                                    <img src="{{ $cancion->album->portada_album ? asset('storage/' . $cancion->album->portada_album) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                    <div class="item-info">
                                        <div class="item-title">{{ $cancion->titulo_cancion }}</div>
                                        <div class="item-subtitle">{{ $cancion->album->artista->n_artista ?? 'Artista Desconocido' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Sección: Artistas Destacados --}}
                @if($artistasDestacados->count() > 0)
                    <section class="home-section">
                        <div class="section-header">
                            <h2>Artistas Destacados</h2>
                            {{-- <a href="#" class="show-all">Mostrar todos</a> --}}{{-- Opcional: enlace para ver todo --}}
                        </div>
                        <div class="section-content horizontal-scroll">
                            @foreach($artistasDestacados as $artista)
                                <div class="artist-item-home">
                                    <img src="{{ $artista->img_artista ? asset('storage/' . $artista->img_artista) : asset('img/default.jpg') }}" alt="Artista" class="item-cover rounded" onerror="this.src='{{ asset('img/default.jpg') }}'">
                                    <div class="item-info text-center">
                                        <div class="item-title">{{ $artista->n_artista }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Sección: Canciones de un Artista Aleatorio --}}
                 @if($randomArtista && $cancionesByArtista->count() > 0)
                    <section class="home-section">
                        <div class="section-header">
                            <h2>Canciones de {{ $randomArtista->n_artista }}</h2>
                            {{-- <a href="#" class="show-all">Mostrar todos</a> --}}{{-- Opcional: enlace para ver todo --}}
                        </div>
                        <div class="section-content horizontal-scroll">
                            @foreach($cancionesByArtista as $cancion)
                                <div class="song-item-home">
                                    <img src="{{ $cancion->album->portada_album ? asset('storage/' . $cancion->album->portada_album) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                    <div class="item-info">
                                        <div class="item-title">{{ $cancion->titulo_cancion }}</div>
                                        <div class="item-subtitle">{{ $cancion->album->titulo_album ?? 'Álbum Desconocido' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Sección: Canciones de un Álbum Aleatorio --}}
                @if($randomAlbum && $cancionesByAlbum->count() > 0)
                    <section class="home-section">
                        <div class="section-header">
                            <h2>Canciones de {{ $randomAlbum->titulo_album }}</h2>
                            {{-- <a href="#" class="show-all">Mostrar todos</a> --}}{{-- Opcional: enlace para ver todo --}}
                        </div>
                        <div class="section-content horizontal-scroll">
                            @foreach($cancionesByAlbum as $cancion)
                                 <div class="song-item-home">
                                    <img src="{{ $cancion->album->portada_album ? asset('storage/' . $cancion->album->portada_album) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                    <div class="item-info">
                                        <div class="item-title">{{ $cancion->titulo_cancion }}</div>
                                        <div class="item-subtitle">{{ $cancion->album->artista->n_artista ?? 'Artista Desconocido' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Sección: Canciones Recientes --}}
                @if($cancionesRecientes->count() > 0)
                    <section class="home-section">
                        <div class="section-header">
                            <h2>Canciones Recientes</h2>
                            {{-- <a href="#" class="show-all">Mostrar todos</a> --}}{{-- Opcional: enlace para ver todo --}}
                        </div>
                        <div class="section-content horizontal-scroll">
                            @foreach($cancionesRecientes as $cancion)
                                 <div class="song-item-home">
                                    <img src="{{ $cancion->album->portada_album ? asset('storage/' . $cancion->album->portada_album) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                    <div class="item-info">
                                        <div class="item-title">{{ $cancion->titulo_cancion }}</div>
                                        <div class="item-subtitle">{{ $cancion->album->artista->n_artista ?? 'Artista Desconocido' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Sección: Álbumes Recientes --}}
                @if($albumsRecientes->count() > 0)
                    <section class="home-section">
                        <div class="section-header">
                            <h2>Álbumes Recientes</h2>
                            {{-- <a href="#" class="show-all">Mostrar todos</a> --}}{{-- Opcional: enlace para ver todo --}}
                        </div>
                        <div class="section-content horizontal-scroll">
                             @foreach($albumsRecientes as $album)
                                <div class="album-item-home">
                                    <img src="{{ $album->portada_album ? asset('storage/' . $album->portada_album) : asset('img/playlist.png') }}" alt="Portada" class="item-cover" onerror="this.src='{{ asset('img/playlist.png') }}'">
                                     <div class="item-info">
                                        <div class="item-title">{{ $album->titulo_album }}</div>
                                        <div class="item-subtitle">{{ $album->artista->n_artista ?? 'Artista Desconocido' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

            @endif

        </div>
    </div>
@endsection
@section('scripts') {{-- Sección para scripts específicos de esta página --}}


@endsection
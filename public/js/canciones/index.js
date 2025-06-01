document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('bodyCanciones');
    const inputBuscar = document.getElementById('buscarCancion');
    const btnBuscar = document.getElementById('btnBuscar');

    const cargarCanciones = async (search = '') => {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">Cargando...</td></tr>`;

        try {
            const response = await fetch(`/dashboard/api/canciones?search=${encodeURIComponent(search)}`);
            const canciones = await response.json();

            if (canciones.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center">No se encontraron canciones.</td></tr>`;
                return;
            }

            tbody.innerHTML = '';
            canciones.forEach(c => {
                const artistas = c.artistas_colaboradores.map(a => a.n_artista).join(', ') || '-';
                const generos = c.generos.map(g => g.n_genero).join(', ') || '-';
                const artistaPrincipal = c.album?.artista?.n_artista || '-';

                tbody.innerHTML += `
                    <tr>
                        <td>${c.titulo_cancion}</td>
                        <td>${c.duracion}</td>
                        <td>${c.album?.titulo_album || '-'}</td>
                        <td>${artistaPrincipal}</td>
                        <td>${artistas}</td>
                        <td>${generos}</td>
                        <td>
                            <a href="/dashboard/canciones/${c.id_cancion}/editar" class="btn btn-sm btn-warning">Editar</a>
                            <button class="btn btn-sm btn-danger" onclick="eliminarCancion(${c.id_cancion})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } catch (error) {
            console.error(error);
            tbody.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Error al cargar canciones</td></tr>`;
        }
    };

    btnBuscar.addEventListener('click', () => {
        const term = inputBuscar.value.trim();
        cargarCanciones(term);
    });

    window.eliminarCancion = async (id) => {
        if (!confirm('¿Estás seguro de eliminar esta canción?')) return;

        try {
            const res = await fetch(`/dashboard/canciones/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await res.json();
            if (data.success) {
                alert('Canción eliminada correctamente');
                cargarCanciones();
            } else {
                alert('Error al eliminar');
            }
        } catch (err) {
            console.error(err);
            alert('Error inesperado');
        }
    };

    // Cargar al inicio
    cargarCanciones();
});

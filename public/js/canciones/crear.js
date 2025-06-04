document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formCrearCancion');
    const erroresDiv = document.getElementById('errores');
    const redirectUrl = form.dataset.redirect; // Leer la URL desde el atributo data-redirect

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        erroresDiv.innerHTML = ''; // Limpiar errores previos

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                alert('Canción creada correctamente');
                window.location.href = redirectUrl; // Redirigir usando la URL del atributo
            } else if (response.status === 422) {
                // Errores de validación
                const errorData = await response.json();
                const errors = errorData.errors || {};

                let mensajes = '<ul>';
                for (const campo in errors) {
                    erroresDiv.style.display = 'block';
                    errors[campo].forEach(msg => {
                        mensajes += `<li>${msg}</li>`;
                    });
                }
                mensajes += '</ul>';
                erroresDiv.innerHTML = mensajes;
            } else {
                throw new Error('Error en la petición');
            }
        } catch (error) {
            console.error('Error inesperado:', error);
            erroresDiv.textContent = 'Error inesperado al guardar la canción.';
        }
    });
});

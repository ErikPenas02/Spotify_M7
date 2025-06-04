document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formEditarCancion');
    const erroresDiv = document.getElementById('errores');
    const redirectUrl = form.dataset.redirect;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        erroresDiv.innerHTML = '';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            });

            if (response.ok) {
                alert('Canción actualizada correctamente');
                window.location.href = redirectUrl;
            } else if (response.status === 422) {
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
            erroresDiv.textContent = 'Error inesperado al actualizar la canción.';
        }
    });
});

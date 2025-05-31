const switchBtn = document.getElementById('switchMode');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
let isLogin = true;

switchBtn.addEventListener('click', function() {
    isLogin = !isLogin;
    if (isLogin) {
        loginForm.classList.add('show');
        loginForm.classList.remove('fade');
        registerForm.classList.add('fade');
        registerForm.classList.remove('show');
        switchBtn.textContent = 'SignUp';
    } else {
        loginForm.classList.add('fade');
        loginForm.classList.remove('show');
        registerForm.classList.add('show');
        registerForm.classList.remove('fade');
        switchBtn.textContent = 'Login';
    }
});

// Utilidad para mostrar errores debajo de cada campo y en el label
function showFieldError(form, field, message) {
    var input = form.querySelector('[name="' + field + '"]');
    if (!input) return;
    input.classList.add('input-error');
    // Poner label en rojo
    var label = input.parentNode.querySelector('label');
    if (label) label.classList.add('label-error');
    var error = input.parentNode.querySelector('.field-error');
    if (!error) {
        error = document.createElement('div');
        error.className = 'field-error';
        error.style.color = '#ff4d4d';
        error.style.fontSize = '0.9em';
        error.style.marginTop = '4px';
        input.parentNode.appendChild(error);
    }
    error.textContent = message;
}

function clearFieldErrors(form) {
    var errors = form.querySelectorAll('.field-error');
    errors.forEach(function(e) { e.remove(); });
    var inputs = form.querySelectorAll('.input-error');
    inputs.forEach(function(i) { i.classList.remove('input-error'); });
    var labels = form.querySelectorAll('.label-error');
    labels.forEach(function(l) { l.classList.remove('label-error'); });
}

// Nueva función para limpiar errores de un campo específico
function clearFieldErrorsForField(form, fieldName) {
     var input = form.querySelector('[name="' + fieldName + '"]');
     if (!input) return;
     input.classList.remove('input-error');
     var label = input.parentNode.querySelector('label');
     if (label) label.classList.remove('label-error');
     var error = input.parentNode.querySelector('.field-error');
     if (error) error.remove();
}

// REGISTER: validación en tiempo real y submit
var registerFormElement = document.getElementById('registerFormElement');
if (registerFormElement) {
    var registerInputs = registerFormElement.querySelectorAll('input');
    registerInputs.forEach(function(input) {
        // Añadir listeners para validación en tiempo real (blur y keyup - opcional)
        input.addEventListener('blur', validateRegisterRealtime);
        // Descomenta la siguiente línea si quieres validar en cada keyup
        // input.addEventListener('keyup', validateRegisterRealtime);

        // Limpiar error de campo específico al hacer keyup o focus, sin enviar petición
        input.addEventListener('keyup', function() {
             clearFieldErrorsForField(registerFormElement, this.name);
        });
         input.addEventListener('focus', function() {
             clearFieldErrorsForField(registerFormElement, this.name);
        });
    });

    function validateRegisterRealtime(e) {
        var input = e.target;
        var fieldName = input.name;

        // Limpiar solo el error del campo actual antes de validar en tiempo real
        clearFieldErrorsForField(registerFormElement, fieldName);

        // Opcional: No enviar petición de validación en tiempo real si el campo está vacío
        // La validación de campos vacíos se maneja en el submit final.
        // if (input.value.trim() === '') {
        //     return;
        // }

        var formData = new FormData(registerFormElement);
        // Puedes enviar solo el campo actual si la validación del backend lo permite.
        // Si no, enviamos todo pero procesamos solo el error del campo actual.
        // En este caso, el backend valida todo, así que enviamos todo:

        fetch('/validate-register', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(function(res) { 
             var resClone = res.clone();
             return res.json().catch(function() {
                  console.error('Error al parsear JSON en validateRegisterRealtime:', resClone);
                  return {}; // Devolver vacío si no es JSON
             });
        })
        .then(function(data) {
            // Procesar y mostrar solo el error que corresponda al campo actual
            if (data.errors && data.errors[fieldName]) {
                showFieldError(registerFormElement, fieldName, data.errors[fieldName]);
            }
            // Los errores de otros campos devueltos por el backend se ignoran en esta validación en tiempo real.
        })
        .catch(function(error) {
             console.error('Error en la petición validate-register:', error);
             // Opcional: Mostrar un mensaje de error general
        });
    }

    // Manejar el submit del formulario de registro (validación final y creación de usuario)
    registerFormElement.addEventListener('submit', function(e) {
        e.preventDefault();
        clearFieldErrors(registerFormElement); // Limpiar todos los errores antes del submit
        var formData = new FormData(registerFormElement);
        fetch('/register', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(function(res) { 
             // Clonar la respuesta antes de consumirla para verificar si hay redirección
             var resClone = res.clone(); 
             return res.json().catch(function() { 
                 // Si no es JSON (ej. redirección), manejar aquí
                 if (res.redirected) {
                     window.location.href = res.url;
                 } else {
                     // Manejar otros errores no JSON si es necesario
                     console.error('Respuesta no JSON o error inesperado en register:', res);
                 }
                 // Devolver un objeto vacío para no romper la cadena .then
                 return {}; // Indica que la respuesta no fue un JSON con errores/éxito
             }); 
        })
        .then(function(data) {
             // Si la respuesta original no fue JSON (ej. redirección), data será vacío aquí
            if (Object.keys(data).length === 0) {
                 // Ya fue manejado en el catch de res.json()
                 return;
            }
            // Mostrar todos los errores devueltos por el backend en caso de fallo de registro/validación
            if (data.errors) {
                Object.keys(data.errors).forEach(function(field) {
                    showFieldError(registerFormElement, field, data.errors[field]);
                });
            } else if (data.success) {
                 // Redirigir solo si el registro fue exitoso
                window.location.href = '/home';
            }
        })
        .catch(function(error) {
            console.error('Error en la petición de registro:', error);
            // Opcional: Mostrar un mensaje de error general si la petición falla completamente
            // showFieldError(registerFormElement, 'general', 'Ocurrió un error al intentar registrarse.');
        });
    });
}

// LOGIN
var loginFormElement = document.querySelector('#loginForm form');
if (loginFormElement) {
    loginFormElement.addEventListener('submit', function(e) {
        e.preventDefault();
        clearFieldErrors(loginFormElement); // Limpiar errores antes del submit de login
        var formData = new FormData(loginFormElement);
        
        // Directamente hacemos la petición al endpoint de login
        fetch('/login', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(function(res) { 
            // Clonar la respuesta antes de consumirla
            var resClone = res.clone(); 
            return res.json().catch(function() { 
                // Si no es JSON (ej. redirección), manejar aquí
                if (res.redirected) {
                    window.location.href = res.url;
                } else {
                    // Manejar otros errores no JSON si es necesario
                    console.error('Respuesta no JSON o error inesperado en login:', res);
                }
                // Devolver un objeto vacío para no romper la cadena .then
                return {}; // Indica que la respuesta no fue un JSON con errores/éxito
            }); 
        })
        .then(function(data) {
            // Si la respuesta original no fue JSON (ej. redirección), data será vacío aquí
            if (Object.keys(data).length === 0) {
                 // Ya fue manejado en el catch de res.json()
                 return;
            }

            if (data.errors) {
                if (data.errors.general) {
                    // Poner input, label y mensaje en rojo para el error general (asociado a password)
                    var passwordInput = loginFormElement.querySelector('[name="password"]');
                    if (passwordInput) {
                         showFieldError(loginFormElement, 'password', data.errors.general);
                    }
                    // También limpiar errores específicos de otros campos si existieran de una validación anterior
                    Object.keys(data.errors).forEach(function(field) {
                        if (field !== 'general' && field !== 'password') { // Evitar duplicar el error general si se asocia a password
                             showFieldError(loginFormElement, field, data.errors[field]);
                        }
                    });

                } else {
                    // Mostrar errores específicos de campos (ej. vacíos)
                    Object.keys(data.errors).forEach(function(field) {
                         showFieldError(loginFormElement, field, data.errors[field]);
                    });
                }
            } else if (data.success) {
                window.location.href = '/home';
            }
        })
        .catch(function(error) {
             console.error('Error en la petición de login:', error);
             // Opcional: Mostrar un mensaje de error general si la petición falla completamente
             // showFieldError(loginFormElement, 'general', 'Ocurrió un error al intentar iniciar sesión.');
        });
    });
}
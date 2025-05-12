document.addEventListener('DOMContentLoaded', () => {
    const nombreCompletoInput = document.querySelector("input[name='nombre_completo']");
    const contraseñaInput = document.querySelector("input[name='contraseña_usuario']");
    const fechaNacimientoInput = document.querySelector("input[name='fecha_usuario']");
    const emailInput = document.querySelector("input[name='email_usuario']");
    const form = document.querySelector("form");

    // Validación de Nombre Completo
    nombreCompletoInput.addEventListener('input', () => {
        const nombreRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        if (!nombreRegex.test(nombreCompletoInput.value)) {
            nombreCompletoInput.setCustomValidity("El nombre solo puede contener letras y espacios.");
        } else {
            nombreCompletoInput.setCustomValidity("");
        }
    });

    // Validación de Contraseña
    contraseñaInput.addEventListener('input', () => {
        const contraseñaRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!contraseñaRegex.test(contraseñaInput.value)) {
            contraseñaInput.setCustomValidity("La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");
        } else {
            contraseñaInput.setCustomValidity("");
        }
    });

    // Validación de Fecha de Nacimiento
    fechaNacimientoInput.addEventListener('input', () => {
        if (!fechaNacimientoInput.value) {
            fechaNacimientoInput.setCustomValidity("Por favor, selecciona una fecha.");
            return;
        }

        const fechaSeleccionada = new Date(fechaNacimientoInput.value);
        const fechaActual = new Date();
        if (fechaSeleccionada > fechaActual) {
            fechaNacimientoInput.setCustomValidity("La fecha de nacimiento no puede ser en el futuro.");
        } else {
            fechaNacimientoInput.setCustomValidity("");
        }
    });

    // Validación de Email
    emailInput.addEventListener('input', () => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            emailInput.setCustomValidity("Por favor, ingresa un correo electrónico válido.");
        } else {
            emailInput.setCustomValidity("");
        }
    });

    // Validación al Enviar el Formulario
    form.addEventListener('submit', (event) => {
        let isValid = true;

        // Validar términos y condiciones
        const terminos = document.querySelector('input[type="checkbox"]');
        if (!terminos.checked) {
            alert('Debes aceptar los términos y condiciones para continuar.');
            isValid = false;
        }

        // Prevenir el envío del formulario si hay errores
        if (!form.checkValidity() || !isValid) {
            event.preventDefault();
            alert("Por favor, corrige los errores en el formulario.");
        }
    });
});

// Vista previa de la imagen
function previewImage() {
    const fileInput = document.getElementById('imgRuta');
    const img = document.getElementById('imgPerfil');
    if (!fileInput || !img) return;

    const file = fileInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        img.style.display = 'none';
    }
}

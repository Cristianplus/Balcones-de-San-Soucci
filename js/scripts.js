// Detectar tecla Enter dentro del textarea
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('mensaje');
    const form = textarea.closest('form');

    textarea.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault(); // Evita el salto de línea
            form.submit(); // Envía el formulario
        }
    });
});

// Script para mostrar/ocultar campo "Número de Casa"

        const rolSelect = document.getElementById('rol');
        const grupoNumeroCasa = document.getElementById('grupo-numero-casa');
        const numeroCasaInput = document.getElementById('numero_casa');

        function actualizarVisibilidad() {
            if (rolSelect.value === 'administrador') {
                grupoNumeroCasa.style.display = 'none';
                numeroCasaInput.required = false;
                numeroCasaInput.value = ''; // Limpia el valor cuando es administrador
            } else {
                grupoNumeroCasa.style.display = 'block';
                numeroCasaInput.required = true;
            }
        }

        rolSelect.addEventListener('change', actualizarVisibilidad);
        document.addEventListener('DOMContentLoaded', actualizarVisibilidad);

// Script para interactividad general

document.addEventListener('DOMContentLoaded', function () {
    console.log("JS cargado correctamente");

    // Mensaje de bienvenida
    const bienvenido = document.querySelector('.bienvenida');
    if (bienvenido) {
        bienvenido.addEventListener('click', function () {
            alert("¡Bienvenido a la página del condominio!");
        });
    }

    // Confirmación de simulación de pago
    const enlacesPago = document.querySelectorAll('.simular-pago');
    enlacesPago.forEach(function (enlace) {
        enlace.addEventListener('click', function (event) {
            if (!confirm('¿Seguro que deseas simular el pago de este recibo?')) {
                event.preventDefault(); // Evitar la acción del enlace
            }
        });
    });

    // Lógica para la tabla de solicitudes desplegable
    const tablaSolicitudes = document.getElementById('tabla-solicitudes');
    if (tablaSolicitudes) {
        const filas = tablaSolicitudes.querySelectorAll('.fila-solicitud');

        filas.forEach(fila => {


            fila.addEventListener('click', function (event) {
                if (event.target.tagName !== 'BUTTON' && event.target.tagName !== 'TEXTAREA' && event.target.tagName !== 'INPUT') {
                    const contenido = this.querySelectorAll('.contenido-colapsable');
                    contenido.forEach(div => {
                        div.classList.toggle('expanded');
                    });
                }
            });
        });

        const botonesResponder = document.querySelectorAll('.btn-responder');
        botonesResponder.forEach(boton => {
            boton.addEventListener('click', function () {
                const form = this.nextElementSibling;
                if (form.style.display === 'none' || form.style.display === '') {
                    form.style.display = 'block';
                    this.textContent = 'Cancelar';
                } else {
                    form.style.display = 'none';
                    this.textContent = 'Responder';
                }
            });
        });
    }
});
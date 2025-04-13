// Scripts JS para interactividad

document.addEventListener('DOMContentLoaded', fuction () {
    console.log("JS cargado correctamente");

    // Mensaje de bienvenida
    const bienvenido = document.querySelector('.bienvenida');
    if (bienvenido) {
        bienvenido.addEventListener('click', fuction () {
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
});
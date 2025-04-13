// Cabecera

<?php
// Iniciar sesión sí aún no se ha iniciado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Condominio Balcones de San Soucci</title>
        <link rel="stylesheet" href="css/styles.css">
        <script src="js/scripts.js"></script>
    </head>
    <body>
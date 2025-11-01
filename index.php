<?php
session_start();

// Si existe una sesión y fue actualizada hace menos de 5 minutos (300 segundos)
if (isset($_SESSION['usuario']) && isset($_SESSION['ultimo_acceso'])) {
    $tiempo_inactivo = time() - $_SESSION['ultimo_acceso'];

    if ($tiempo_inactivo < 300) { // 300 segundos = 5 minutos
        header("Location: dashboard.php");
        exit;
    } else {
        // Si pasó más de 5 minutos, se destruye la sesión
        session_unset();
        session_destroy();
    }
}
?>

<!-- Pagina de inicio --> 
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <title>Inicio - Plataforma Condominio Balcones de San Soucci</title>

        <link rel="icon" type="image/png" href="img/icon.png">
        <link rel="stylesheet" href="css/styles.css?v=1.0">
    </head>
    <body>
        <!-- Logo -->
        <div class="logo-container">
            <img src="img/logo.jpg" alt="Logo del Condominio">
            <button class="btn-home" onclick="window.location.href='index.html'"></button>
        </div>
    
        <div class="welcome-container">
            <h1>Bienvenido a la plataforma del Condominio</h1>
            <p>Este sistema permite a los residentes y administradores del condominio <strong>Balcones de San Soucci</strong> consultar y gestionar recibos de administración, así como acceder a documentos importantes.</p>

            <h3>¿Ya tienes una cuenta?</h3>
            <a href="login.php" class="button">Iniciar sesión</a>

            <br><br><br>

            <p style="font-size: 0.9em; color: #595959;">Creado por el Programador Cristian Cordero</p>
        </div>
    </body>
</html>

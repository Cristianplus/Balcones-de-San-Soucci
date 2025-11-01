<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$archivo_actual = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="css/styles.css?v=1.0">

<div class="logo-container">

    <?php
    // Archivos que muestran "Volver al panel"
    $archivos_con_volver_panel = ['usuarios.php','recibos.php', 'documentos.php', 'solicitudes.php'];

    // Archivos que muestran "Volver a usuarios"
    $archivos_con_volver_usuarios = ['crear_usuario.php'];

    // Mostrar botón correspondiente o el placeholder invisible
    if (in_array($archivo_actual, $archivos_con_volver_panel)) {
        echo '<div class="volver-panel-container"><a href="dashboard.php" class="btn-volver">Panel</a></div>';
    } elseif (in_array($archivo_actual, $archivos_con_volver_usuarios)) {
        echo '<div class="volver-panel-container"><a href="usuarios.php" class="btn-volver">Usuarios</a></div>';
    } else {
        // Si no hay botón, se coloca un div invisible con el mismo ancho
        echo '<div class="volver-panel-placeholder"></div>';
    }
    ?>

    <img src="img/logo.jpg" alt="Logo del Condominio">
    <button class="btn-home" onclick="window.location.href='index.php'"></button>
</div>

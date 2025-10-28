<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$archivo_actual = basename($_SERVER['PHP_SELF']); // obtiene el nombre del archivo
?>

<link rel="stylesheet" href="css/styles.css?v=1.0">

<div class="logo-container">
<?php
// Mostrar el enlace solo en ciertos archivos
$archivos_con_volver = ['usuarios.php','recibos.php', 'documentos.php', 'solicitudes.php']; // <- archivo donde aparece "Atrás"

if (in_array($archivo_actual, $archivos_con_volver)) {
    echo '
<div class="volver-panel-container">
    <a href="dashboard.php" class="btn-volver">Panel</a>
</div>';

}
?>
<?php
// Mostrar el enlace solo en ciertos archivos
$archivos_con_volver = ['crear_usuario.php']; // <- archivo donde aparece "Atrás"

if (in_array($archivo_actual, $archivos_con_volver)) {
    echo '
<div class="volver-panel-container">
    <a href="usuarios.php" class="btn-volver">Usuarios</a>
</div>';

}
?>
    <img src="img/logo.jpg" alt="Logo del Condominio">
    <button class="btn-home" onclick="window.location.href='index.html'"></button>
    
</div>



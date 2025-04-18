<?php include 'includes/header.php'; ?>
<?php
session_start();

//Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
include 'includes/db.php';

// Obtener datos de la sesión
$nombre = $_SESSION['nombre'];
$rol = $_SESSION['rol'];

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UFT-8">
        <title>Dashboard - Condominio Balcones de San Soucci</title>
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>
    <h3>Rol: <?php echo htmlspecialchars(ucfirst($rol)); ?></h3>
    <div class="dashboard-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h1>

        <?php if ($rol === "administrador"): ?>
            <!-- Panel de Administrador -->
            <div class="contenedor-enlaces">
                <a class="enlace-item" href="usuarios.php">Gestión de usuarios</a>
                <a class="enlace-item" href="recibos.php">Gestión de recibos</a>
                <a class="enlace-item" href="documentos.php">Gestión de documentos</a>
                <a class="enlace-item" href="solicitudes.php">Ver solicitudes</a>
            </div>
            <?php else: ?>
                <!-- Panel de Residente -->
                <div class="contenedor-enlaces">
                    <a class="enlace-item" href="recibos.php">Ver mis recibos</a>
                    <a class="enlace-item" href="documentos.php">Ver documentos</a>
                    <a class="enlace-item" href="solicitudes.php">Enviar solicitud</a>
                </div>
        <?php endif; ?>

        <p><a href="logout.php" class="logout-button">Cerrar sesión</a></p>
    </body>
</html>
<?php include 'includes/footer.php'; ?>
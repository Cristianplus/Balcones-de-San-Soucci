<?php include 'includes/header.php'; ?>
<?php

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
        <link rel="stylesheet" href="css/styles.css?v=1.0">
    </head>
    <body>
    <h2 style="padding: 20px 0 0 260px;">Rol: <?php echo htmlspecialchars(ucfirst($rol)); ?></h2>
    <div class="dashboard-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h1>

        <?php if ($rol === "administrador"): ?>
            <!-- Panel de Administrador -->
            <div class="contenedor-enlaces">
                <a class="enlace-item" href="usuarios.php">Usuarios</a>
                <a class="enlace-item" href="recibos.php">Recibos</a>
                <a class="enlace-item" href="documentos.php">Documentos</a>
                <a class="enlace-item" href="solicitudes.php">Solicitudes</a>
            </div>
            <?php else: ?>
                <!-- Panel de Residente -->
                <div class="contenedor-enlaces">
                    <a class="enlace-item" href="recibos.php">Recibos</a>
                    <a class="enlace-item" href="documentos.php">Documentos</a>
                    <a class="enlace-item" href="solicitudes.php">Enviar solicitud</a>
                </div>
        <?php endif; ?>

        <p><a href="logout.php" class="logout-button">Cerrar sesión</a></p>
    </body>
    </div>
</html>
<br><br>
<?php include 'includes/footer.php'; ?>
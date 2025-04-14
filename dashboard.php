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
    <div class="dashboard-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h1>
        <h3>Rol: <?php echo htmlspecialchars(ucfirst($rol)); ?></h3>

        <?php if ($rol === "administrador"): ?>
            <h2>Panel de Administrador</h2>
            <table class="dashboard-table">
            <ul>
                <li><a href="usuarios.php">Gestión de usuarios</a></li>
                <li><a href="recibos.php">Gestión de recibos</a></li>
                <li><a href="documentos.php">Gestión de documentos</a></li>
                <li><a href="solicitudes.php">Ver solicitudes</a></li>
            </ul>
            <?php else: ?>
                <h2>Panel de Residente</h2>
                <table class="dashboard-table">
                <ul>
                    <li><a href="recibos.php">Ver mis recibos</a></li>
                    <li><a href="documentos.php">Ver documentos</a></li>
                    <li><a href="solicitudes.php">Enviar solicitud</a></li>
                </ul>
        <?php endif; ?>

        <p><a href="logout.php" class="logout-button">Cerrar sesión</a></p>
    </body>
</html>
<?php include 'includes/footer.php'; ?>
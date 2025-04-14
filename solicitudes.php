<?php
session_start();
include 'includes/db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Procesar el formulario de nueva solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensaje'])) {
    $mensaje = trim($_POST['mensaje']);
    if (!empty($mensaje)) {
        $stmt = $conn->prepare("INSERT INTO solicitudes (usuario_id, mensaje, fecha) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $usuario_id, $mensaje);
        $stmt->execute();
        $stmt->close();
        $success = "Solicitud enviada correctamente.";
    } else {
        $error = "El mensaje no puede estar vacío.";
    }
}

// Obtener las solicitudes para mostrar
if ($rol === 'administrador') {
    $sql = "SELECT s.id, u.nombre AS usuario, s.mensaje, s.fecha
            FROM solicitudes s
            JOIN usuarios u ON s.usuario_id = u.id
            ORDER BY s.fecha DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT id, mensaje, fecha
            FROM solicitudes
            WHERE usuario_id = ?
            ORDER BY fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
}

$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes - Condominio Balcones de San Soucci</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Solicitudes</h1>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php elseif (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($rol !== 'administrador'): ?>
        <form method="POST" action="solicitudes.php">
            <label for="mensaje">Escribe tu solicitud:</label><br>
            <textarea name="mensaje" id="mensaje" rows="4" cols="50" required></textarea><br>
            <input type="submit" value="Enviar solicitud">
        </form>
        <hr>
    <?php endif; ?>

    <h2>Listado de Solicitudes</h2>
    <table class="tabla-solicitudes">
        <tr>
            <?php if ($rol === 'administrador'): ?>
                <th>Residente</th>
            <?php endif; ?>
            <th>Mensaje</th>
            <th>Fecha</th>
        </tr>
        <?php while ($solicitud = $resultado->fetch_assoc()): ?>
            <tr>
                <?php if ($rol === 'administrador'): ?>
                    <td><?php echo htmlspecialchars($solicitud['usuario']); ?></td>
                <?php endif; ?>
                <td><?php echo nl2br(htmlspecialchars($solicitud['mensaje'])); ?></td>
                <td><?php echo $solicitud['fecha']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="dashboard.php">Volver al panel</a></p>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>

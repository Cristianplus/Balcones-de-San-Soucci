<?php
include 'includes/header.php';
if (isset($_SESSION['mensaje_exito'])) {
    echo '<div id="popup-mensaje" class="popup-mensaje exito">' . $_SESSION['mensaje_exito'] . '<span class="close-btn" onclick="closePopup()">&times;</span></div>';
    unset($_SESSION['mensaje_exito']);
}
if (isset($_SESSION['mensaje_error'])) {
    echo '<div id="popup-mensaje" class="popup-mensaje error">' . $_SESSION['mensaje_error'] . '<span class="close-btn" onclick="closePopup()">&times;</span></div>';
    unset($_SESSION['mensaje_error']);
}
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
        $_SESSION['mensaje_exito'] = "Solicitud enviada correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "El mensaje no puede estar vacío.";
    }
    header("Location: solicitudes.php");
    exit;
}

// Procesar respuesta del administrador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuesta']) && $rol === 'administrador') {
    $respuesta = trim($_POST['respuesta']);
    $solicitud_id = $_POST['solicitud_id'];

    if (!empty($respuesta)) {
        $stmt = $conn->prepare("UPDATE solicitudes SET respuesta = ?, fecha_respuesta = NOW() WHERE id = ?");
        $stmt->bind_param("si", $respuesta, $solicitud_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['mensaje_exito'] = "Respuesta enviada correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "La respuesta no puede estar vacía.";
    }
    header("Location: solicitudes.php");
    exit;
}

// Procesar eliminación de solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_solicitud'])) {
    $solicitud_id_eliminar = $_POST['solicitud_id_eliminar'];

    // Verificar permisos para eliminar
    if ($rol === 'administrador') {
        $stmt = $conn->prepare("DELETE FROM solicitudes WHERE id = ?");
        $stmt->bind_param("i", $solicitud_id_eliminar);
    } else {
        $stmt = $conn->prepare("DELETE FROM solicitudes WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $solicitud_id_eliminar, $usuario_id);
    }

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "Solicitud eliminada correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Error al eliminar la solicitud.";
    }
    $stmt->close();
    header("Location: solicitudes.php");
    exit;
}

// Obtener las solicitudes para mostrar
if ($rol === 'administrador') {
    $sql = "SELECT s.id, u.nombre AS usuario, s.mensaje, s.fecha, s.respuesta, s.fecha_respuesta
            FROM solicitudes s
            JOIN usuarios u ON s.usuario_id = u.id
            ORDER BY s.fecha DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT id, mensaje, fecha, respuesta, fecha_respuesta
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
    <h1 style="text-align: center; padding-top: 20px";>Solicitudes</h1>



    <?php if ($rol !== 'administrador'): ?>
        <form method="POST" action="solicitudes.php" class="form-principal">
            <label for="mensaje">Escribe tu solicitud:</label><br>
            <textarea name="mensaje" id="mensaje" rows="4" cols="40" required placeholder="Escribe tu solicitud"></textarea><br><br>
            <input type="submit" value="Enviar solicitud">
        </form>
        
    <?php endif; ?>

    <h2 style="text-align: center; padding-top: 20px";>Listado de Solicitudes</h2>
    <table class="tabla-solicitudes" id="tabla-solicitudes">
        <thead>
            <tr>
                <?php if ($rol === 'administrador'): ?>
                    <th>Residente</th>
                <?php endif; ?>
                <th>Mensaje</th>
                <th>Fecha</th>
                <th>Respuesta</th>
                <th>Fecha de Respuesta</th>
                <th colspan="2">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($solicitud = $resultado->fetch_assoc()): ?>
                <tr class="fila-solicitud">
                    <?php if ($rol === 'administrador'): ?>
                        <td><?php echo htmlspecialchars($solicitud['usuario']); ?></td>
                    <?php endif; ?>
                    <td><div class="contenido-colapsable"><?php echo nl2br(htmlspecialchars($solicitud['mensaje'])); ?></div></td>
                    <td>
                        <?php 
                            $fecha = new DateTime($solicitud['fecha']);
                            echo $fecha->format('d/m/Y') . '<br>' . $fecha->format('H:i:s');
                        ?>
                    </td>
                    <td><div class="contenido-colapsable"><?php echo isset($solicitud['respuesta']) ? nl2br(htmlspecialchars($solicitud['respuesta'])) : ''; ?></div></td>
                    <td>
                        <?php 
                            if (!empty($solicitud['fecha_respuesta'])) {
                                $fecha_respuesta = new DateTime($solicitud['fecha_respuesta']);
                                echo $fecha_respuesta->format('d/m/Y') . '<br>' . $fecha_respuesta->format('H:i:s');
                            }
                        ?>
                    </td>
                    <?php if ($rol === 'administrador'): ?>
                        <td>
                            <button class="btn-responder">Responder</button>
                            <form method="POST" action="solicitudes.php" class="form-respuesta" style="display: none;">
                                <input type="hidden" name="solicitud_id" value="<?php echo $solicitud['id']; ?>">
                                <textarea name="respuesta" rows="3" cols="30" required></textarea><br>
                                <input type="submit" value="Enviar Respuesta">
                            </form>
                        </td>
                    <?php else: ?>
                        <td></td>
                    <?php endif; ?>
                    <td>
                        <form method="POST" action="solicitudes.php" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta solicitud?');">
                            <input type="hidden" name="solicitud_id_eliminar" value="<?php echo $solicitud['id']; ?>">
                            <button type="submit" name="eliminar_solicitud" value="Eliminar" class="btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<script src="js/scripts.js"></script>
<script>
    function showPopup() {
    const popup = document.getElementById('popup-mensaje');
    if (popup) {
        popup.style.display = 'block';
        setTimeout(() => {
            popup.style.opacity = 1;
            popup.style.top = '40px';
        }, 10);

        setTimeout(() => {
            closePopup();
        }, 5000); // Cierra el pop-up después de 5 segundos
    }
}

function closePopup() {
    const popup = document.getElementById('popup-mensaje');
    if (popup) {
        popup.style.opacity = 0;
        popup.style.top = '20px';
        setTimeout(() => {
            popup.style.display = 'none';
        }, 500);
    }
}

document.addEventListener('DOMContentLoaded', showPopup);
</script>
    
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
<?php include 'includes/footer.php'; ?>

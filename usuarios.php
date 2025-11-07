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
?>

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
<?php

include("includes/db.php");

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Manejar la eliminación de usuarios
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: usuarios.php");
    exit;
}

// Obtener la lista de usuarios
$resultado = $conn->query("SELECT id, nombre, numero_casa, rol, correo FROM usuarios ORDER BY id ASC");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="css/styles.css?v=1.0">
</head>
<body>
    <h1 style="text-align: center; padding-top: 15px";>Gestión de Usuarios</h1>
    <a href="crear_usuario.php" class="button">+ Crear Usuario</a>
    <table class="tabla-recibos">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Casa</th>
                <th>Rol</th>
                <th>Correo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

            <?php while ($usuario = $resultado->fetch_assoc()): ?>
                <tr>
                    
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td style="text-align: center;"><?php echo htmlspecialchars($usuario['numero_casa']); ?></td>
                    <td><?php echo $usuario['rol']; ?></td>
                    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="button-user">Editar</a>
                        <?php if ($usuario['rol'] !== 'administrador'): ?>
                        <a href="usuarios.php?eliminar=<?php echo $usuario['id']; ?>" 
                        class="button-user" 
                        onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                        Eliminar
                        </a>
                    <?php endif; ?>

                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
<?php include 'includes/footer.php'; ?>
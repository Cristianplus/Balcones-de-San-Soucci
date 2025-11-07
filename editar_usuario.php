<?php
include 'includes/header.php';
// Iniciar sesión y verificar si el usuario es administrador

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
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
include("includes/db.php");

// Obtener el ID del usuario a editar
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener los datos actuales del usuario
    $stmt = $conn->prepare("SELECT nombre, numero_casa, rol, correo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
    } else {
        echo "Usuario no encontrado.";
        exit;
    }

    $stmt->close();
} else {
    echo "ID de usuario no proporcionado.";
    exit;
}

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $rol = $_POST['rol'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    // Si el rol es administrador, forzar número de casa a NULL
    if ($rol === 'administrador') {
        $numero_casa = null;
    } else {
        // Si es residente, tomar el número de casa del formulario (o NULL si vacío)
        $numero_casa = (!empty($_POST['numero_casa'])) ? $_POST['numero_casa'] : null;
    }

    // Si se proporciona una nueva contraseña
    if (!empty($contraseña)) {
        $contraseña_encriptada = md5($contraseña);

        $sql = "UPDATE usuarios 
                SET nombre = ?, numero_casa = ?, rol = ?, correo = ?, contraseña = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $_SESSION['mensaje_error'] = "Error al preparar la consulta (actualizar contraseña): " . $conn->error;
            header("Location: editar_usuario.php?id=" . $id);
            exit;
        }

        $stmt->bind_param("sssssi", $nombre, $numero_casa, $rol, $correo, $contraseña_encriptada, $id);

    } else {
        // Si no se cambia la contraseña
        $sql = "UPDATE usuarios 
                SET nombre = ?, numero_casa = ?, rol = ?, correo = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $_SESSION['mensaje_error'] = "Error al preparar la consulta (UPDATE sin contraseña): " . $conn->error;
            header("Location: editar_usuario.php?id=" . $id);
            exit;
        }

        $stmt->bind_param("ssssi", $nombre, $numero_casa, $rol, $correo, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "Usuario editado exitosamente.";
        header("Location: usuarios.php");
        exit;
    } else {
        $_SESSION['mensaje_error'] = "Error al actualizar el usuario: " . $stmt->error;
        header("Location: editar_usuario.php?id=" . $id);
        exit;
    }

    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1 style="padding: 10px; text-align: center";>Editar Usuario</h1>
    <form action="editar_usuario.php?id=<?php echo $id; ?>" method="POST" class="form-principal">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="administrador" <?php if ($usuario['rol'] === 'administrador') echo 'selected'; ?>>Administrador</option>
                <option value="residente" <?php if ($usuario['rol'] === 'residente') echo 'selected'; ?>>Residente</option>
            </select>
        </div>

        <div class="form-group" id="grupo-numero-casa">
            <label for="numero_casa">Número de Casa:</label>
            <input type="text" name="numero_casa" value="<?php echo htmlspecialchars($usuario['numero_casa']); ?>">
        </div>

        <div class="form-group">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
        </div>

        <div class="form-group">
            <label for="contraseña">Nueva Contraseña (dejar en blanco para no cambiarla):</label>
            <input type="password" name="contraseña">
        </div>

        <div class="form-group">
            <input type="submit" value="Actualizar Usuario">
        </div>
    </form>
    <script src="js/scripts.js"></script>
    

</body>
</html>


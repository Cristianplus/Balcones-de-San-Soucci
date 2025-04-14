<?php
// Iniciar sesión y verificar si el usuario es administrador
session_start();
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
    $numero_casa = $_POST['numero_casa'];
    $rol = $_POST['rol'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    if (!empty($contrasena)) {
        // Si se proporciona una nueva contraseña, encriptarla
        $contrasena_encriptada = md5($contrasena);
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, numero_casa = ?, rol = ?, correo = ?, contrasena = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nombre, $numero_casa, $rol, $correo, $contrasena_encriptada, $id);
    } else {
        // Si no se proporciona una nueva contraseña, no actualizarla
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, numero_casa = ?, rol = ?, correo = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nombre, $numero_casa, $rol, $correo, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "✅ Usuario editado exitosamente.";
        header("Location: usuarios.php");
        exit;
    } else {
        echo "Error al actualizar el usuario: " . $stmt->error;
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
    <h2>Editar Usuario</h2>
    <form action="editar_usuario.php?id=<?php echo $id; ?>" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="numero_casa">Número de Casa:</label>
            <input type="text" name="numero_casa" value="<?php echo htmlspecialchars($usuario['numero_casa']); ?>" required>
        </div>

        <div class="form-group">
            <label for="rol">Rol:</label>
            <select name="rol" required>
                <option value="administrador" <?php if ($usuario['rol'] === 'administrador') echo 'selected'; ?>>Administrador</option>
                <option value="residente" <?php if ($usuario['rol'] === 'residente') echo 'selected'; ?>>Residente</option>
            </select>
        </div>

        <div class="form-group">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
        </div>

        <div class="form-group">
            <label for="contrasena">Nueva Contraseña (dejar en blanco para no cambiarla):</label>
            <input type="password" name="contrasena">
        </div>

        <div class="form-group">
            <input type="submit" value="Actualizar Usuario">
        </div>
    </form>
    <p><a href="usuarios.php">Volver a la lista de usuarios</a></p>
</body>
</html>


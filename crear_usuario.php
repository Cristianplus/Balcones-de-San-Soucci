<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

// Iniciar sesión y verificar si el usuario es administrador
session_start();
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $numero_casa = $_POST['numero_casa'];
    $rol = $_POST['rol'];
    $correo = $_POST['correo'];
    $contraseña = md5($_POST['contraseña']); // Encriptar la contraseña con MD5

    // Preparar la consulta para evitar inyección SQL
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, numero_casa, rol, correo, contraseña) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $numero_casa, $rol, $correo, $contraseña);

    if ($stmt->execute()) {
            $_SESSION['mensaje_exito'] = "✅ Usuario creado exitosamente.";
            header("Location: usuarios.php");
            exit;
        } else {
        echo "Error al crear el usuario: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Crear Usuario</h2>
    <form action="crear_usuario.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>

        <div class="form-group">
            <label for="numero_casa">Número de Casa:</label>
            <input type="text" name="numero_casa" id="numero_casa" required>
        </div>

        <div class="form-group">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="administrador">Administrador</option>
                <option value="residente">Residente</option>
            </select>
        </div>

        <div>
            
        </div>

        <div class="form-group">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" id="correo" required>
        </div>

        <div class="form-group">
            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contraseña" id="contraseña" required>
        </div>

        <div class="form-group">
            <input type="submit" value="Crear Usuario">
        </div>
    </form>
    <p><a href="usuarios.php">Volver a la lista de usuarios</a></p>
</body>
</html>

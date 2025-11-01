<?php
session_start();
include("includes/db.php");
include("includes/header.php"); // Encabezado visual

// --- Si ya hay una sesión activa y no ha pasado más de 5 minutos ---
if (isset($_SESSION['usuario_id']) && isset($_SESSION['ultimo_acceso'])) {
    $tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];

    if ($tiempo_transcurrido < 300) { // 300 segundos = 5 minutos
        header("Location: dashboard.php");
        exit;
    } else {
        // Si ya pasaron 5 minutos, se destruye la sesión
        session_unset();
        session_destroy();
    }
}

// --- Verificar si se envió el formulario ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    // Preparar la consulta para evitar inyección SQL
    $stmt = $conn->prepare("SELECT id, nombre, rol, contraseña FROM usuarios WHERE correo = ?");
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña (comparar hash md5)
        if ($usuario['contraseña'] === md5($contraseña)) {
            // Guardar los datos en la sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['ultimo_acceso'] = time(); // 🕒 Guarda hora actual

            // Redirigir al panel principal
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Condominio Balcones de San Soucci</title>
    <link rel="stylesheet" href="css/styles.css?v=1.0">
</head>
<body>

    <h2 style="padding-left: 330px; padding-top: 20px;">Iniciar Sesión</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red; text-align:center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST" class="form-principal">
        <label for="correo">Correo Electrónico:</label><br>
        <input type="email" id="correo" name="correo" class="campo-login" required>

        <label for="contraseña">Contraseña:</label><br>
        <input type="password" id="contraseña" name="contraseña" class="campo-login" required><br>

        <input type="submit" value="Ingresar" class="boton-login">
    </form>

</body>
</html>

<br><br><br>
<?php include("includes/footer.php"); ?>

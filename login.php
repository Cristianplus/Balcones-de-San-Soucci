<?php include 'includes/header.php'; ?>
<?php
// Iniciar sesión
session_start();

// Incluir el encabezado
include("includes/header.php");

// Incluir la conexión a la base de datos
include("includes/db.php");

// Verificar sí se envió el formulario
if ($_SERVER["REQUEST_METHOD"]=="POST") {
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

    if($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar la contraseña
        if ($usuario['contraseña'] === md5($contraseña)) {
            // Guardar los datos en la sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];          

            // Redirigir según el rol del usuario
            if ($usuario['rol'] === 'administrador') {
                header("Location: dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
}
?>

<?php
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Iniciar Sesión - Condominio Balcones de San Soucci</title>
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>

    <!-- Logo -->
    <div class="logo-container">
        <img src="img/logo.jpg" alt="Logo del Condominio">
    </div>
    
        <h2>Iniciar Sesión</h2>

        <?php if (!empty($error)): ?>
            <p styles = "color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action = "login.php" method = "POST">
        <label for = "correo">Correo Electrónico:</label><br>
        <input type = "email" id = "correo" name = "correo" required><br><br>
        <label for = "contraseña">Contraseña:</label><br>
        <input type = "password" id = "contraseña" name = "contraseña" required><br><br>

        <input type = "submit" value = "Ingresar">
        </form>

        <p><a href = "index.html">Volver al Inicio</a></p>
    </body>
</html>
<?php include("includes/footer.php"); ?>
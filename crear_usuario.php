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
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

// Iniciar sesión y verificar si el usuario es administrador

if ($_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $rol = $_POST['rol'];
    $numero_casa = ($rol === 'administrador') ? null : $_POST['numero_casa']; // Si el rol es administrador, no se requiere número de casa
    $correo = $_POST['correo'];
    $contraseña = md5($_POST['contraseña']); // Encriptar la contraseña con MD5

    // Preparar la consulta para evitar inyección SQL
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, numero_casa, rol, correo, contraseña) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $numero_casa, $rol, $correo, $contraseña);

    if ($stmt->execute()) {
            $_SESSION['mensaje_exito'] = "Usuario creado exitosamente.";
            header("Location: usuarios.php");
            exit;
        } else {
        $_SESSION['mensaje_error'] = "Error al crear el usuario: " . $stmt->error;
        header("Location: crear_usuario.php");
        exit;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="css/styles.css?v=1.0">
</head>
<body>
    <h1 style="padding-left: 130px";>Crear Usuario</h1>
    <form action="crear_usuario.php" method="POST" class="form-principal">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>

        <div class="form-group">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="administrador">Administrador</option>
                <option value="residente">Residente</option>
            </select>
        </div>

        <div class="form-group" id="grupo-numero-casa">
            <label for="numero_casa">Número de Casa:</label>
            <input type="text" name="numero_casa" id="numero_casa">
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

    <!-- Script para ocultar el campo "Número de Casa" si se selecciona "administrador" -->
    <script>
        const rolSelect = document.getElementById('rol');
        const grupoNumeroCasa = document.getElementById('grupo-numero-casa');

        function actualizarVisibilidad() {
            if (rolSelect.value === 'administrador') {
                grupoNumeroCasa.style.display = 'none';
                document.getElementById('numero_casa').required = false;
            } else {
                grupoNumeroCasa.style.display = 'block';
                document.getElementById('numero_casa').required = true;
            }
        }

        rolSelect.addEventListener('change', actualizarVisibilidad);

        // Llamamos a la función al cargar la página por si ya viene con un valor seleccionado
        actualizarVisibilidad();
    </script>
</body>
</html>

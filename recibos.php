<?php include 'includes/header.php'; ?>
<?php

session_start();
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Procesar el formulario para agregar un recibo (solo admin)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $rol === 'administrador') {
    $usuario_id = $_POST['usuario_id'];
    $monto = $_POST['monto'];
    $fecha_limite = $_POST['fecha_limite'];
    $estado = $_POST['estado'];

    // Insertar el nuevo recibo en la base de datos
    $stmt = $conn->prepare("INSERT INTO recibos (usuario_id, monto, fecha_limite, estado) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $usuario_id, $monto, $fecha_limite, $estado);

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "✅ Recibo agregado exitosamente.";
        header("Location: recibos.php"); // Redirigir para evitar reenvío
        exit;
    } else {
        echo "Error al agregar el recibo: " . $stmt->error;
    }

    $stmt->close();
}

// Si se envió una solicitud de "pago simulado"
if (isset($_GET['pagar_id'])) {
    $reciboId = intval($_GET['pagar_id']);

    // Solo se actualiza si el recibo pertenece al usuario o si es el administrador
    if ($rol === 'administrador') {
        $stmt = $conn->prepare("UPDATE recibos SET estado = 'Pagado' WHERE id = ?");
        $stmt->bind_param("i", $reciboId);
    } else {
        $stmt = $conn->prepare("UPDATE recibos SET estado = 'Pagado' WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $reciboId, $usuario_id);
    }
    $stmt->execute();
    $stmt->close();

    // Redirigir para evitar reenvío del formulario
    header("Location: recibos.php");
    exit;
}

// Consultar recibos según el rol del usuario
if ($rol === 'administrador') {
    $sql = "SELECT r.id, u.nombre AS usuario, r.monto, r.fecha_limite, r.estado
            FROM recibos r
            JOIN usuarios u ON r.usuario_id = u.id
            ORDER BY r.fecha_limite DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT id, monto, fecha_limite, estado
            FROM recibos
            WHERE usuario_id = ?
            ORDER BY fecha_limite DESC";
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
    <title>Recibos - Condominio Balcones de San Soucci</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Recibos de Administración</h1>

    <?php if ($rol === 'administrador'): ?>
    <h2>Agregar Recibo</h2>
    <form action="recibos.php" method="POST">
        <div class="form-group">
            <label for="usuario_id">Residente:</label>
            <select name="usuario_id" id="usuario_id" required>
                <?php
                // Obtener los residentes para mostrarlos en el formulario
                $stmt_residentes = $conn->prepare("SELECT id, nombre FROM usuarios WHERE rol = 'residente'");
                $stmt_residentes->execute();
                $result_residentes = $stmt_residentes->get_result();
                while ($residente = $result_residentes->fetch_assoc()):
                ?>
                    <option value="<?php echo $residente['id']; ?>"><?php echo htmlspecialchars($residente['nombre']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="monto">Monto:</label>
            <input type="number" name="monto" id="monto" required>
        </div>

        <div class="form-group">
            <label for="fecha_limite">Fecha límite:</label>
            <input type="date" name="fecha_limite" id="fecha_limite" required>
        </div>

        <div class="form-group">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required>
                <option value="Pendiente">Pendiente</option>
                <option value="Pagado">Pagado</option>
            </select>
        </div>

        <div class="form-group">
            <input type="submit" value="Agregar Recibo">
        </div>
    </form>
<?php endif; ?>


    <table class="tabla-recibos">
        <tr>
            <?php if ($rol === 'administrador'): ?>
                <th>Residente</th>
            <?php endif; ?>
            <th>Monto</th>
            <th>Fecha límite</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>

        <?php while ($recibo = $resultado->fetch_assoc()): ?>
            <tr>
                <?php if ($rol === 'administrador'): ?>
                    <td><?php echo htmlspecialchars($recibo['usuario']); ?></td>
                <?php endif; ?>
                <td>$<?php echo number_format($recibo['monto'], 0, ',', '.'); ?></td>
                <td><?php echo $recibo['fecha_limite']; ?></td>
                <td><?php echo $recibo['estado']; ?></td>
                <td>
                    <?php if ($recibo['estado'] !== 'Pagado'): ?>
                        <a href="recibos.php?pagar_id=<?php echo $recibo['id']; ?>">Simular pago</a>
                    <?php else: ?>
                        Pagado
                    <?php endif; ?>
                </td>
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
<?php include 'includes/footer.php'; ?>

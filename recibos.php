// Gestión y vsualización de recibos
<?php include 'includes/header.php'; ?>
<?php
session_start();

//Verificar si el usuario ha iniciado sesión
if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Si se envió una solicitud de "pago simulado"
if (isset($_GET['pagar_id'])) {
    $reciboId = intval($_GET['pagar_id']);

    // Solo se actualiza sí el recibo pertenece al usuario o si es el administrador
    if ($rol === 'administrador') {
        $stmt = $conn->prepare("UPDATE recibos SET estado = 'Pagado' WHERE id = ?");
        $stmt->bind_param("i", $reciboId);
    } else {
        $stmt = $conn->prepare("UPDATE recibos SET estado = 'Pagado' WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $reicboId, $usuarioId);
    }
    $stmt->execute();
    $stmt->close();

    // Redirigir para evitar re-envío del formulario
    header("Location: recibos.php");
    exit;
}

// Consultar recibos según el rol del usuario
if ($rol === 'administrador') {
    $sql = "SELECT r.id, u.nombre AS usuario, r.monto, r.fecha_limite, r.estado
            FROM recubos r
            JOIN usuarios u ON r.usuario_id = u.id
            ORDER BY r.fceha_limite DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT id, monto, fecha_limite, estado
            FROM recibos
            WHERE usuario_id = ?
            ORDER BY fecha_limite DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuarioId);
}

$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Recibos - Condominio Balcones de San Soucci</title>
        <link rel= "stylesheet" href= "css/styles.css">
    </head>
    <body>
        <h1>Recibos de Administración</h1>

        <table class="tabla-recibos">
            <tr>
                <?php if ($rol === 'adminstrador'): ?>
                    <th>Usuario</th>
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
// Gestión y visualización de documentos

<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include 'include/db.php';

$usuarioId = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Consultar documentos según el rol del usuario
if ($rol === 'administrador') { 
    $sql = "SELECT d.id, u.nombre AS usuario, d.tipo, d.url
            FROM documentos d
            JOIN usuarios u ON d.usuario_id = u.id
            ORDER BY d.id DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT id, tipo, url
            FROM documentos
            WHERE usuario_id = ?
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuarioId);
}

$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF_8">
    <title>Documentos - Condominio Balcones de San Soucci</title>
    <link rel="stylesheet" href= "css/styles.css">
</head>
<body>
    <h1>Documentos del Condominio</h1>

    <table class="tabla.documentos">
    <tr>
        <?php if ($rol === 'administrador'): ?>
            <th>Usuario</th>
        <?php endif; ?>
        <th>Tipo de documentos</th>
        <th>Enlace</th>
    </tr>

    <?php while ($documento = $resultado->fetch_assoc()): ?>
        <tr>
            <?php if ($rol === 'administrador'): ?>
                <td><?php echo htmlspecialchars($documento['usuario']); ?></td>
            <?php endif; ?>
            <td><?php echo ucfirst(str_replace('_', ' ', $documento['tipo'])); ?></td>
            <td>
                <a href="<?php echo htmlspecialchars($documento['url']); ?>" target="_blanck">Ver documento</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </table>

    <p><a href="dashboard.php">Volver al panel</a></p>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$stmt->close();
$stmt->close();
?>
<?php include 'includes/header.php'; ?>
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/db.php';

// Consultar todos los documentos sin restricciones por rol
$sql = "SELECT id, tipo, url
        FROM documentos
        ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos - Condominio Balcones de San Soucci</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Documentos del Condominio</h1>

    <table class="tabla-documentos">
        <tr>
            <th>Tipo de documento</th>
            <th>Enlace</th>
        </tr>

        <?php while ($documento = $resultado->fetch_assoc()): ?>
            <?php
            // Determinar la URL según el tipo de documento
            switch ($documento['tipo']) {
                case 'obra':
                    $enlace = 'https://drive.google.com/file/d/1m5RqNo9WOlTtv7g5Ailn_xs5t8EVEHif/view?usp=sharing';
                    break;
                case 'trasteo':
                    $enlace = 'https://drive.google.com/drive/folders/1VtI47QJBUKViP-pLd_WITirnWZzz8x06?usp=sharing';
                    break;
                case 'alquiler_area_social':
                    $enlace = 'https://drive.google.com/file/d/14vZMyR2oz7t7q5njJ-Kck4OpeqSXh6gV/view?usp=sharing';
                    break;
                default:
                    $enlace = '#'; // Enlace por defecto si el tipo no coincide
                    break;
            }
            ?>
            <tr>
                <td><?php echo ucfirst(str_replace('_', ' ', $documento['tipo'])); ?></td>
                <td>
                    <a href="<?php echo htmlspecialchars($enlace); ?>" target="_blank">Ver documento</a>
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
$conn->close();
?>
<?php include 'includes/footer.php'; ?>

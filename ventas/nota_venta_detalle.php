<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
require "../config.php";

$folio = isset($_GET['folio']) ? $_GET['folio'] : null;

if (!$folio) {
    die("Folio no proporcionado");
}

// Obtener detalles de la nota de venta
$sql_nota = "SELECT nv_folio, nv_fecha, nv_glosa FROM nota_venta WHERE nv_folio = :folio";
$stmt_nota = $conn->prepare($sql_nota);
$stmt_nota->bindParam(':folio', $folio, PDO::PARAM_INT);
$stmt_nota->execute();
$nota_venta = $stmt_nota->fetch(PDO::FETCH_ASSOC);

if (!$nota_venta) {
    die("Nota de venta no encontrada");
}

// Obtener detalles de los productos
$sql_detalle = "SELECT dnv.ndet_producto, dnv.ndet_cantidad, p.pro_nombre 
                FROM detalle_nota_venta dnv 
                JOIN producto p ON dnv.ndet_producto = p.pro_codigo 
                WHERE dnv.ndet_nota_venta = :folio";
$stmt_detalle = $conn->prepare($sql_detalle);
$stmt_detalle->bindParam(':folio', $folio, PDO::PARAM_INT);
$stmt_detalle->execute();
$detalles = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Nota de Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
</head>
<body>
    <?php include '../menu.php'; ?>
    <div class="ui container" style="margin-top: 20px;">
        <h2 class="ui header">Detalle de Nota de Venta</h2>
        <div class="ui segment">
            <h3>Información de la Nota</h3>
            <p><strong>Folio:</strong> <?php echo htmlspecialchars($nota_venta['nv_folio']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d-m-Y', strtotime($nota_venta['nv_fecha'])); ?></p>
            <p><strong>Glosa:</strong> <?php echo htmlspecialchars($nota_venta['nv_glosa']); ?></p>
        </div>

        <div class="ui segment">
            <h3>Productos</h3>
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['ndet_producto']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['pro_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['ndet_cantidad']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <a href="lista_nota_venta.php" class="ui button green">Volver al listado</a>
    </div>

    <script>
    $(document).ready(function() {
        $('.ui.dropdown').dropdown();
    });
    </script>
</body>
</html>
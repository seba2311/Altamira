<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
require "config.php";

$folio = isset($_GET['folio']) ? $_GET['folio'] : null;

if (!$folio) {
    die("Folio no proporcionado");
}

$mensaje = '';

// Función para recepcionar la guía
function recepcionarGuia($conn, $folio) {
    try {
        $conn->beginTransaction();

        // Actualizar el estado de la guía de entrada
        $sql_update_guia = "UPDATE guia_entrada SET guia_estado = 'RCP' WHERE guia_folio = :folio";
        $stmt_update_guia = $conn->prepare($sql_update_guia);
        $stmt_update_guia->execute([':folio' => $folio]);

        // Obtener los detalles de la guía
        $sql_detalles = "SELECT gdet_producto, gdet_cantidad FROM detalle_guia_entrada WHERE gdet_guia_entrada = :folio";
        $stmt_detalles = $conn->prepare($sql_detalles);
        $stmt_detalles->execute([':folio' => $folio]);
        $detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

        // Actualizar el stock para cada producto
        $sql_update_stock = "INSERT INTO stock (stock_producto, stock_cantidad) 
                             VALUES (:producto, :cantidad) 
                             ON DUPLICATE KEY UPDATE stock_cantidad = stock_cantidad + VALUES(stock_cantidad)";
        $stmt_update_stock = $conn->prepare($sql_update_stock);

        foreach ($detalles as $detalle) {
            $stmt_update_stock->execute([
                ':producto' => $detalle['gdet_producto'],
                ':cantidad' => $detalle['gdet_cantidad']
            ]);
        }

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollBack();
        return $e->getMessage();
    }
}

// Procesar la recepción si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recepcionar'])) {
    $resultado = recepcionarGuia($conn, $folio);
    if ($resultado === true) {
        $mensaje = "Guía de entrada recepcionada con éxito.";
    } else {
        $mensaje = "Error al recepcionar la guía: " . $resultado;
    }
}

// Obtener detalles de la guía de entrada
$sql_guia = "SELECT guia_folio, guia_fecha, guia_glosa, guia_estado FROM guia_entrada WHERE guia_folio = :folio";
$stmt_guia = $conn->prepare($sql_guia);
$stmt_guia->bindParam(':folio', $folio, PDO::PARAM_INT);
$stmt_guia->execute();
$guia_entrada = $stmt_guia->fetch(PDO::FETCH_ASSOC);

if (!$guia_entrada) {
    die("Guía de entrada no encontrada");
}

// Obtener detalles de los productos
$sql_detalle = "SELECT dget.gdet_producto, p.pro_nombre, dget.gdet_cantidad
                FROM detalle_guia_entrada dget 
                JOIN producto p ON dget.gdet_producto = p.pro_codigo 
                WHERE dget.gdet_guia_entrada = :folio";
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
    <title>Detalle de Guía de Entrada</title>
    <link href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
</head>
<body>
    <?php include 'menu.php'; ?>
    <div class="ui container" style="margin-top: 20px;">
        <h2 class="ui header">Detalle de Guía de Entrada</h2>
        
        <?php if ($mensaje): ?>
            <div class="ui message">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="ui segment">
            <h3>Información de la Guía</h3>
            <p><strong>Folio:</strong> <?php echo htmlspecialchars($guia_entrada['guia_folio']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d-m-Y', strtotime($guia_entrada['guia_fecha'])); ?></p>
            <p><strong>Glosa:</strong> <?php echo htmlspecialchars($guia_entrada['guia_glosa']); ?></p>
            <p><strong>Estado:</strong> 
                <?php
                if ($guia_entrada['guia_estado'] == 'PND') {
                    echo 'Pendiente por recepcionar';
                } else {
                    echo 'Recepcionada';
                }
                ?>
            </p>
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
                            <td><?php echo htmlspecialchars($detalle['gdet_producto']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['pro_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['gdet_cantidad']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($guia_entrada['guia_estado'] == 'PND'): ?>
            <form method="POST" class="ui form">
                <button type="submit" name="recepcionar" class="ui button primary">Recepcionar Todo</button>
            </form>
        <?php endif; ?>
        <br>
        <a href="lista_guia_entrada.php" class="ui button green">Volver al listado</a>
    </div>

    <script>
    $(document).ready(function() {
        $('.ui.dropdown').dropdown();
    });
    </script>
</body>
</html>
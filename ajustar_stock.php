<?php
header('Content-Type: application/json');
require "config.php";

function sendJsonResponse($success, $message = '') {
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

try {
    if (!isset($_POST['codigo']) || !isset($_POST['ingreso']) || !isset($_POST['salida'])) {
        throw new Exception("Datos incompletos");
    }

    $codigo = $_POST['codigo'];
    $ingreso = intval($_POST['ingreso']);
    $salida = intval($_POST['salida']);

    $conn->beginTransaction();

    // Verificar si existe un registro de stock para el producto
    $stmt = $conn->prepare("SELECT stock_cantidad FROM stock WHERE stock_producto = :codigo");
    $stmt->execute(['codigo' => $codigo]);
    $stockActual = $stmt->fetchColumn();

    if ($stockActual === false) {
        // No existe stock, crear uno nuevo
        $nuevoStock = $ingreso - $salida;
        if ($nuevoStock < 0) {
            throw new Exception("No se puede crear un stock negativo");
        }
        
        $stmt = $conn->prepare("INSERT INTO stock (stock_producto, stock_cantidad) VALUES (:codigo, :nuevoStock)");
        $stmt->execute([
            'codigo' => $codigo,
            'nuevoStock' => $nuevoStock
        ]);
        
        $mensaje = "Nuevo stock creado y ajustado correctamente";
    } else {
        // Existe stock, actualizarlo
        $nuevoStock = $stockActual + $ingreso - $salida;
        if ($nuevoStock < 0) {
            throw new Exception("El stock no puede ser negativo");
        }
        
        $stmt = $conn->prepare("UPDATE stock SET stock_cantidad = :nuevoStock WHERE stock_producto = :codigo");
        $stmt->execute([
            'nuevoStock' => $nuevoStock,
            'codigo' => $codigo
        ]);
        
        $mensaje = "Stock actualizado correctamente";
    }

    $conn->commit();
    sendJsonResponse(true, $mensaje);
} catch (Exception $e) {
    $conn->rollBack();
    sendJsonResponse(false, $e->getMessage());
}
?>

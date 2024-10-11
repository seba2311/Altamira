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

    // Obtener stock actual
    $stmt = $conn->prepare("SELECT stock_cantidad FROM stock WHERE stock_producto = :codigo");
    $stmt->execute(['codigo' => $codigo]);
    $stockActual = $stmt->fetchColumn();

    if ($stockActual === false) {
        throw new Exception("Producto no encontrado");
    }

    $nuevoStock = $stockActual + $ingreso - $salida;

    if ($nuevoStock < 0) {
        throw new Exception("El stock no puede ser negativo");
    }

    // Actualizar stock
    $stmt = $conn->prepare("UPDATE stock SET stock_cantidad = :nuevoStock WHERE stock_producto = :codigo");
    $stmt->execute([
        'nuevoStock' => $nuevoStock,
        'codigo' => $codigo
    ]);

    $conn->commit();
    sendJsonResponse(true, "Stock actualizado correctamente");
} catch (Exception $e) {
    $conn->rollBack();
    sendJsonResponse(false, $e->getMessage());
}
?>
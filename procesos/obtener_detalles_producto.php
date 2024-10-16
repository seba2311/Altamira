<?php
header('Content-Type: application/json');

function sendJsonResponse($success, $data, $message = '') {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

try {
    require "../config.php";

    if (!isset($_GET['codigo'])) {
        throw new Exception("Código de producto no proporcionado");
    }

    $codigo = $_GET['codigo'];
    
    $query = "SELECT p.pro_codigo, p.pro_nombre, COALESCE(s.stock_cantidad, 0) as stock_cantidad
              FROM producto p
              LEFT JOIN stock s ON p.pro_codigo = s.stock_producto
              WHERE p.pro_codigo = :codigo";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
    $stmt->execute();
    
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($producto) {
        sendJsonResponse(true, $producto);
    } else {
        sendJsonResponse(false, null, "Producto no encontrado");
    }
} catch (Exception $e) {
    sendJsonResponse(false, null, $e->getMessage());
}
?>
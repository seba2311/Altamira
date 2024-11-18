<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

try {
    // Iniciar transacción
    $conn->beginTransaction();

    // Verificar que se recibieron los datos necesarios
    if (!isset($_POST['folio']) || !isset($_POST['etiquetas'])) {
        throw new Exception('Datos incompletos');
    }
    
    $folio = $_POST['folio'];
    $etiquetas = json_decode($_POST['etiquetas'], true);
    
    if (!is_array($etiquetas) || empty($etiquetas)) {
        throw new Exception('Lista de etiquetas inválida');
    }

    // Verificar si existe la nota y su estado
    $queryExiste = "SELECT nv_estado 
                    FROM nota_venta 
                    WHERE nv_folio = :folio";
                    
    $stmtExiste = $conn->prepare($queryExiste);
    $stmtExiste->bindParam(':folio', $folio);
    $stmtExiste->execute();
    
    $nota = $stmtExiste->fetch(PDO::FETCH_ASSOC);
    
    if (!$nota) {
        throw new Exception('Nota de venta no existe');
    }
    
    if ($nota['nv_estado'] !== 'PND') {
        throw new Exception('Nota de venta ya está terminada');
    }

    // Obtener el detalle de la nota para actualizar stock
    $queryDetalle = "SELECT ndet_producto, ndet_cantidad 
                     FROM detalle_nota_venta 
                     WHERE ndet_nota_venta = :folio";
    
    $stmtDetalle = $conn->prepare($queryDetalle);
    $stmtDetalle->bindParam(':folio', $folio);
    $stmtDetalle->execute();
    
    $detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

    // Actualizar stock para cada producto del detalle
    foreach ($detalles as $detalle) {
        $queryUpdateStock = "UPDATE stock 
                           SET stock_cantidad = stock_cantidad - :cantidad 
                           WHERE stock_producto = :producto";
        
        $stmtUpdateStock = $conn->prepare($queryUpdateStock);
        $stmtUpdateStock->bindParam(':producto', $detalle['ndet_producto']);
        $stmtUpdateStock->bindParam(':cantidad', $detalle['ndet_cantidad']);
        $stmtUpdateStock->execute();

        if ($stmtUpdateStock->rowCount() === 0) {
            throw new Exception('Error al actualizar stock del producto: ' . $detalle['ndet_producto']);
        }
    }

    // Preparar placeholders para la consulta de etiquetas
    $placeholders = str_repeat('?,', count($etiquetas) - 1) . '?';
    
    // Actualizar las etiquetas con el folio de la nota
    $queryUpdateEtiquetas = "UPDATE etiquetas 
                            SET eti_nota_venta = ? 
                            WHERE eti_numero IN ($placeholders)";
    
    $stmtUpdateEtiquetas = $conn->prepare($queryUpdateEtiquetas);
    
    // Preparar array de parámetros con el folio al inicio
    $params = array_merge([$folio], $etiquetas);
    
    // Ejecutar la actualización de etiquetas
    $stmtUpdateEtiquetas->execute($params);

    if ($stmtUpdateEtiquetas->rowCount() !== count($etiquetas)) {
        throw new Exception('Error al actualizar algunas etiquetas');
    }
    
    // Actualizar el estado de la nota
    $queryUpdate = "UPDATE nota_venta 
                   SET nv_estado = 'TER' 
                   WHERE nv_folio = :folio";
                   
    $stmtUpdate = $conn->prepare($queryUpdate);
    $stmtUpdate->bindParam(':folio', $folio);
    $stmtUpdate->execute();
    
    if ($stmtUpdate->rowCount() > 0) {
        // Confirmar todos los cambios
        $conn->commit();

        $response = [
            'status' => 'success',
            'message' => 'Nota de venta despachada correctamente'
        ];
    } else {
        throw new Exception('Error al actualizar el estado de la nota');
    }
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // Revertir cambios en caso de error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    $response = [
        'status' => 'error',
        'message' => 'Error en la base de datos',
        'error' => $e->getMessage()
    ];
    
    http_response_code(500);
    echo json_encode($response);
    
} catch (Exception $e) {
    // Revertir cambios en caso de error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    
    http_response_code(400);
    echo json_encode($response);
}
?>
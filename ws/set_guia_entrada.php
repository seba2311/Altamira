<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

try {
    // Iniciar transacción
    $conn->beginTransaction();

    // Verificar que se recibió el folio
    if (!isset($_POST['folio'])) {
        throw new Exception('Folio no proporcionado');
    }
    
    $folio = $_POST['folio'];
    
    // Verificar si existe la guía
    $queryExiste = "SELECT guia_estado 
                    FROM guia_entrada 
                    WHERE guia_folio = :folio";
                    
    $stmtExiste = $conn->prepare($queryExiste);
    $stmtExiste->bindParam(':folio', $folio);
    $stmtExiste->execute();
    
    $guia = $stmtExiste->fetch(PDO::FETCH_ASSOC);
    
    if (!$guia) {
        throw new Exception('Guía de entrada no existe');
    }
    
    // Verificar que la guía esté pendiente
    if ($guia['guia_estado'] !== 'PND') {
        throw new Exception('Guía de entrada ya está recepcionada');
    }

    // Obtener el detalle de la guía para actualizar stock
    $queryDetalle = "SELECT gdet_producto, gdet_cantidad 
                     FROM detalle_guia_entrada 
                     WHERE gdet_guia_entrada = :folio";
    
    $stmtDetalle = $conn->prepare($queryDetalle);
    $stmtDetalle->bindParam(':folio', $folio);
    $stmtDetalle->execute();
    
    $detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

    // Actualizar stock para cada producto
    foreach ($detalles as $detalle) {
        // Verificar si el producto existe en stock
        $queryStockExiste = "SELECT stock_cantidad 
                            FROM stock 
                            WHERE stock_producto = :producto";
        
        $stmtStockExiste = $conn->prepare($queryStockExiste);
        $stmtStockExiste->bindParam(':producto', $detalle['gdet_producto']);
        $stmtStockExiste->execute();
        
        if ($stmtStockExiste->rowCount() > 0) {
            // Actualizar stock existente
            $queryUpdateStock = "UPDATE stock 
                               SET stock_cantidad = stock_cantidad + :cantidad 
                               WHERE stock_producto = :producto";
        } else {
            // Insertar nuevo registro de stock
            $queryUpdateStock = "INSERT INTO stock (stock_producto, stock_cantidad) 
                               VALUES (:producto, :cantidad)";
        }
        
        $stmtUpdateStock = $conn->prepare($queryUpdateStock);
        $stmtUpdateStock->bindParam(':producto', $detalle['gdet_producto']);
        $stmtUpdateStock->bindParam(':cantidad', $detalle['gdet_cantidad']);
        $stmtUpdateStock->execute();
    }
    
    // Actualizar el estado de la guía
    $queryUpdate = "UPDATE guia_entrada 
                   SET guia_estado = 'RCP' 
                   WHERE guia_folio = :folio";
                   
    $stmtUpdate = $conn->prepare($queryUpdate);
    $stmtUpdate->bindParam(':folio', $folio);
    $stmtUpdate->execute();
    
    if ($stmtUpdate->rowCount() > 0) {
        // Confirmar todos los cambios
        $conn->commit();

        $response = [
            'status' => 'success',
            'message' => 'Estado de guía actualizado y stock actualizado correctamente'
        ];
    } else {
        throw new Exception('Error al actualizar el estado de la guía');
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
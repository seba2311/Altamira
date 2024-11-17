<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

try {
    // Verificar que se recibió el folio
    if (!isset($_GET['folio'])) {
        throw new Exception('Folio no proporcionado');
    }
    
    $folio = $_GET['folio'];
    
    // Primero verificamos si existe la nota de venta independiente del estado
    $queryExiste = "SELECT nv_folio, nv_estado, nv_glosa 
                    FROM nota_venta 
                    WHERE nv_folio = :folio";
                    
    $stmtExiste = $conn->prepare($queryExiste);
    $stmtExiste->bindParam(':folio', $folio);
    $stmtExiste->execute();
    
    $notaVenta = $stmtExiste->fetch(PDO::FETCH_ASSOC);
    
    if (!$notaVenta) {
        throw new Exception('Nota de venta no existe');
    }
    
    // Si existe pero no está pendiente
    if ($notaVenta['nv_estado'] !== 'PND') {
        throw new Exception('Nota de venta ya atendida');
    }
    
    // Si llegamos aquí, la nota existe y está pendiente
    // Obtener el detalle de la nota con información de productos
    $queryDetalle = "SELECT d.ndet_producto, d.ndet_cantidad, 
                            p.pro_codigo, p.pro_nombre
                     FROM detalle_nota_venta d
                     JOIN producto p ON d.ndet_producto = p.pro_codigo
                     WHERE d.ndet_nota_venta = :folio";
                     
    $stmtDetalle = $conn->prepare($queryDetalle);
    $stmtDetalle->bindParam(':folio', $folio);
    $stmtDetalle->execute();
    
    $detalle = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
    
    // Preparar la respuesta
    $response = [
        'status' => 'success',
        'message' => 'Nota de venta recuperada exitosamente',
        'data' => [
            'cabecera' => [
                'folio' => $notaVenta['nv_folio'],
                'estado' => $notaVenta['nv_estado'],
                'glosa' => $notaVenta['nv_glosa']
            ],
            'detalle' => array_map(function($item) {
                return [
                    'producto' => [
                        'codigo' => $item['pro_codigo'],
                        'nombre' => $item['pro_nombre']
                    ],
                    'cantidad' => $item['ndet_cantidad']
                ];
            }, $detalle)
        ]
    ];
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    $response = [
        'status' => 'error',
        'message' => 'Error en la base de datos',
        'error' => $e->getMessage()
    ];
    
    http_response_code(500);
    echo json_encode($response);
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    
    http_response_code(400);
    echo json_encode($response);
}
?>
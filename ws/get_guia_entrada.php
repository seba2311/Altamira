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
    
    // Obtener la cabecera de la guía (solo si está pendiente)
    $queryExiste = "SELECT guia_folio, guia_fecha, guia_estado, guia_glosa 
                    FROM guia_entrada 
                    WHERE guia_folio = :folio";
                    
    $stmtExiste = $conn->prepare($queryExiste);
    $stmtExiste->bindParam(':folio', $folio);
    $stmtExiste->execute();
    
    $guia = $stmtExiste->fetch(PDO::FETCH_ASSOC);
    
    if (!$guia) {
        throw new Exception('Guía de entrada no existe');
    }
    
    // Si existe pero no está pendiente
    if ($guia['guia_estado'] !== 'PND') {
        throw new Exception('Guía de entrada ya atendida');
    }
    
    // Obtener el detalle de la guía con información de productos y sus etiquetas
    $queryDetalle = "SELECT 
                        d.gdet_producto,
                        d.gdet_cantidad,
                        p.pro_codigo,
                        p.pro_nombre,
                        GROUP_CONCAT(e.eti_numero) as etiquetas
                     FROM detalle_guia_entrada d
                     JOIN producto p ON d.gdet_producto = p.pro_codigo
                     LEFT JOIN etiquetas e ON e.eti_producto = d.gdet_producto 
                        AND e.eti_guia_entrada = d.gdet_guia_entrada
                     WHERE d.gdet_guia_entrada = :folio
                     GROUP BY d.gdet_producto, d.gdet_cantidad, p.pro_codigo, p.pro_nombre";
                     
    $stmtDetalle = $conn->prepare($queryDetalle);
    $stmtDetalle->bindParam(':folio', $folio);
    $stmtDetalle->execute();
    
    $detalle = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
    
    // Procesar los resultados para dar formato a las etiquetas
    $detalleFormateado = array_map(function($item) {
        $etiquetasArray = $item['etiquetas'] ? explode(',', $item['etiquetas']) : [];
        return [
            'producto' => [
                'codigo' => $item['pro_codigo'],
                'nombre' => $item['pro_nombre']
            ],
            'cantidad' => $item['gdet_cantidad'],
            'etiquetas' => $etiquetasArray
        ];
    }, $detalle);
    
    // Preparar la respuesta
    $response = [
        'status' => 'success',
        'message' => 'Guía de entrada recuperada exitosamente',
        'data' => [
            'cabecera' => [
                'folio' => $guia['guia_folio'],
                'fecha' => $guia['guia_fecha'],
                'estado' => $guia['guia_estado'],
                'glosa' => $guia['guia_glosa']
            ],
            'detalle' => $detalleFormateado
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
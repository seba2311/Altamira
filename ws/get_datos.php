<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

try {
    // Array para almacenar todas las estadísticas
    $estadisticas = [];

    // 1. Los 10 productos con mayor stock
    $queryMayorStock = "SELECT s.stock_producto, p.pro_nombre, s.stock_cantidad 
                       FROM stock s
                       JOIN producto p ON s.stock_producto = p.pro_codigo
                       WHERE s.stock_cantidad > 0
                       ORDER BY s.stock_cantidad DESC
                       LIMIT 10";
                       
    $stmtMayorStock = $conn->prepare($queryMayorStock);
    $stmtMayorStock->execute();
    $productosMayorStock = $stmtMayorStock->fetchAll(PDO::FETCH_ASSOC);

    // 2. Los 10 productos con menor stock (pero mayor a 0)
    $queryMenorStock = "SELECT s.stock_producto, p.pro_nombre, s.stock_cantidad 
                       FROM stock s
                       JOIN producto p ON s.stock_producto = p.pro_codigo
                       WHERE s.stock_cantidad > 0
                       ORDER BY s.stock_cantidad ASC
                       LIMIT 10";
                       
    $stmtMenorStock = $conn->prepare($queryMenorStock);
    $stmtMenorStock->execute();
    $productosMenorStock = $stmtMenorStock->fetchAll(PDO::FETCH_ASSOC);

    // 3. Productos sin stock (agotados)
    $queryAgotados = "SELECT s.stock_producto, p.pro_nombre, s.stock_cantidad 
                      FROM stock s
                      JOIN producto p ON s.stock_producto = p.pro_codigo
                      WHERE s.stock_cantidad = 0";
                      
    $stmtAgotados = $conn->prepare($queryAgotados);
    $stmtAgotados->execute();
    $productosAgotados = $stmtAgotados->fetchAll(PDO::FETCH_ASSOC);

    // 4. Total de productos diferentes en stock
    $queryTotalProductos = "SELECT COUNT(*) as total FROM stock WHERE stock_cantidad > 0";
    $stmtTotalProductos = $conn->prepare($queryTotalProductos);
    $stmtTotalProductos->execute();
    $totalProductos = $stmtTotalProductos->fetch(PDO::FETCH_ASSOC)['total'];

    // 5. Total de unidades en stock
    $queryTotalUnidades = "SELECT SUM(stock_cantidad) as total FROM stock";
    $stmtTotalUnidades = $conn->prepare($queryTotalUnidades);
    $stmtTotalUnidades->execute();
    $totalUnidades = $stmtTotalUnidades->fetch(PDO::FETCH_ASSOC)['total'];

    // 6. Últimos movimientos (guías de entrada y notas de venta)
    $queryUltimosMovimientos = "
        (SELECT 'Entrada' as tipo, g.guia_folio as folio, g.guia_fecha as fecha, g.guia_estado as estado
         FROM guia_entrada g
         ORDER BY g.guia_fecha DESC
         LIMIT 5)
        UNION ALL
        (SELECT 'Venta' as tipo, n.nv_folio as folio, n.nv_fecha as fecha, n.nv_estado as estado
         FROM nota_venta n
         ORDER BY n.nv_fecha DESC
         LIMIT 5)
        ORDER BY fecha DESC
        LIMIT 10";
        
    $stmtUltimosMovimientos = $conn->prepare($queryUltimosMovimientos);
    $stmtUltimosMovimientos->execute();
    $ultimosMovimientos = $stmtUltimosMovimientos->fetchAll(PDO::FETCH_ASSOC);

    // Preparar la respuesta
    $response = [
        'status' => 'success',
        'message' => 'Estadísticas recuperadas exitosamente',
        'data' => [
            'resumen' => [
                'total_productos_diferentes' => $totalProductos,
                'total_unidades' => $totalUnidades,
                'productos_agotados' => count($productosAgotados)
            ],
            'mayor_stock' => array_map(function($item) {
                return [
                    'codigo' => $item['stock_producto'],
                    'nombre' => $item['pro_nombre'],
                    'cantidad' => intval($item['stock_cantidad'])
                ];
            }, $productosMayorStock),
            'menor_stock' => array_map(function($item) {
                return [
                    'codigo' => $item['stock_producto'],
                    'nombre' => $item['pro_nombre'],
                    'cantidad' => intval($item['stock_cantidad'])
                ];
            }, $productosMenorStock),
            'agotados' => array_map(function($item) {
                return [
                    'codigo' => $item['stock_producto'],
                    'nombre' => $item['pro_nombre']
                ];
            }, $productosAgotados),
            'ultimos_movimientos' => array_map(function($item) {
                return [
                    'tipo' => $item['tipo'],
                    'folio' => $item['folio'],
                    'fecha' => $item['fecha'],
                    'estado' => $item['estado']
                ];
            }, $ultimosMovimientos)
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
}
?>
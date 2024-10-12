<?php
require_once "config.php";

function obtenerProductosMasVendidos($fechaInicio, $fechaFin, $limit = 10) {
    global $conn;
    $sql = "SELECT p.pro_codigo, p.pro_nombre, SUM(dnv.ndet_cantidad) as total_vendido
            FROM nota_venta nv
            JOIN detalle_nota_venta dnv ON nv.nv_folio = dnv.ndet_nota_venta
            JOIN producto p ON dnv.ndet_producto = p.pro_codigo
            WHERE nv.nv_fecha BETWEEN :fecha_inicio AND :fecha_fin
            GROUP BY p.pro_codigo, p.pro_nombre
            ORDER BY total_vendido DESC
            LIMIT :limit";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fecha_inicio', $fechaInicio, PDO::PARAM_STR);
    $stmt->bindParam(':fecha_fin', $fechaFin, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function generarGraficoProductosMasVendidos($idCanvas, $datos) {
    $labels = array_column($datos, 'pro_nombre');
    $data = array_column($datos, 'total_vendido');
    
    // Generar colores aleatorios
    $backgroundColor = [];
    $borderColor = [];
    foreach ($data as $value) {
        $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        $backgroundColor[] = $color . '80'; // 80 al final para 50% de opacidad
        $borderColor[] = $color;
    }

    $chartData = [
        'type' => 'bar',
        'data' => [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Cantidad Vendida',
                'data' => $data,
                'backgroundColor' => $backgroundColor,
                'borderColor' => $borderColor,
                'borderWidth' => 1
            ]]
        ],
        'options' => [
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Cantidad Vendida'
                    ]
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Productos'
                    ]
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => false
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Top ' . count($datos) . ' Productos Más Vendidos'
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return "Vendidos: " + context.parsed.y;
                        }'
                    ]
                ]
            ]
        ]
    ];

    return json_encode($chartData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
}

// Ejemplo de uso:
// $fechaInicio = '2023-01-01';
// $fechaFin = '2023-12-31';
// $productosMasVendidos = obtenerProductosMasVendidos($fechaInicio, $fechaFin);
// $chartData = generarGraficoProductosMasVendidos('graficoVentas', $productosMasVendidos);
?>
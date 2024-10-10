<?php
require_once "config.php";

function obtenerTopProductosStock($limit = 10) {
    global $conn;
    $sql = "SELECT p.pro_nombre, s.stock_cantidad 
            FROM stock s
            JOIN producto p ON s.stock_producto = p.pro_codigo
            ORDER BY s.stock_cantidad DESC
            LIMIT :limit";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function generarGraficoTopProductos($idCanvas, $datos) {
    $labels = array_column($datos, 'pro_nombre');
    $data = array_column($datos, 'stock_cantidad');
    
    // Generar colores aleatorios para cada segmento
    $backgroundColor = [];
    $borderColor = [];
    foreach ($data as $value) {
        $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        $backgroundColor[] = $color . '80'; // 80 al final para 50% de opacidad
        $borderColor[] = $color;
    }

    $chartData = [
        'type' => 'pie',
        'data' => [
            'labels' => $labels,
            'datasets' => [[
                'data' => $data,
                'backgroundColor' => $backgroundColor,
                'borderColor' => $borderColor,
                'borderWidth' => 1
            ]]
        ],
        'options' => [
            'responsive' => true,
            'maintainAspectRatio' => true, // Cambiado a true
            'aspectRatio' => 1, // Añadido para mantener una relación de aspecto cuadrada
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Top ' . count($datos) . ' Productos con Mayor Stock'
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            var label = context.label || "";
                            var value = context.parsed || 0;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = ((value / total) * 100).toFixed(2);
                            return [
                                "Producto: " + label,
                                "Cantidad: " + value,
                                "Porcentaje: " + percentage + "%"
                            ];
                        }'
                    ]
                ]
            ]
        ]
    ];

    return json_encode($chartData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
}
function obtenerProductosBajos($limit = 10) {
    global $conn;
    $sql = "SELECT p.pro_nombre, s.stock_cantidad 
            FROM stock s
            JOIN producto p ON s.stock_producto = p.pro_codigo
            ORDER BY s.stock_cantidad ASC
            LIMIT :limit";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function generarGraficoProductosBajos($idCanvas, $datos) {
    $labels = array_column($datos, 'pro_nombre');
    $data = array_column($datos, 'stock_cantidad');
    
    $chartData = [
        'type' => 'bar',
        'data' => [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Cantidad en Stock',
                'data' => $data,
                'backgroundColor' => 'rgba(255, 99, 132, 0.8)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 1
            ]]
        ],
        'options' => [
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => true
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Top ' . count($datos) . ' Productos con Menor Stock'
                ]
            ]
        ]
    ];

    return json_encode($chartData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
}
?>
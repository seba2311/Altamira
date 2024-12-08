<?php
require_once '../config.php';
require_once 'datos.php';
require_once '../_p.php';

$predictor = new VentasPredictor($conn);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predicción de Ventas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos para la tabla */
        .ui.celled.table {
            width: 100%;
            border-collapse: collapse;
        }

        .ui.celled.table th {
            background-color: #f9fafb;
            padding: 12px 8px;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
        }

        /* Ajuste de anchos de columnas */
        .ui.celled.table th.producto-column {
            width: 25%;
            text-align: left;
        }

        .ui.celled.table th.mes-column {
            width: 10%;
        }

        .ui.celled.table th.prediccion-column {
            width: 12%;
        }

        .ui.celled.table th.fiabilidad-column {
            width: 20%;
        }

        .ui.celled.table th.estado-column {
            width: 13%;
        }

        /* Alineación de celdas */
        .ui.celled.table td {
            padding: 10px 8px;
            vertical-align: middle;
        }

        .ui.celled.table td.numero {
            text-align: center;
        }

        /* Estilos para la barra de progreso */
        .ui.progress {
            margin: 0 !important;
            height: 24px !important;
            border-radius: 4px !important;
            background-color: #f3f3f3 !important;
            position: relative !important;
            overflow: visible !important;
            /* Permitir que el texto sea visible fuera de la barra */
        }

        .ui.progress .bar {
            min-width: 2px !important;
            border-radius: 4px !important;
            position: absolute !important;
            height: 100% !important;
        }

        /* Nuevo contenedor para el texto */
        .progress-text {
            position: absolute !important;
            width: 100% !important;
            text-align: center !important;
            line-height: 24px !important;
            z-index: 2 !important;
            color: #000 !important;
            font-weight: 500 !important;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.7) !important;
            left: 0 !important;
        }

        /* Estilos para las etiquetas de estado */
        .ui.label {
            width: 100%;
            text-align: center;
            margin: 0 !important;
        }

        /* Tooltip styles */
        .tooltip-header {
            position: relative;
            cursor: help;
        }

        .tooltip-icon {
            margin-left: 5px;
            color: #2185d0;
        }

        .tooltip-text {
            visibility: hidden;
            width: 300px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-weight: normal;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .tooltip-header:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }
    </style>
</head>

<body>
    <?php include '../menu.php'; ?>

    <div class="ui container" style="padding: 20px;">
        <h2 class="ui header">
            <i class="chart line icon"></i>
            <div class="content">
                Predicción de Ventas
                <div class="sub header">Análisis y predicción basada en datos históricos</div>
            </div>
        </h2>

        <div class="ui grid">
            <div class="sixteen wide column">
                <table class="ui celled table">
                    <thead>
                        <tr>
                            <th class="producto-column" rowspan="2">Producto</th>
                            <th colspan="3" class="center aligned">Ventas por Mes</th>
                            <th class="prediccion-column" rowspan="2">Predicción Próximo Mes</th>
                            <th class="fiabilidad-column tooltip-header" rowspan="2">
                                Fiabilidad del Pronóstico
                                <i class="info circle icon tooltip-icon"></i>
                                <span class="tooltip-text">
                                    Porcentaje que indica qué tan confiable es la predicción basándose en la estabilidad
                                    de las ventas históricas. Un valor más alto indica ventas más estables y una predicción más confiable.
                                </span>
                            </th>
                            <th class="estado-column" rowspan="2">Estado</th>
                        </tr>
                        <tr>
                            <?php
                            $fecha_max = new DateTime($predictor->obtenerFechaMasReciente());
                            $fecha_inicio = clone $fecha_max;
                            $fecha_inicio->modify('-2 months'); // Retroceder al primer mes

                            for ($i = 0; $i < 3; $i++) {
                                echo "<th class='mes-column'>" . $fecha_inicio->format('M Y') . "</th>";
                                $fecha_inicio->modify('+1 month');
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_fecha = "SELECT MAX(nv_fecha) as fecha_max FROM nota_venta WHERE nv_estado = 'TER'";
                        $stmt_fecha = $conn->prepare($sql_fecha);
                        $stmt_fecha->execute();
                        $fecha_max = $stmt_fecha->fetch(PDO::FETCH_ASSOC)['fecha_max'];

                        $sql = "SELECT DISTINCT p.pro_codigo, p.pro_nombre 
                               FROM producto p
                               INNER JOIN detalle_nota_venta d ON p.pro_codigo = d.ndet_producto
                               INNER JOIN nota_venta n ON d.ndet_nota_venta = n.nv_folio
                               WHERE n.nv_estado = 'TER'
                               AND n.nv_fecha >= DATE_SUB(?, INTERVAL 3 MONTH)
                               ORDER BY p.pro_nombre";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$fecha_max]);
                        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($productos as $producto) {
                            $prediccion = $predictor->predecirProximoMes($producto['pro_codigo']);
                            $ventas_mensuales = $predictor->obtenerVentasUltimosMeses($producto['pro_codigo']);

                            if (!empty($ventas_mensuales)) {
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['pro_nombre']); ?></td>
                                    <?php foreach ($ventas_mensuales as $venta) { ?>
                                        <td class="numero">
                                            <?php echo number_format($venta['cantidad_total'], 0); ?>
                                        </td>
                                    <?php } ?>
                                    <td class="numero"><?php echo $prediccion['prediccion']; ?></td>
                                    <td>
                                        <div class="ui <?php echo $prediccion['confianza'] > 70 ? 'green' : ($prediccion['confianza'] > 40 ? 'yellow' : 'red'); ?> progress" data-percent="<?php echo $prediccion['confianza']; ?>">
                                            <div class="bar" style="width: <?php echo max($prediccion['confianza'], 1); ?>%"></div>
                                            <div class="progress-text"><?php echo $prediccion['confianza']; ?>%</div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($prediccion['confianza'] > 70): ?>
                                            <div class="ui green label">Alta Confiabilidad</div>
                                        <?php elseif ($prediccion['confianza'] > 40): ?>
                                            <div class="ui yellow label">Confiabilidad Media</div>
                                        <?php else: ?>
                                            <div class="ui red label">Baja Confiabilidad</div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
</body>

</html>
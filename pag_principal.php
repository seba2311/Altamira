<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$nombreUsuario = htmlspecialchars($_SESSION['usuario']);

require_once "graficos.php";
require_once "grafico_ventas.php";

$topProducts = obtenerTopProductosStock(10);
$productosBajos = obtenerProductosBajos(10);

// Obtener datos para el nuevo gráfico de ventas
$fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d', strtotime('-1 month'));
$fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$productosMasVendidos = obtenerProductosMasVendidos($fechaInicio, $fechaFin, 10);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo: RFID</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        body {
            background-color: #f8f8f8;
        }

        .ui.menu {
            margin-bottom: 0;
            border-radius: 0;
        }

        .ui.menu .logo-container {
            padding: 0.5em 1em;
        }

        .ui.menu .logo-container img {
            max-height: 40px;
            width: auto;
        }

        .datetime-container {
            background-color: #f0f0f0;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .datetime {
            max-width: 1127px;
            margin: 0 auto;
            padding-left: 10px;
        }

        .main.container {
            max-width: 1127px !important;
            width: 100%;
            margin-top: 2em;
        }

        .ui.dropdown .menu {
            border-radius: 0;
        }

        .ui.dropdown .menu>.item {
            padding: 0.78571429em 1.14285714em !important;
        }

        #stockChartLow {
            width: 100%;
            height: 100%;
        }

        .chart-container {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Eliminar height: 400px; o modificarlo */
            height: auto;
            /* Permitir que se ajuste al contenido */
            min-height: 400px;
            /* Establecer una altura mínima */
            position: relative;
            margin-bottom: 20px;
        }

        .chart-container.chart-fixed-height {
            height: 400px;
        }

        .ui.header {
            margin-bottom: 15px;
            color: #333;
        }

        .main.container {
            margin-top: 2em;
            margin-bottom: 2em;
            margin-left: auto;
            margin-right: auto;
        }

        @media (max-width: 768px) {
            #stockChartLow {
                height: 300px;
            }
        }

        #ventasChart {
            width: 100%;
            height: 100%;
        }

        .date-range-form {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <div class="datetime-container">
        <div class="datetime">
            <span id="current-datetime"></span>
        </div>
    </div>

    <div class="main container">
        <div class="ui two column stackable grid">
            <!-- Listado de Mayor Stock -->
            <div class="column">
                <div class="chart-container">
                    <?php echo generarListaTopProductos($topProducts); ?>
                </div>
            </div>
            <!-- Gráfico de Menor Stock -->
            <div class="column">
                <div class="chart-container">
                    <h2 class="ui header">Top 10 Productos con Menor Stock</h2>
                    <canvas id="stockChartLow"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Ventas -->
        <div class="ui one column stackable grid" id="grafico_venta">
            <div class="column">
                <div class="chart-container">
                    <h2 class="ui header">Top 10 Productos Más Vendidos por rangos de fecha</h2>
                    <form class="ui form date-range-form" method="GET">
                        <div class="two fields">
                            <div class="field">
                                <label>Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" value="<?php echo $fechaInicio; ?>">
                            </div>
                            <div class="field">
                                <label>Fecha Fin</label>
                                <input type="date" name="fecha_fin" value="<?php echo $fechaFin; ?>">
                            </div>
                        </div>
                        <button class="ui button blue" type="submit">Actualizar</button>
                    </form>
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateDateTime() {
            const now = new Date();
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            $('#current-datetime').text(now.toLocaleDateString('es-ES', options).replace(',', '|'));
        }

        $(document).ready(function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);

            Chart.register(ChartDataLabels);
            $('.ui.dropdown').dropdown();

            function initChart(canvasId, chartConfig) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;

                var ctx = canvas.getContext('2d');
                if (chartConfig.options && chartConfig.options.plugins && chartConfig.options.plugins.tooltip) {
                    for (var callbackName in chartConfig.options.plugins.tooltip.callbacks) {
                        chartConfig.options.plugins.tooltip.callbacks[callbackName] =
                            eval('(' + chartConfig.options.plugins.tooltip.callbacks[callbackName] + ')');
                    }
                }
                try {
                    new Chart(ctx, chartConfig);
                } catch (error) {
                    console.error('Error al crear el gráfico ' + canvasId + ':', error);
                    $('#' + canvasId).parent().append('<p class="ui red message">Error al cargar el gráfico. Por favor, intente nuevamente.</p>');
                }
            }

            // Inicializar solo los gráficos que existen
            initChart('stockChartLow', <?php echo generarGraficoProductosBajos('stockChartLow', $productosBajos); ?>);
            initChart('ventasChart', <?php echo generarGraficoProductosMasVendidos('ventasChart', $productosMasVendidos); ?>);
        });
    </script>
</body>

</html>
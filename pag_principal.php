<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$nombreUsuario = htmlspecialchars($_SESSION['usuario']);

require_once "graficos.php";
require_once "grafico_ventas.php"; // Asegúrate de que este archivo existe y contiene las funciones necesarias

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

        /* Estilos adicionales para el menú desplegable */
        .ui.dropdown .menu {
            border-radius: 0;
        }

        .ui.dropdown .menu>.item {
            padding: 0.78571429em 1.14285714em !important;
        }

        /*css del grafico */
        #stockChartHigh, #stockChartLow {
        width: 100%;
        height: 100%; /* Ocupará todo el alto del contenedor */
    }
    .chart-container {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 400px;
            position: relative;
            margin-bottom: 20px;
        }

        .ui.header {
            margin-bottom: 15px;
            color: #333;
        }

        /* Ajuste para el contenedor principal de los gráficos */
        .main.container {
            margin-top: 2em;
            margin-bottom: 2em;
            margin-left: auto;
            margin-right: auto;
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {

            #stockChartHigh,
            #stockChartLow {
                height: 300px;
                /* Altura reducida para dispositivos móviles */
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
            <div class="column">
                <div class="chart-container">
                    <h2 class="ui header">Top 10 Productos con Mayor Stock</h2>
                    <canvas id="stockChartHigh"></canvas>
                </div>
            </div>
            <div class="column">
                <div class="chart-container">
                    <h2 class="ui header">Top 10 Productos con Menor Stock</h2>
                    <canvas id="stockChartLow"></canvas>
                </div>
            </div>
        </div>
        
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
                year: 'numeric', month: 'long', day: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
            };
            $('#current-datetime').text(now.toLocaleDateString('es-ES', options).replace(',', '|'));
        }

        $(document).ready(function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);

            $('.ui.dropdown').dropdown();

            // Función para inicializar un gráfico
            function initChart(canvasId, chartConfig) {
                var ctx = document.getElementById(canvasId).getContext('2d');
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

            // Inicializar gráficos
            initChart('stockChartHigh', <?php echo generarGraficoTopProductos('stockChartHigh', $topProducts); ?>);
            initChart('stockChartLow', <?php echo generarGraficoProductosBajos('stockChartLow', $productosBajos); ?>);
            initChart('ventasChart', <?php echo generarGraficoProductosMasVendidos('ventasChart', $productosMasVendidos); ?>);
        });
    </script>
</body>
</html>